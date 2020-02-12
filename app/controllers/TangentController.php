<?php

namespace App\Controllers;

class TangentController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->pageTitle = 'Tangent';
    }

    public function getStateAction()
    {
        return $this->json('OK', [
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}
