<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;

class MinecraftHandler extends AbstractMinecraftRCONHandler
{

    private $fsock;

    private $_Id;

    /**
     * @inheritDoc
     */
    public function __destruct() {
        @fclose($this->fsock);
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    public function connect() {
        $this->fsock = @fsockopen($this->hostname,$this->port, $errno, $errstr, 30);

        if (!$this->fsock) {
            throw new MinecraftException();
        }

        $this->set_timeout($this->fsock,2,500);

        // login to server rcon
        $this->login($this->password);
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    public function login($password) {
        $PackID = $this->write(3,$password);

        // Real response (id: -1 = failure)
        $ret = $this->packetRead();
        if ($ret[0]['ID'] == -1) {
            return false;
        }
        return true;
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     */
    public function set_timeout(&$res,$s,$m=0) {
        if (version_compare(phpversion(),'4.3.0','<')) {
            return socket_set_timeout($res,$s,$m);
        }
        return stream_set_timeout($res,$s,$m);
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * Writes the packat.
     * 
     * @param   array $cmd
     * @param   string $s1
     * @param   string $s2
     * @return  int packet identificator
     */
    private function write($cmd, $s1='', $s2='') {
        // Get and increment the packet id
        $id = ++$this->_Id;

        // Put our packet together
        $data = pack("VV",$id,$cmd).$s1.chr(0).$s2.chr(0);

        // Prefix the packet size
        $data = pack("V",strlen($data)).$data;

        // Send packet
        fwrite($this->fsock,$data,strlen($data));

        // In case we want it later we'll return the packet id
        return $id;
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    private function packetRead() {
        //Declare the return array
        $retarray = array();
        //Fetch the packet size
        while ($size = @fread($this->fsock,4)) {
            $size = unpack('V1Size',$size);
            //Work around valve breaking the protocol
            if ($size["Size"] > 4096) {
                //pad with 8 nulls
                $packet = "\x00\x00\x00\x00\x00\x00\x00\x00".fread($this->fsock,4096);
            }
            else {
                //Read the packet back
                $packet = fread($this->fsock,$size["Size"]);
            }
            array_push($retarray,unpack("V1ID/V1Response/a*S1/a*S2",$packet));
        }
        return $retarray;
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    public function parseResult() {
        $Packets = $this->packetRead();

        foreach($Packets as $pack) {
            if (isset($ret[$pack['ID']])) {
                $ret[$pack['ID']]['S1'] .= $pack['S1'];
                $ret[$pack['ID']]['S2'] .= $pack['S1'];
            } else {
                $ret[$pack['ID']] = array(
                    'Response' => $pack['Response'],
                    'S1' => $pack['S1'],
                    'S2' => $pack['S2'],
                );
            }
        }
        return $ret;
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    public function execute($command) {
        $this->write(2,$command);
    }

    /**
     * Based on https://gist.github.com/tehbeard/1292348
     * @inheritDoc
     */
    public function call($command)
    {
        $this->execute($command);

        $ret = $this->parseResult();

        return $ret[$this->_Id]['S1'];
    }

}
