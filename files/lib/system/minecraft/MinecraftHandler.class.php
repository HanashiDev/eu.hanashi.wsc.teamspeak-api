<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;

/**
 * MinecraftHandler class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
class MinecraftHandler extends AbstractMinecraftRCONHandler
{

    /**
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    private $fsock;

    /**
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    private $_Id;

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        parent::__destruct();
        @fclose($this->fsock);
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function connect()
    {
        $this->fsock = @fsockopen($this->hostname, $this->port, $errno, $errstr, 30);

        if (!$this->fsock) {
            throw new MinecraftException("Can't connect.");
        }

        $this->setTimeout($this->fsock, 2, 500);

        parent::connect();
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function login()
    {
        $PackID = $this->write(3, $this->password);

        // Real response (id: -1 = failure)
        $ret = $this->packetRead();
        if ($ret[0]['ID'] == 1) {
            parent::login();
            return;
        }
        throw new MinecraftException("Wrong password.");

        parent::login();
    }

    /**
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function setTimeout(&$res, $s, $m = 0)
    {
        if (version_compare(phpversion(), '4.3.0', '<')) {
            return socket_set_timeout($res, $s, $m);
        }
        return stream_set_timeout($res, $s, $m);
    }

    /**
     * Writes the packat.
     *
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     *
     * @param   array $cmd
     * @param   string $s1
     * @param   string $s2
     * @return  int packet identificator
     */
    private function write($cmd, $s1 = '', $s2 = '')
    {
        // Get and increment the packet id
        $id = ++$this->_Id;

        // Put our packet together
        $data = pack("VV", $id, $cmd) . $s1 . chr(0) . $s2 . chr(0);

        // Prefix the packet size
        $data = pack("V", strlen($data)) . $data;

        // Send packet
        fwrite($this->fsock, $data, strlen($data));

        // In case we want it later we'll return the packet id
        return $id;
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    private function packetRead()
    {
        //Declare the return array
        $retarray = array();
        //Fetch the packet size
        while ($size = @fread($this->fsock, 4)) {
            $size = unpack('V1Size', $size);
            //Work around valve breaking the protocol
            if ($size["Size"] > 4096) {
                //pad with 8 nulls
                $packet = "\x00\x00\x00\x00\x00\x00\x00\x00" . fread($this->fsock, 4096);
            } else {
                //Read the packet back
                $packet = fread($this->fsock, $size["Size"]);
            }
            array_push($retarray, unpack("V1ID/V1Response/a*S1/a*S2", $packet));
        }
        return $retarray;
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function parseResult()
    {
        parent::parseResult();

        $Packets = $this->packetRead();

        foreach ($Packets as $pack) {
            if (isset($ret[$pack['ID']])) {
                $ret[$pack['ID']]['S1'] .= $pack['S1'];
                $ret[$pack['ID']]['S2'] .= $pack['S1'];
            } else {
                $ret[$pack['ID']] = array(
                    'Response' => $pack['Response'],
                    'S1' => rtrim($pack['S1']),
                    'S2' => rtrim($pack['S2']),
                );
            }
        }
        return $ret;
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function execute($command)
    {
        $this->write(2, $command);
    }

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function call($command)
    {
        parent::call($command);

        $ret = $this->parseResult();

        return $ret[$this->_Id];
    }
}
