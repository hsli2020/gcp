<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ImportService extends Injectable
{
    public function import()
    {
        $this->log('Start importing');

        $projects = $this->projectService->getAll();

        $fileCount = 0;
        foreach ($projects as $project) {
            echo $project->siteName, EOL;

            $dir = 'C:/GCP-FTP-ROOT/'.$project->ftpdir;

            foreach (glob($dir . '/*.csv') as $filename) {
                echo "\t", $filename, EOL;

                // wait until the file is completely uploaded
                while (time() - filemtime($filename) < 10) {
                    sleep(1);
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

    public function getUreaLevel()
    {
        $rows = $this->db->fetchAll("SELECT * FROM urea");
        $rows = array_column($rows, 'tag', 'project_id');

        foreach ($rows as $projectId => $tag) {
            $url = "https://safetypower.net/api/1.0/stations/$tag/tags";
            $res = $this->httpGet($url);

            if ($res) {
                $data = addslashes($res);
                $sql = "UPDATE urea SET data='$data' WHERE project_id=$projectId";
                $this->db->execute($sql);

                echo "UREA Level: Project $projectId $tag\n";
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
