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
    public $proxyDebug = [];

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

        $this->fsock = \stream_socket_client($proxyString, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);

        if (!$this->fsock) {
            throw new MinecraftException("Can't connect.");
        }

        if ($errno != 0) {
            throw new MinecraftException('Request denied, Errorcode ' . $errno . ': ' . $errstr);
        }

        \fwrite($this->fsock, 'CONNECT ' . $this->hostname . ':' . $this->port . " HTTP/1.1\r\n\r\n");

        $statusLine = \fgets($this->fsock);

        if (!$statusLine) {
            throw new MinecraftException('Missing status line', 100);
        }

        if (!\preg_match('/^HTTP\/([0-9]\.[0-9]) ([0-9]{3}) (.*)\r\n$/', $statusLine, $matches)) {
            throw new MinecraftException('Invalid status line', 100);
        }

        $this->proxyDebug['Version'] = $matches[1];
        $this->proxyDebug['StatusCode'] = $matches[2];
        $this->proxyDebug['StatusMessage'] = $matches[3];

        if ($this->proxyDebug['Version'] !== '1.0' && $this->proxyDebug['Version'] !== '1.1') {
            throw new MinecraftException(\sprintf('Incorrect HTTP version. Expected 1.0 or 1.1, got \'%s\'.', $this->proxyDebug['Version']), 100);
        }

        if (!(200 <= $this->proxyDebug['StatusCode'] && $this->proxyDebug['StatusCode'] < 300)) {
            throw new MinecraftException(\sprintf('Status code \'%1$d\' does not indicate success. (%2$s)', $this->proxyDebug['StatusCode'], $this->proxyDebug['StatusMessage']), 100);
        }

        while (($line = @\fgets($this->fsock)) !== "\r\n") {
            if ($line === \false) {
                throw new MinecraftException('Encountered EOF while searching for the end of proxy response headers.', 100);
            }
            $values = \preg_split('/:\s*/', $line);
            if (isset($values[1])) {
                $this->proxyDebug[$values[0]] = \rtrim($values[1]);
            }
        }

        $this->setTimeout($this->fsock, 2, 500);

        $this->login();
    }
}
