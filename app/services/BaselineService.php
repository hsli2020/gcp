<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class BaselineService extends Injectable
{
    // Project Zones
    public function getProjectZones()
    {
        $sql = "SELECT * FROM project_zone";
        $rows = $this->db->fetchAll($sql);

        $zones = [];
        foreach ($rows as $row) {
            $zone = $row['zone_name'];
            $zones[$zone][] = $row;
        }
        return $zones;
    }

    // Load Baseline history for exporting
    public function getBaseline($zone = '', $startDate = '', $endDate = '')
    {
        $criterias = [];

        if ($zone) {
            $criterias[] = "zone_name='$zone'";
        }

        if ($startDate) {
            $criterias[] = "date>='$startDate'";
        }

        if ($endDate) {
            $criterias[] = "date<='$endDate'";
        }

        $where = '';
        if ($criterias) {
            $where = 'WHERE '. implode(' AND ', $criterias);
        }

        $sql = "SELECT * FROM baseline_history $where";

        $rows = $this->db->fetchAll($sql);

        foreach ($rows as $key => $row) {
            $rows[$key]['baseline']    = json_decode($row['baseline'], true);
            $rows[$key]['actual_load'] = json_decode($row['actual_load'], true);
            $rows[$key]['inday_adj']   = json_decode($row['inday_adj'], true);
        }

        return $rows;
    }

    public function generateBaseline($date)
    {
        $zones = $this->getProjectZones();

        foreach ($zones as $zoneName => $zone) {

            $data = $this->getHourlyLoad($zoneName, $date);
            $bl = $this->calcBaseline($zoneName, $data);

            $data = $this->getOneDayLoad($zoneName, $date);
            $al = $this->calcActualLoad($data);

            $ia = $this->calcIndayAdjustment($bl, $al);

            // Save Baseline History
            try {
                $this->db->execute("DELETE FROM baseline_history WHERE date='$date' AND zone_name='$zoneName'");

                $this->db->insertAsDict('baseline_history', [
                    'date'        => $date,
                    'zone_name'   => $zoneName,
                    'baseline'    => json_encode($bl, JSON_FORCE_OBJECT),
                    'actual_load' => json_encode($al, JSON_FORCE_OBJECT),
                    'inday_adj'   => json_encode($ia, JSON_FORCE_OBJECT),
                ]);
            } catch (\Exception $e) {
                //echo $e->getMessage(), "\n";
            }
        }
    }

    public function calcBaseline($zoneName, $data)
    {
        /**
         * $data = [
         *   '2022-02-23' => [
         *       [ 'project' => 1, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *       [ 'project' => 2, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *       [ 'project' => 3, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *   ],
         *   '2022-02-22' => [
         *       [ 'project' => 1, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *       [ 'project' => 2, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *       [ 'project' => 3, 'load' => [ 1 => NNN, 2 => NNN, ... 23 => NNN ] ],
         *   ],
         *   ...
         * ];
         */

       #$days = 0;
        $hourly = [];

        foreach ($data as $dt => $projects) {
           #if ($this->isDateExcluded($dt, $zoneName)) {
           #    continue;
           #}

            foreach (range(0, 23) as $hour) {
                $hourSum = 0;
                foreach ($projects as $project) {
                    if (isset($project['load'][$hour])) {
                        $hourSum += $project['load'][$hour];
                    }
                }
               #if ($hourSum > 0) {
                    $hourly[$hour][] = $hourSum;
               #}
            }

           #if (++$days == 20) {
           #    break;
           #}
        }

        $baseline = [];
        foreach (range(0, 23) as $hour) {
            rsort($hourly[$hour]);
            $hourly[$hour] = array_slice($hourly[$hour], 0, 15);
            $baseline[$hour] = round(array_sum($hourly[$hour]) / count($hourly[$hour]));
        }

        return $baseline;
    }

    public function calcActualLoad($data)
    {
        $hourly = [];

        foreach ($data as $project) {
            foreach (range(0, 23) as $hour) {
                if (isset($project['load'][$hour])) {
                    $hourly[$hour][] = $project['load'][$hour];
                }
            }
        }

        $acload = [];
        foreach (range(0, 23) as $hour) {
            if (!isset($hourly[$hour])) {
                $hourly[$hour] = [];
            }
            $acload[$hour] = array_sum($hourly[$hour]);
        }

        return $acload;
    }

    public function calcIndayAdjustment($baseline, $actualLoad)
    {
        $adj = [];

        foreach (range(0, 23) as $hour) {
            if ($hour < 12) {
                $adj[$hour] = '';
                continue;
            }

            $bl = [ $baseline[$hour-4], $baseline[$hour-3], $baseline[$hour-2] ];
            $al = [ $actualLoad[$hour-4], $actualLoad[$hour-3], $actualLoad[$hour-2] ];

            $blAvg = array_sum($bl) / count($bl);
            $alAvg = array_sum($al) / count($al);

            $adj[$hour] = round($alAvg/$blAvg, 2);
        }

        return $adj;
    }

    public function getHourlyLoad($zone, $date) // $date not used
    {
        $sql = "SELECT * FROM hourly_load
                 WHERE zone_name='$zone'
              ORDER BY `date` DESC";

        $data = $this->db->fetchAll($sql);

        // Re-index by date
        $result = [];
        foreach ($data as $rec) {
            $rec['load'] = json_decode($rec['load'], 1);

            $dt = $rec['date'];
            $result[$dt][] = $rec;
        }

        return $result;
    }

    public function getOneDayLoad($zone, $date)
    {
        $sql = "SELECT * FROM hourly_load WHERE `date`='$date' AND zone_name='$zone'";
        $data = $this->db->fetchAll($sql);

        foreach ($data as $key => $rec) {
            $data[$key]['load'] = json_decode($rec['load'], 1);
        }

        return $data;
    }

    public function generateHourlyLoad($date)
    {
        $sql = "SELECT * FROM project_zone";
        $projects = $this->db->fetchAll($sql);

        foreach ($projects as $project) {
            $projectId = $project['project_id'];
            $zoneName  = $project['zone_name'];

            $validDates = $this->getValidDates($date, $zoneName);

            foreach ($validDates as $validDate) {
                $data = $this->calcHourlyLoad($project, $validDate);

                // Save Actual Load
                $this->db->execute("DELETE FROM hourly_load WHERE date='$validDate' AND project_id=$projectId");

                $this->db->insertAsDict('hourly_load', [
                    'date'       => $validDate,
                    'project_id' => $projectId,
                    'zone_name'  => $project['zone_name'],
                    'load'       => json_encode($data, JSON_FORCE_OBJECT),
                    'excluded'   => 0, // $this->isDateExcluded($validDate, $zoneName),
                ]);
            }
        }
    }

    public function calcHourlyLoad($project, $date)
    {
        $fieldName = $project['field_name'];
        $tableName = $project['table_name'];

        // convert time from EST/local to UTC/db
        $start = $this->db->fetchColumn("SELECT CONVERT_TZ('$date 00:00:00', 'EST', 'UTC')");
        $end   = $this->db->fetchColumn("SELECT CONVERT_TZ('$date 23:59:59', 'EST', 'UTC')");

        $sql = "SELECT time_utc,
                   --  CONVERT_TZ(time_utc, 'UTC', 'America/Toronto') AS time_edt,
                       CONVERT_TZ(time_utc, 'UTC', 'EST') AS time_est,
                       $fieldName AS `load`
                  FROM $tableName
                 WHERE time_utc>='$start' AND time_utc<'$end'";
        $rows = $this->db->fetchAll($sql);

        $hourly = [];
        foreach ($rows as $row) {
            $time = $row['time_est'];
           #$dt = substr($time, 0, 10);
            $hr = intval(substr($time, 11, 2));

            if (isset($hourly[$hr])) {
                $hourly[$hr]['sum'] += abs($row['load']);
                $hourly[$hr]['cnt'] += 1;
            } else {
                $hourly[$hr]['sum'] = abs($row['load']);
                $hourly[$hr]['cnt'] = 1;
            }
        }

        $result = [];
        foreach ($hourly as $hour => $rec) {
           #$h = sprintf("%02d:00", intval($hour));
            $result[$hour] = intval($rec['sum']/$rec['cnt']);
        }

        return $result;
    }

    public function getValidDates($startDate, $zone)
    {
        // Step 1: Collect 35 business days
        $bizDates = [];

        $date = strtotime($startDate);
        while (true) {
            if ($this->isWeekend($date)) {
                $date -= 24*60*60;
                continue;
            }

            $bizDates[] = date('Y-m-d', $date);

            if (count($bizDates) >= 35) {
                break;
            }

            $date -= 24*60*60;
        }

        // Step 2: Get rid of holidays and excluded dates
        $validDates = [];

        foreach ($bizDates as $date) {
            if ($this->isHoliday($date) || $this->isDateExcluded($date, $zone)) {
                continue;
            }

            $validDates[] = $date;

            if (count($validDates) >= 20) {
                break;
            }
        }

        return $validDates;
    }

    public function isDateExcluded($date, $zone)
    {
        $row = $this->db->fetchOne("SELECT id FROM date_excluded WHERE date='$date' AND zone='$zone'");
        return (bool)$row;
    }

    public function setDateExcluded($params)
    {
        $date = $params['date'];
        $note = $params['note'];
        $zone = $params['zone'];
        $user = $params['user'];

        try {
            $this->db->insertAsDict('date_excluded', [
                'date' => $date,
                'note' => $note,
                'zone' => $zone,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function loadExcludedDateList($zone = '')
    {
        if (!$zone) {
            return [];
        }

        $sql = "SELECT * FROM date_excluded WHERE zone='$zone' ORDER BY zone, `date` DESC";
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }

    public function loadZoneNameList()
    {
        $sql = "SELECT DISTINCT(zone_name) zone FROM project_zone";
        $rows = $this->db->fetchAll($sql);
        return array_column($rows, 'zone');
    }

    public function loadPublicHolidays()
    {
        $sql = "SELECT * FROM holidays ORDER BY id DESC LIMIT 100";
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }

    public function savePublicHoliday($params)
    {
        $date = $params['date'];
        $note = $params['note'];
        $user = $params['user'];

        try {
            $this->db->insertAsDict('holidays', [
                'date' => $date,
                'note' => $note,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
        }
    }

    public function isHoliday($date)
    {
        $sql = "SELECT id FROM holidays WHERE date='$date'";
        $row = $this->db->fetchOne($sql);
        return (bool)$row;
    }

    public function isWeekend($date)
    {
        #return date('N', strtotime($date)) >= 6;
        return date('N', $date) >= 6;
    }

    public function export($params)
    {
        $startDate = isset($params['start-time']) ? $params['start-time'] : date('Y-m-d', strtotime('-1 days'));
        $endDate   = isset($params['end-time'])   ? $params['end-time']   : date('Y-m-d');

        $START_HR = 8;
        $END_HR = 22;

        // Create CSV File
        $filename = BASE_DIR . '/tmp/export-baseline-'. date('Ymd-His'). '.csv';
        $fp = fopen($filename, "wb");

        // CSV Title
        $columns = ['Time(EST)', 'Standard Baseline', 'Actual-Load', 'In-Day Adjustment'];
        fputcsv($fp, $columns);
        foreach (range($START_HR, $END_HR) as $hr) {
            $columns[] = "$hr:00";
        }

        // All Zones
        $baseline = $this->getBaseline('', $startDate, $endDate);

        // CSV Data
        foreach ($baseline as $b) {
            fputcsv($fp, [ $b['zone_name'] ]);

            foreach (range($START_HR, $END_HR) as $hr) {
                $data = [];
                $data[] = $b['date']. " $hr:00";
                $data[] = $b['baseline'][$hr];
                $data[] = $b['actual_load'][$hr];
                $data[] = $b['inday_adj'][$hr];
                fputcsv($fp, $data);
            }

            fputs($fp, "\n");
        }

        fclose($fp);

        return $filename;
    }

    public function exportToday()
    {
        $today = date('Y-m-d');

        $this->generateHourlyLoad($today);
        $this->generateBaseline($today);

        $params = [
            'start-time' => $today,
            'end-time'   => $today,
        ];

        return $this->export($params);
    }
}
