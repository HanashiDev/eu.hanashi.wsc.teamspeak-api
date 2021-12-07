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
class MinecraftProxyHandler extends MinecraftHandler
{
    /** @var array List of proxy Responses. */
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

        $this->fsock = stream_socket_client($proxyString, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);

        if (!$this->fsock) {
            throw new MinecraftException("Can't connect.");
        }

        if ($errno != 0) {
            throw new MinecraftException('Request denied, Errorcode ' . $errno . ': ' . $errstr);
        }

        fwrite($this->fsock, 'CONNECT ' . $this->hostname . ':' . $this->port . " HTTP/1.1\r\n\r\n");

        $this->rsp = [];

        while (($line = @fgets($this->fsock)) !== "\r\n") {
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

        $this->setTimeout($this->fsock, 2, 500);

        $this->login();
    }
}
