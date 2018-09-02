<?php

namespace App\Controllers;

class DashboardController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'GCP Dashboard';
        $this->view->data = $this->snapshotService->load();
    }

    public function chartAction($type = "")
    {
        if ($type == "generators") {
            $this->view->pageTitle = 'Numbers of Running Generators';
           #$this->view->data = $this->snapshotService->load();
        }

        if ($type == "power") {
            $this->view->pageTitle = 'Total Generator Power';
           #$this->view->data = $this->snapshotService->load();
        }
    }
}
