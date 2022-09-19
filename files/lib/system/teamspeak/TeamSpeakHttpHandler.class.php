<?php

namespace wcf\system\teamspeak;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use wcf\system\exception\SystemException;
use wcf\system\exception\TeamSpeakException;
use wcf\system\io\HttpFactory;
use wcf\util\JSON;

/**
 * Api for connection with TeamSpeak http query.
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package WoltLabSuite\Core\System\TeamSpeak
 */
class TeamSpeakHttpHandler extends AbstractTeamSpeakQueryHandler
{
    /**
     * the hostname/ip of your TeamSpeak server
     *
     * @var string
     */
    protected $hostname;

    /**
     * the server query port of your TeamSpeak server (standard: raw = 10011; ssh = 10022)
     *
     * @var int
     */
    protected $port;

    /**
     * Password of server query
     *
     * @var string
     */
    protected $apiKey;

    /**
     * ID of virtual teamspeak server
     *
     * @var int
     */
    protected $virtualServerID;

    /**
     * protocol
     *
     * @var string
     */
    protected $protocol = 'http';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @inheritDoc
     */
    public function __construct($hostname, $port, $username, $password)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->apiKey = $password;
    }

    /**
     * set virtual teamspeak server id
     *
     * @param  int $serverID
     * @return void
     */
    public function setVirtualServerID($serverID)
    {
        $this->virtualServerID = $serverID;
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        // nothing to do
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
        // nothing to do
    }

    final protected function getHttpClient(): ClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = HttpFactory::makeClient([
                RequestOptions::TIMEOUT => 2,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * @inheritDoc
     */
    public function execute($command)
    {
        $url = $this->protocol . '://' . $this->hostname . ':' . $this->port . '/' . $this->virtualServerID . '/' . $command;

        $headers = [
            'x-api-key' => $this->apiKey,
        ];

        $request = new Request('GET', $url, $headers);
        try {
            $response = $this->getHttpClient()->send($request);

            return (string)$response->getBody();
        } catch (BadResponseException $e) {
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }

            return (string)$e->getResponse()->getBody();
        } catch (GuzzleException $e) {
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }

            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function login($username, $password)
    {
        // nothing to do
    }

    /**
     * @inheritDoc
     */
    public function call($method, $args)
    {
        $command = $method;
        if (\count($args)) {
            $arguments = [];
            foreach ($args[0] as $key => $value) {
                if (\is_numeric($key)) {
                    $arguments[] = $value;
                } else {
                    $arguments[] = \urlencode($key) . '=' . \urlencode($value);
                }
            }
            $command = $command . '?' . \implode('&', $arguments);
        }
        $result = $this->execute($command);

        return $this->parseResult($result);
    }

    /**
     * @inheritDoc
     */
    public function parseResult($result)
    {
        try {
            $resultArr = JSON::decode($result);
            if (empty($resultArr['status']['message'])) {
                throw new TeamSpeakException('Unknown teamspeak result: ' . \print_r($result, true));
            }
            if ($resultArr['status']['message'] != 'ok') {
                throw new TeamSpeakException($resultArr['status']['message']);
            }
            if (!empty($resultArr['body'])) {
                return $resultArr['body'];
            } else {
                return [];
            }
        } catch (SystemException $e) {
            throw new TeamSpeakException('could not decode teamspeak message');
        }
    }
}
