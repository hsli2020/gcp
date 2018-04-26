<?php

namespace App\Controllers;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        // home page
        return $this->dispatcher->forward([
            'controller' => 'dashboard',
            'action' => 'index'
        ]);
    }

    public function testAction()
    {
        $this->view->pageTitle = 'Test Page';
        $this->view->data = __METHOD__;
        $this->flashSession->success('Some shit happened');
    }
}
