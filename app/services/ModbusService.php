<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ModbusService extends Injectable
{
    private function connectModbus()
    {
        // Create Modbus object
        $modbus = new \ModbusMaster("74.198.22.159", "TCP");
        $modbus->port = 1024;
        return $modbus;
    }

    // $status: 0 or 1
    public function writeSingleCoil($status)
    {
        $modbus = $this->connectModbus();

        // Data to be writen - TRUE, FALSE
        $data_true = array(TRUE);
        $data_false = array(FALSE);

        try {
            // Write single coil - FC5
            $data = $status ? $data_true : $data_false;
            $modbus->writeSingleCoil(1, 5, $data); // 5 seems ok
        }
        catch (Exception $e) {
            //echo $modbus;
            //echo $e;
            return false;
        }

        return true;
    }

    public function readRegisters()
    {
        $modbus = $this->connectModbus();

        try {
            // FC 3
            $recData = $modbus->readMultipleRegisters(1, 9000, 2);
        }
        catch (Exception $e) {
            //echo $modbus;
            //echo $e;
            return 'Error';
        }

        return decbin(\PhpType::bytes2unsignedInt($recData)>>16);

        // Print status information
        #echo "Status:\n" . $modbus;

        // Print read data
        #echo "Data:\n";
        #print_r($recData);
        #echo decbin(PhpType::bytes2unsignedInt($recData)>>16);
        #echo "\n";
    }
}
