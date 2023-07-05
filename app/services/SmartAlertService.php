<?php

namespace App\Service;

use App\System\WebRelayQuad;
use Phalcon\Di\Injectable;

class SmartAlertService extends Injectable
{
    protected $alerts;

    public function run()
    {
        echo "Smart Alert is running ...", EOL;

        $this->alerts = [];

        $this->checkStatusChanged();
        $this->checkDataError();
        $this->checkRemoteStatus();

        if ($this->alerts) {
            $this->saveAlerts();
            $this->sendAlerts();
        }
    }

    protected function checkStatusChanged()
    {
        $alertType = 'STATUS-CHANGED';

        $rows = $this->db->fetchAll("SELECT * FROM status_change WHERE checked=0");

        foreach ($rows as $row) {
            //if ($row['checked']) { continue; }

            $projectId = $row['project_id'];

            $row['time_old_utc'] = $row['time_old'];
            $row['time_new_utc'] = $row['time_new'];

            // UTC to LocalTime
            $row['time_old'] = changeTimezone($row['time_old'], 'UTC', 'EST');
            $row['time_new'] = changeTimezone($row['time_new'], 'UTC', 'EST');

            $project = $this->projectService->get($projectId);
            $projectName = $project->name;

            // check Generator Power
            $genPowerOld = abs($row['gen_power_old']);
            $genPowerNew = abs($row['gen_power_new']);

            $delta = abs($genPowerNew - $genPowerOld);
            $threshold = max(max($genPowerNew, $genPowerOld)/2, 100);

            if ($delta > $threshold) {
                $subject = "GCP Alert: $projectName - Generator Power Changed";
                $this->log($subject);
                $this->log(print_r($row, true));
                $this->alerts[$projectId][] = $alert = [
                    'type'    => $alertType,
                    'subject' => $subject,
                    'project' => $project,
                    'data'    => $row,
                ];
                //fpr($alert);
            }

            // check Store Load
            $storeLoadOld = abs($row['store_load_old']);
            $storeLoadNew = abs($row['store_load_new']);

            $delta = abs($storeLoadNew - $storeLoadOld);
            $threshold = max($storeLoadNew, $storeLoadOld, 100)/2;

            if ($delta > $threshold) {
                $subject = "GCP Alert: $projectName - Store Load Changed";
                $this->log($subject);
                $this->log(print_r($row, true));
                $this->alerts[$projectId][] = $alert = [
                    'type'    => $alertType,
                    'subject' => $subject,
                    'project' => $project,
                    'data'    => $row,
                ];
                //fpr($alert);
            }
        }

        $this->db->execute("UPDATE status_change SET checked=1");
    }

    protected function checkDataError()
    {
        $alertType = 'DATA-ERROR';

        $rows = $this->db->fetchAll("SELECT * FROM latest");
        foreach ($rows as $row) {
            $projectId = $row['project_id'];
            if ($this->dataErrorAlertTriggered($projectId)) {
                continue;
            }

            $data = json_decode($row['data'], true);
            $error = $data['error'];

            if ($error != 0) {
                $project = $this->projectService->get($projectId);
                $projectName = $project->name;

                $subject = "GCP Alert: $projectName - Data Error";
                $this->alerts[$projectId][] = [
                    'type'    => $alertType,
                    'subject' => $subject,
                    'project' => $project,
                    'data'    => $data,
                ];

                $this->db->insertAsDict('data_error_log', [
                    'project_id' => $projectId,
                    'error'      => $error,
                ]);
            }
        }
    }

    public function checkRemoteStatus()
    {
        if ((date('i')%10) != 0) {
            return; // every 10 minutes
        }

        $list = $this->projectService->getWebRelayList();
        foreach ($list as $info) {
            if ($info['active'] == 0) {
                continue;
            }
            $siteName = $info['site_name'];
            $primaryIP = parse_url($info['primary_ip'], PHP_URL_HOST);
            $backupIP = parse_url($info['backup_ip'], PHP_URL_HOST);

            echo $siteName;

            $webRelay = new WebRelayQuad($info);
            $state = $webRelay->getState();

            if (!empty($state)) {
                echo "\tOK\n";
                continue;
            }

            echo "\tUnreachable\n";

            $recepient = "wsong365@gmail.com";
            $subject = "GCP Alert: Unreachable Remote Status";
            $body  = "<h2>Please check what is wrong!</h2>\n";
            $body .= "<b>Name:</b> $siteName<br>\n";
            $body .= "<b>Primary IP:</b> $primaryIP<br>\n";
            $body .= "<b>Backup IP:</b> $backupIP<br>";

            $this->sendEmail($recepient, $subject, $body);
        }
    }

    protected function dataErrorAlertTriggered($projectId)
    {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM data_error_log WHERE project_id=$projectId AND DATE(createdon)='$today'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    protected function generateHtml($alerts)
    {
        ob_start();
        include(BASE_DIR . "/job/templates/status-change.tpl");
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    protected function saveAlerts()
    {
        /*
        foreach ($this->alerts as $projectId => $alerts) {
            foreach ($alerts as $alert) {
                try {
                    $this->db->insertAsDict('smart_alert_log', [
                        'time'         => $alert['time'],
                        'project_id'   => $alert['project_id'],
                        'alert'        => $alert['alert'],
                        'message'      => $alert['message'],
                    ]);
                } catch (\Exception $e) {
                    echo $e->getMessage(), EOL;
                }
            }
        }
        */
    }

    protected function sendAlerts()
    {
        $users = $this->userService->getAll();

        foreach ($users as $user) {
           #if ($user['id'] > 1) break;

            if (strpos($user['email'], '@') === false) {
                continue;
            }

            foreach ($this->alerts as $projectId => $alerts) {
                $html = $this->generateHtml($alerts);
                $subject = $this->getSubject($alerts);
                $this->sendEmail($user['email'], $subject, $html);
            }
        }
    }

    protected function getSubject($alerts)
    {
        $alert = $alerts[0];
        return $alert['subject'];
    }

    protected function sendEmail($recepient, $subject, $body)
    {
        if (!$this->emailService->send($recepient, $subject, $body)) {
            $this->log("Mailer Error: " . $this->emailService->getErrorInfo());
        } else {
            $this->log("Smart Alert sent to $recepient.");
        }
    }

    protected function log($str)
    {
#       return;
        $filename = BASE_DIR . '/app/logs/alert.log';
        $str = date('Y-m-d H:i:s ') . $str . "\n";

        echo $str;
        error_log($str, 3, $filename);
    }
}
