<?php

namespace App\Controllers;

use App\System\WebRelayQuad;

const DEBUG = 0;

class TangentController extends ControllerBase
{
    public function indexAction($projectId = '')
    {
        $project = $this->projectService->get($projectId);
        $webRelay = $this->projectService->getWebRelayInfo($projectId);

        // "http://74.198.22.2:9001/state.xml" => "74.198.22.2"
        $webRelay['primary_ip'] = parse_url($webRelay['primary_ip'], PHP_URL_HOST);
        $webRelay['backup_ip'] =  parse_url($webRelay['backup_ip'],  PHP_URL_HOST);

        $this->view->projectId = $projectId;
        $this->view->project   = $project;
        $this->view->webRelay  = $webRelay;
        $this->view->pageTitle = 'Tangent - '. $project->siteName;
    }

    public function getStateAction($projectId = '')
    {
        if (DEBUG) {
            $state = [
                'relay1state' => 0,
                'relay2state' => 0,
                'relay3state' => 0,
                'relay4state' => 0,
            ];
        } else {
            $ips = $this->projectService->getWebRelayInfo($projectId);
            $webRelay = new WebRelayQuad($ips);
            $state = $webRelay->getState();
        }

        return $this->json('OK', $state);
    }

    public function turnOnAction($projectId = '')
    {
        if (DEBUG) {
            $state = [
                'relay1state' => 1,
                'relay2state' => 0,
                'relay3state' => 0,
                'relay4state' => 0,
            ];
        } else {
            $ips = $this->projectService->getWebRelayInfo($projectId);
            $webRelay = new WebRelayQuad($ips);
            $state = $webRelay->turnOn(1); // relay_1
        }

        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
    }

    public function turnOffAction($projectId = '')
    {
        if (DEBUG) {
            $state = [
                'relay1state' => 0,
                'relay2state' => 0,
                'relay3state' => 0,
                'relay4state' => 0,
            ];
        } else {
            $ips = $this->projectService->getWebRelayInfo($projectId);
            $webRelay = new WebRelayQuad($ips);
            $state = $webRelay->turnOff(1); // relay_1
        }

        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
    }

    public function ipListAction()
    {
        $this->view->pageTitle = 'Tangent - IP List';
        $iplist = $this->projectService->getWebRelayList();
        $this->view->iplist = $iplist;
    }

    // Log the state change of relay
    protected function saveWebRelayLog($projectId, $state)
    {
        if (empty($state)) {
            return;
        }

        $project = $this->projectService->get($projectId);
        $auth = $this->session->get('auth');

        $info = $state;
        $info['user_id'] = $auth['id'];
        $info['user_name'] = $auth['username'];
        $info['user_ip'] = $this->request->getClientAddress();
        $info['project_id'] = $projectId;
        $info['project_name'] = $project->name;
        $this->projectService->saveWebRelayLog($info);
    }
}
