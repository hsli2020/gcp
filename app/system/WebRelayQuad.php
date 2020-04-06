<?php

namespace App\System;

class WebRelayQuad
{
    protected $primaryIP;
    protected $backupIP;

    public function __construct($info)
    {
        $this->primaryIP = $info['primary_ip'];
        $this->backupIP  = $info['backup_ip'];
    }

    /**
     * http://74.198.22.2:9001/state.xml
     * http://184.151.59.10:9001/state.xml
     *
     * This will return the following XML page.:
     *
     * <?xml version="1.0" encoding="utf-8" ?>
     * <datavalues>
     *   <relay1state>0</relay1state>
     *   <relay2state>1</relay2state>
     *   <relay3state>1</relay3state>
     *   <relay4state>1</relay4state>
     * </datavalues>
     *
     * <relayXstate> 0 = Off (coil off)
     * <relayXstate> 1 = On (coil energized)
     *
     * @return
     *
     *  Array
     *  (
     *      [relay1state] => 0
     *      [relay2state] => 1
     *      [relay3state] => 1
     *      [relay4state] => 1
     *  )
     */
    public function getState()
    {
        $res = $this->httpGet('');
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }

    /**
     * Turn Relay 1 Off （Stop Generator)
     *   http://74.198.22.2:9001/state.xml?relay1State=0
     *   http://184.151.59.10:9001/state.xml?relay1State=0
     *
     * Turn Relay 1 On （Start Generator)
     *   http://74.198.22.2:9001/state.xml?relay1State=1
     *   http://184.151.59.10:9001/state.xml?relay1State=1
     *
     * This WebRelay Model has 4 relays,
     * we use relay 1 to Start/Stop the Generator.
     */
    public function setState($relay, $state)
    {
        $res = $this->httpGet("?relay{$relay}State=$state");
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        return $array;
    }

    public function turnOn($relay)
    {
        return $this->setState($relay, 1);
    }

    public function turnOff($relay)
    {
        return $this->setState($relay, 0);
    }

    protected function httpGet($params)
    {
        $hosts = [
            $this->primaryIP,
            $this->backupIP,
        ];

        $output = "";
        foreach ($hosts as $host) {
            $ch = curl_init();

            $url = $host.$params;
           #echo $url, PHP_EOL;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout in seconds

            $output = curl_exec($ch);
           #var_dump($output);
            curl_close($ch);

            if ($output) {
                break;
            }
        }

        return $output;
    }
}

#$a = new WebRelayQuad();
#var_dump($a->getState());
#var_dump($a->setState(1, 1));
