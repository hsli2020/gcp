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

        if ($btn == 'write0') {
			$this->view->coil = '0';
			$this->modbusService->writeSingleCoil(0);
        } else if ($btn == 'write1') {
			$this->view->coil = '1';
			$this->modbusService->writeSingleCoil(1);
        } else if ($btn == 'readreg') {
			$reg = $this->modbusService->readRegisters();
			$this->view->register = $reg;
        }
    }
}
