<?php

namespace App\Controllers;

class ProjectController extends ControllerBase
{
    public function indexAction()
    {
        #return $this->dispatcher->forward([
        #    'controller' => 'report',
        #    'action' => 'daily'
        #]);
    }

    public function detailAction($id = 0)
    {
        $this->view->pageTitle = 'Project Details';
        $this->view->now = date('g:i a');
        $this->view->refreshInterval = 60;

        try {
            $info = $this->projectService->getDetails($id);
            $this->view->project = $info['project'];
            $this->view->data = $info['details'];
            $this->view->alarms = $info['alarms'];
        } catch (\Exception $e) {
           #$this->response->redirect('/error/404');
            $this->dispatcher->forward([
                'controller' => 'error',
                'action'     => 'error404'
            ]);
        }
    }

    public function tangentAction($id = 0)
    {
        $this->view->pageTitle = 'Project Details';
        $this->view->now = date('g:i a');
        $this->view->refreshInterval = 60;

        try {
            $info = $this->projectService->getDetails($id);
            $this->view->project = $info['project'];
            $this->view->data = $info['details'];
            $this->view->alarms = $info['alarms'];
        } catch (\Exception $e) {
           #$this->response->redirect('/error/404');
            $this->dispatcher->forward([
                'controller' => 'error',
                'action'     => 'error404'
            ]);
        }
    }

    public function ajaxTeslaAction()
    {
        $this->view->pageTitle = 'Project Details';
        $data = $this->projectService->getAjaxTeslaData();
        $this->view->data = $data;
    }

    public function exportAction()
    {
        $this->view->pageTitle = 'Data Exporting';

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $filename = $this->exportService->export($params);
            $this->startDownload($filename);
        }

        $projects = $this->projectService->getAll();
        $this->view->projects = $projects;
    }
}
