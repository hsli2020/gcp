<?php

namespace App\Controllers;

class BaselineController extends ControllerBase
{
    public function exportAction()
    {
        $this->view->pageTitle = 'Baseline Exporting';

        $this->view->startTime = date('Y-m-d', strtotime('-1 days'));
        $this->view->endTime = date('Y-m-d');

        if ($this->request->isPost()) {
            set_time_limit(0);

            $params = $this->request->getPost();

            if ($this->request->getPost('btn') == 'today') {
                $filename = $this->baselineService->exportToday();
            } else {
                $filename = $this->baselineService->export($params);
            }

            $this->startDownload($filename);
        }
    }

    public function excludeAction()
    {
        $this->view->pageTitle = 'Excluded Dates';

        $zone = '';

        if ($this->request->isPost()) {
            $params = $this->request->getPost();
            $zone = $this->request->getPost('zone');
            $auth = $this->session->get('auth');
            $params['user'] = $auth['username'];
            $this->baselineService->setDateExcluded($params);
           #$this->response->redirect('/baseline/exclude');
        }

        $dates = $this->baselineService->loadExcludedDateList($zone);
        $zones = $this->baselineService->loadZoneNameList();

        $this->view->dates = $dates;
        $this->view->zones = $zones;
    }
}
