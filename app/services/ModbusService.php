<?php

namespace App\Service;

use Phalcon\Di\Injectable;

class ModbusService extends Injectable
{
    private function getModbusSetting($project)
    {
		$sql = "SELECT * FROM modbus_setting WHERE project_id=$project";
		$row = $this->db->fetchOne($sql);
		return $row;
    }

    private function connectModbus($setting)
    {
		$ip = $setting['ip1'];
		$port = $setting['port'];

        // Create Modbus object
        $modbus = new \ModbusMaster($ip, "TCP");
        $modbus->port = $port;

        return $modbus;
    }

    // $status: 0 or 1
    public function writeSingleCoil($project, $status)
    {
		$setting = $this->getModbusSetting($project);
        $modbus = $this->connectModbus($setting);
        $coilAddr = $setting['coil_address']; // 5

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

    public function readRegisters($project)
    {
		$setting = $this->getModbusSetting($project);
        $modbus = $this->connectModbus($setting);
        $regAddr = $setting['holding_reg_address'] - 40001; // 49001 - 40001

        try {
            // FC 3
            $recData = $modbus->readMultipleRegisters(1, 9000, 2);
        }
        catch (Exception $e) {
            //echo $modbus;
            //echo $e;
            return 'Error';
        }

        return decbin((\PhpType::bytes2unsignedInt($recData)>>16)&0xFFFF);

        // Print status information
        #echo "Status:\n" . $modbus;

        // Print read data
        #echo "Data:\n";
        #print_r($recData);
        #echo decbin(PhpType::bytes2unsignedInt($recData)>>16);
        #echo "\n";
    }
}
