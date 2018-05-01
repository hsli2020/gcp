<?php

namespace App\Controllers;

class DashboardController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'GCP Dashboard';
        $this->view->data = $this->snapshotService->load();
    }
}
