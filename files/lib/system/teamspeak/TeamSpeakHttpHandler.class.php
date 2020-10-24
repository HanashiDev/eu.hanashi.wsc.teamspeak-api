<?php
namespace wcf\system\teamspeak;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\HTTPServerErrorException;
use wcf\system\exception\HTTPUnauthorizedException;
use wcf\system\exception\SystemException;
use wcf\system\exception\TeamSpeakException;
use wcf\util\exception\HTTPException;
use wcf\util\HTTPRequest;
use wcf\util\JSON;

/**
 * Api for connection with TeamSpeak http query.
 *
 * @author	Peter Lohse <hanashi@hanashi.eu>
 * @copyright	Hanashi
 * @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package	WoltLabSuite\Core\System\TeamSpeak
 */
class TeamSpeakHttpHandler extends AbstractTeamSpeakQueryHandler {
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
	 * @inheritDoc
	 */
	public function __construct($hostname, $port, $username, $password) {
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
	public function setVirtualServerID($serverID) {
		$this->virtualServerID = $serverID;
	}

	/**
	 * @inheritDoc
	 */
	public function __destruct() {
		// nothing to do
	}

	/**
	 * @inheritDoc
	 */
	public function connect() {
		// nothing to do
	}

	/**
	 * @inheritDoc
	 */
	public function execute($command) {
		$url = $this->protocol . '://' . $this->hostname . ':' . $this->port . '/' . $this->virtualServerID . '/' . $command;

		$request = new HTTPRequest($url);
		$request->addHeader('x-api-key', $this->apiKey);
		try {
			$request->execute();
			$reply = $request->getReply();
			return $reply['body'];
		} catch (HTTPNotFoundException $e) {
			return '';
		} catch (HTTPServerErrorException $e) {
			return '';
		} catch (HTTPUnauthorizedException $e) {
			return '';
		} catch (SystemException $e) {
			return '';
		} catch (HTTPException $e) {
			return '';
		}
	}

	/**
	 * @inheritDoc
	 */
	public function login($username, $password) {
		// nothing to do
	}

	/**
	 * @inheritDoc
	 */
	public function call($method, $args) {
		$command = $method;
		if (count($args)) {
			$command = $command . '?' . http_build_query($args[0], null, '&');
		}
		$result = $this->execute($command);
		return $this->parseResult($result);
	}

	/**
     * @inheritDoc
     */
    public function parseResult($result) {
		try {
			$resultArr = JSON::decode($result);
			if (empty($resultArr['status']['message'])) {
				throw new TeamSpeakException('Unknown teamspeak result: '.print_r($result, true));
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
