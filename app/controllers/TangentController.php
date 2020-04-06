<?php

namespace App\Controllers;

use App\System\WebRelayQuad;

class TangentController extends ControllerBase
{
    public function indexAction($projectId = '')
    {
        $this->view->projectId = $projectId;
        $this->view->pageTitle = 'Tangent';
    }

    public function getStateAction($projectId = '')
    {
        $ips = $this->projectService->getWebRelayInfo($projectId);

        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->getState();
        return $this->json('OK', $state);
    }

    public function turnOnAction($projectId = '')
    {
        $ips = $this->projectService->getWebRelayInfo($projectId);

        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->turnOn(1); // relay_1
        /**
         * $state = [
         *    'relay1state' => 0,
         *    'relay2state' => 0,
         *    'relay3state' => 0,
         *    'relay4state' => 0,
         * ];
         */
        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
    }

    public function turnOffAction($projectId = '')
    {
        $ips = $this->projectService->getWebRelayInfo($projectId);

        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->turnOff(1); // relay_1
        /**
         * $state = [
         *    'relay1state' => 0,
         *    'relay2state' => 0,
         *    'relay3state' => 0,
         *    'relay4state' => 0,
         * ];
         */
        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
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
