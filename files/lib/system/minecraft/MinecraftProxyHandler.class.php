<?php

namespace wcf\system\minecraft;

use wcf\system\exception\MinecraftException;
use wcf\util\Url;

/**
 * MinecraftProxyHandler class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
class MinecraftProxyHandler extends AbstractMinecraftRCONHandler
{

    private $proxy;

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
        @fclose($this->proxy);
    }

    public $rsp = [];

    /**
     * @inheritDoc
     * @see https://gist.github.com/tehbeard/1292348 Based on the work of tehbeard.
     */
    public function connect()
    {

        $proxyUrl = Url::parse(PROXY_SERVER_HTTP);

        $auth = '';
        if (!empty($proxyUrl['user']) && !empty($proxyUrl['pass'])) {
            $auth = '[' . $proxyUrl['user'] . ':' . $proxyUrl['pass'] . '@';
        }

        $proxyString = 'tcp://' . $auth . $proxyUrl['host'] . ':' . $proxyUrl['port'];

        $this->proxy = stream_socket_client($proxyString, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);

        if (!$this->proxy) {
            throw new MinecraftException("Can't connect.");
        }

        if ($errno != 0) {
            throw new MinecraftException('Request denied, Errorcode ' . $errno . ': ' . $errstr);
        }

        fwrite($this->proxy, 'CONNECT ' . $this->hostname . ':' . $this->port . " HTTP/1.1\r\n\r\n");

        $this->rsp = [];

        while (($line = @fgets($this->proxy)) !== "\r\n") {
            if ($line === false) {
                throw new MinecraftException('Reached end of file to early.');
            }
            if (preg_match('/^HTTP\/([0-9]\.[0-9]) ([0-9]{3}) (.*)\r\n$/', $line, $matches)) {
                $this->rsp['version'] = $matches[1];
                $this->rsp['statusCode'] = $matches[2];
            } else {
                $values = preg_split('/:\s*/', $line);
                if (isset($values[1])) {
                    $this->rsp[$values[0]] = rtrim($values[1]);
                }
            }
        }

        if (empty($this->rsp)) {
            throw new MinecraftException('Response empty.');
        }

        if (preg_match('/(0-9)\.(0-9)/', $this->rsp['version'])) {
            throw new MinecraftException('Unknown Version format: ' . $rsp['version']);
        }

        if ($this->rsp['version'] != '1.0' && $this->rsp['version'] != '1.1') {
            throw new MinecraftException('Unsupporter HTTP version: ' . $rsp['version']);
        }

        if (!(200 <= $this->rsp['statusCode'] && $this->rsp['statusCode'] <= 299)) {
            throw new MinecraftException('HTML code ' . $this->rsp['statusCode']);
        }

        $this->setTimeout($this->proxy, 2, 500);

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
        fwrite($this->proxy, $data, strlen($data));

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
        while ($size = @fread($this->proxy, 4)) {
            $size = unpack('V1Size', $size);
            //Work around valve breaking the protocol
            if ($size["Size"] > 4096) {
                //pad with 8 nulls
                $packet = "\x00\x00\x00\x00\x00\x00\x00\x00" . fread($this->proxy, 4096);
            } else {
                //Read the packet back
                $packet = fread($this->proxy, $size["Size"]);
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
