<?php

namespace App\Controllers;

use App\Models\Users;
use App\System\WebRelayQuad;

const DEBUG = 1;

class TangentController extends ControllerBase
{
    protected $mockState = [
        'relay1state' => 0,
        'relay2state' => 0,
        'relay3state' => 0,
        'relay4state' => 0,
    ];

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
       #if (!$this->checkAuth()) {
       #    return $this->json('OK', $this->mockState);
       #}

        if (DEBUG) {
            $this->mockState['relay1state'] = $this->getMockState($projectId);
            return $this->json('OK', $this->mockState);
        }

        $ips = $this->projectService->getWebRelayInfo($projectId);
        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->getState();

        return $this->json('OK', $state);
    }

    public function turnOnAction($projectId = '')
    {
        if (!$this->checkAuth()) {
            $this->setMockState($projectId, 1);
            $this->mockState['relay1state'] = 1;
            return $this->json('OK', $this->mockState);
        }

        if (DEBUG) {
            $this->setMockState($projectId, 1);
            $this->mockState['relay1state'] = 1;
            return $this->json('OK', $this->mockState);
        }

        $ips = $this->projectService->getWebRelayInfo($projectId);
        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->turnOn(1); // relay_1

        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
    }

    public function turnOffAction($projectId = '')
    {
        if (!$this->checkAuth()) {
            $this->setMockState($projectId, 0);
            $this->mockState['relay1state'] = 0;
            return $this->json('OK', $this->mockState);
        }

        if (DEBUG) {
            $this->setMockState($projectId, 0);
            $this->mockState['relay1state'] = 0;
            return $this->json('OK', $this->mockState);
        }

        $ips = $this->projectService->getWebRelayInfo($projectId);
        $webRelay = new WebRelayQuad($ips);
        $state = $webRelay->turnOff(1); // relay_1

        $this->saveWebRelayLog($projectId, $state);

        return $this->json('OK', $state);
    }

    public function ipListAction()
    {
        $this->view->pageTitle = 'Tangent - IP List';
       #$iplist = $this->projectService->getWebRelayList();
        $this->view->iplist = []; //$iplist;
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

    public function remoteAction()
    {
        $this->view->pageTitle = 'Remote Start/Stop';

        $projects = $this->projectService->getWebRelayList();

        $this->view->projects = $projects;
        $this->view->ids = implode(',', array_column($projects, 'project_id'));
    }

    public function checkAuthAction()
    {
        $auth = $this->session->get('auth');
        $username = $auth['username'];
        $password = $this->request->getPost('password');
        $auth['authchecked'] = 0;

        $user = Users::findFirstByUsername($username);

        if ($user && $user->active == 'Y' && $this->security->checkHash($password, $user->password)) {
            $auth['authchecked'] = 1;
            return $this->json('OK', 'Authorized');
        }

        $this->session->set('auth', $auth);

        $message = 'Wrong Username/password.';
        return $this->json('ERROR', $message);
    }

    protected function checkAuth()
    {
        $auth = $this->session->get('auth');
        if (empty($auth['authchecked'])) {
            return false;
        }
        return true;
    }

    protected function setMockState($projectId, $state)
    {
        $mockState = $this->session->get('mock');
        $mockState[$projectId] = $state;
        $this->session->set('mock', $mockState);
    }

    protected function getMockState($projectId)
    {
        $mockState = $this->session->get('mock');
        return isset($mockState[$projectId]) ? $mockState[$projectId] : 0;
    }
}
