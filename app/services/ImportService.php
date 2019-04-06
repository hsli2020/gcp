<?php

namespace App\Service;

use Phalcon\Di\Injectable;
use phpseclib\Net\SFTP;

class ImportService extends Injectable
{
    public function import()
    {
        $this->log('Start importing');

        $projects = $this->projectService->getAll();

        $fileCount = 0;
        foreach ($projects as $project) {
            echo $project->siteName, EOL;

            $path = pathinfo($project->ftpdir, PATHINFO_DIRNAME);
            if (strlen($path) == 1 || $path[1] != ':') { // relative path
                $dir = 'C:/GCP-FTP-ROOT/'.$project->ftpdir;
            } else { // absolute path
                $dir = $project->ftpdir;
            }

            foreach (glob($dir . '/*.csv') as $filename) {
                echo "\t", $filename, EOL;

                // check if the file is completely uploaded
                if (time() - filemtime($filename) < 10) {
                    continue; // if not, skip it, import next time
                }

                $fileCount++;

                $this->importFile($filename, $project);
                $this->backupFile($filename, $dir);
            }
        }

        $this->log("Importing completed, $fileCount file(s) imported.\n");
    }

    protected function backupFile($filename, $ftpdir)
    {
        // move file to BACKUP folder, even it's not imported
        $dir = $ftpdir . '/archive/' . date('Y/m/d', filemtime($filename));
        if (!file_exists($dir) && !is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $newfile = $dir . '/' . basename($filename);
        rename($filename, $newfile);
    }

    protected function importFile($filename, $project)
    {
        // filename: c:\GCP-FTP-ROOT\GCP_Glen_Erin_001EC605493D\mb-001.5AE11A04_1.log.csv

        $parts = explode('.', basename($filename));
        $dev  = $parts[0]; // mb-001
        $hash = $parts[1]; // 5AE11A04_1

        if (!isset($project->devices[$dev])) {
           #$this->log("Invalid Filename: $filename");
            return;
        }

        $device  = $project->devices[$dev];
        $columns = $device->getTableColumns();

        if (($handle = fopen($filename, "r")) !== FALSE) {
            $latest = [];

            fgetcsv($handle); // skip first line
            while (($fields = fgetcsv($handle)) !== FALSE) {
                $fields = array_slice($fields, 0, count($columns));
                if (count($columns) != count($fields)) {
                    $this->log("DATA ERROR: $filename\n\t" . implode(', ', $fields));
                    continue;
                };

                $data = array_combine($columns, $fields);

                $this->insertIntoDatabase($project, $device, $data);

                $latest = $data;
            }
            fclose($handle);

            $this->saveLatestData($project, $device, $latest);
            $this->generateAlarm($project, $device, $latest);
        }
    }

    protected function insertIntoDatabase($project, $device, $data)
    {
        // insert into devtab
        $devtab = $device->getTable();

        $columnList = '`' . implode('`, `', array_keys($data)) . '`';
        $values = "'" . implode("', '", $data). "'";

        $sql = "INSERT INTO $devtab ($columnList) VALUES ($values)";

        try {
            $this->db->execute($sql);
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function saveLatestData($project, $device, $data)
    {
        if (empty($data)) {
            return;
        }

        $id = $project->id;
        $name = addslashes($project->siteName);
        $time = $data['time_utc'];
        $devtype = $device->type;
        $devcode = $device->code;
        $json = addslashes(json_encode($data));

        $sql = "REPLACE INTO latest SET"
             . " project_id = $id,"
             . " project_name = '$name',"
             . " time = '$time',"
             . " devtype = '$devtype',"
             . " devcode = '$devcode',"
             . " data = '$json'";

        $this->db->execute($sql);
    }

    public function generateAlarm($project, $device, $data)
    {
        if (empty($this->modbus)) {
            $sql = "SELECT * FROM modbus";
            $rows = $this->db->fetchAll($sql);
            $this->modbus = array_column($rows, null, 'tag_name');
        }

        foreach ($this->modbus as $tagname => $info) {
            $normval = $info['normval'];
            if ($normval == 'NA') {
                continue;
            }

            $description = $info['description'];
            if ($description == '') {
                $description = $tagname;
            }

            if (isset($data[$tagname])) {
                $tagval = $data[$tagname];

                // find last Not-Closed alarm
                $sql = "SELECT * FROM alarm
                         WHERE project_id={$project->id} AND tagname='$tagname' AND end_time IS NULL
                         ORDER BY id DESC";
                $lastAlarm = $this->db->fetchOne($sql);

                if ($lastAlarm) {
                    if ($lastAlarm['value'] != $tagval) {
                        $this->db->updateAsDict('alarm', [
                            'end_time' => $data['time_utc'],
                        ],
                        'id='.$lastAlarm['id']);
                    }
                } else if ($tagval != $normval) {
                    $this->db->insertAsDict('alarm', [
                        'project_id'  => $project->id,
                        'start_time'  => $data['time_utc'],
                        'end_time'    => null,
                        'devcode'     => $device->code,
                        'tagname'     => $tagname,
                        'value'       => $data[$tagname],
                        'description' => $description,
                    ]);
                }
            }
        }
    }

    public function getSafetyPower()
    {
        $rows = $this->db->fetchAll("SELECT * FROM safety_power");
        $rows = array_column($rows, 'tag', 'project_id');

        foreach ($rows as $projectId => $tag) {
            $url = "https://safetypower.net/api/1.0/stations/$tag/tags";
            $res = $this->httpGet($url);

            if ($res) {
                $data = addslashes($res);
                $sql = "UPDATE safety_power SET data='$data' WHERE project_id=$projectId";
                $this->db->execute($sql);

                echo "Safety Power: Project $projectId $tag\n";
            }
        }
    }

    protected function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $accessToken = "QJbs0soFrj3IukQQNyIAvTi0l7iLNQAtAL";

        $headers = [ "Authorization: Bearer $accessToken" ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output ;
    }

    public function restartFtpServer()
    {
        $projects = $this->projectService->getAll();

        $fileCount = 0;
        foreach ($projects as $project) {
            $path = pathinfo($project->ftpdir, PATHINFO_DIRNAME);
            if (strlen($path) == 1 || $path[1] != ':') { // relative path
                $dir = 'C:/GCP-FTP-ROOT/'.$project->ftpdir;
            } else { // absolute path
                $dir = $project->ftpdir;
            }

            foreach (glob($dir . '/*.csv') as $filename) {
                if (time() - filemtime($filename) > 10*60) {
                    $fileCount++;
                }
            }
        }

        if ($fileCount > 0) {
            exec('net stop "FileZilla Server FTP server"');
            sleep(5);
            exec('net start "FileZilla Server FTP server"');
            $this->log("=> Restart FTP Server\n");
        }
    }

    public function getForecastPeak()
    {
        $today = date('Ymd');

        $user = "gormanm";
        $pass = "Sunshine4ever!";
        $host = "reports.ieso.ca";
        $frmt = "/public/Adequacy2/PUB_Adequacy2_$today.xml";
        $floc = BASE_DIR."/tmp/".basename($frmt);

        if (file_exists($floc) && (time() - filemtime($floc) < 60*30)) {
            return;
        }

        $sftp = new SFTP($host);
        if (!$sftp->login($user, $pass)) {
            return;
        }

        $sftp->get($frmt, $floc);

        $xml = simplexml_load_file($floc);

        $peakMW = 0;
        $peakHour = 0;

        foreach ($xml->DocBody->ForecastDemand->OntarioDemand->ForecastOntDemand->Demand as $demand) {
            if (intval($demand->EnergyMW) > $peakMW) {
                $peakMW = intval($demand->EnergyMW);
                $peakHour = intval($demand->DeliveryHour);
            }
        }

        try {
            $this->db->insertAsDict('forecast_peak', [
                'time_utc'    => strval($xml->DocHeader->CreatedAt),
                'peak_hour'   => $peakHour,
                'peak_energy' => $peakMW,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function getErthmeter()
    {
        $host = "transfers.meterdataservices.com";
        $user = "GRT_Circle";
        $pass = "meCLu%M6";

        $sftp = new SFTP($host);
        if (!$sftp->login($user, $pass)) {
            return;
        }

        // table struct is same as csv file
        $columns = $this->db->fetchAll("DESC erthmeter");
        $columns = array_column($columns, 'Field');

        $remdir = './BackUp/';
        $locdir = BASE_DIR."/tmp/erthmeter/";

        $list = $sftp->nlist($remdir);

        foreach ($list as $file) {
            $remfile = $remdir.$file;
            $locfile = $locdir.$file;

            if (file_exists($locfile)) {
                continue;
            }

            if (!$sftp->get($remfile, $locfile)) {
                continue;
            }

            if (($handle = fopen($locfile, "r")) !== FALSE) {
                fgetcsv($handle); // skip first line
                while (($values = fgetcsv($handle)) !== FALSE) {
                    $values = array_map('trim', $values);
                    $fields = array_combine($columns, $values);

                    try {
                        $this->db->insertAsDict('erthmeter', $fields);
                    } catch (\Exception $e) {
                        // echo $e->getMessage(), EOL;
                    }
                }
                fclose($handle);
            }

            echo $file, EOL;
        }
    }

    protected function log($str)
    {
        $filename = BASE_DIR . '/app/logs/import.log';

        if (file_exists($filename) && filesize($filename) > 512*1024) {
            unlink($filename);
        }

        $str = date('Y-m-d H:i:s ') . $str . "\n";

        echo $str;
        error_log($str, 3, $filename);
    }
}
