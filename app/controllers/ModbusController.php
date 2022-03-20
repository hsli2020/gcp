<?php

namespace App\Controllers;

class ModbusController extends ControllerBase
{
    public function bramptonAction()
    {
        $this->view->pageTitle = 'Brampton Testing';

        $this->view->coil = '';
        $this->view->register = '';

        $btn = $this->request->getPost('btn');

		$project = 23; // Brampton

        if ($btn == 'write0') {
			$this->view->coil = '0';
			$this->modbusService->writeSingleCoil($project, 0);
        } else if ($btn == 'write1') {
			$this->view->coil = '1';
			$this->modbusService->writeSingleCoil($project, 1);
        } else if ($btn == 'readreg') {
			$reg = $this->modbusService->readRegisters($project);
			$this->view->register = $reg;
        }
    }

    public function windsorAction()
    {
        $this->view->pageTitle = 'Windsor Testing';

        $this->view->coil = '';
        $this->view->register = '';

        $btn = $this->request->getPost('btn');

		$project = 28; // Windsor

        if ($btn == 'write0') {
			$this->view->coil = '0';
			$this->modbusService->writeSingleCoil($project, 0);
        } else if ($btn == 'write1') {
			$this->view->coil = '1';
			$this->modbusService->writeSingleCoil($project, 1);
        } else if ($btn == 'readreg') {
			$reg = $this->modbusService->readRegisters($project);
			$this->view->register = $reg;
        }
    }
}
