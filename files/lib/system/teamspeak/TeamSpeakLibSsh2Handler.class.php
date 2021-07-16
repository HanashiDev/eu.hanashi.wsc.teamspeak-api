<?php

namespace wcf\system\teamspeak;

use wcf\system\exception\ErrorException;
use wcf\system\exception\TeamSpeakException;
use wcf\util\StringUtil;

/**
* Api for connection with TeamSpeak ssh server query with libssh2.
*
* @author   Peter Lohse <hanashi@hanashi.eu>
* @copyright    Hanashi
* @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package  WoltLabSuite\Core\System\TeamSpeak
*/
class TeamSpeakLibSsh2Handler extends AbstractTeamSpeakQueryHandler
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
     * Username of server query (standard: serveradmin)
     *
     * @var string
     */
    protected $username;

    /**
     * Password of server query
     *
     * @var string
     */
    protected $password;

    /**
     * SSH2 connection ressource
     *
     * @var resource
     */
    protected $connection;

    /**
     * server query object
     *
     * @var resource
     */
    protected $queryObj;

    /**
     * @inheritDoc
     */
    public function __construct($hostname, $port, $username, $password)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
        if ($this->queryObj) {
            $this->execute('quit');
        }
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
        try {
            $this->connection = ssh2_connect($this->hostname, $this->port);
        } catch (ErrorException $e) {
            throw new TeamSpeakException('Connection failed');
        }

        if (!$this->connection) {
            throw new TeamSpeakException('Connection failed');
        }

        // login to server query
        $this->login($this->username, $this->password);
    }

    /**
     * @inheritDoc
     */
    public function login($username, $password)
    {
        if (!ssh2_auth_password($this->connection, $username, $password)) {
            throw new TeamSpeakException('Authentication failed...');
        }
        $this->queryObj = ssh2_shell($this->connection, 'raw');
        if (!$this->queryObj) {
            throw new TeamSpeakException('Opening Shell failed');
        }
        stream_set_blocking($this->queryObj, true);

        $header = StringUtil::trim(stream_get_line($this->queryObj, PHP_INT_MAX, "\n\r"));
        if ($header != 'TS3') {
            throw new TeamSpeakException('Not a TeamSpeak server');
        }
        $motd = StringUtil::trim(stream_get_line($this->queryObj, PHP_INT_MAX, "\n\r"));
    }

    /**
     * @inheritDoc
     */
    public function execute($command)
    {
        $result = [];
        fwrite($this->queryObj, $command . "\n");
        if ($command == 'quit') {
            return [];
        }
        do {
            $line = StringUtil::trim(stream_get_line($this->queryObj, PHP_INT_MAX, "\n\r"));
            $result[] = $line;
        } while ($line && substr($line, 0, 5) != "error");

        return $result;
    }
}
