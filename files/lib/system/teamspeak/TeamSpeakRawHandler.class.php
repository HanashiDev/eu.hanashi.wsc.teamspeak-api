<?php

namespace wcf\system\teamspeak;

use Override;
use SensitiveParameter;
use wcf\system\exception\SystemException;
use wcf\system\exception\TeamSpeakException;
use wcf\system\io\RemoteFile;
use wcf\util\StringUtil;
use wcf\util\TeamSpeakUtil;

/**
 * Api for connection with TeamSpeak raw server query.
 *
 * @author   Peter Lohse <hanashi@hanashi.eu>
 * @copyright    Hanashi
 * @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package  WoltLabSuite\Core\System\TeamSpeak
 */
final class TeamSpeakRawHandler extends AbstractTeamSpeakQueryHandler
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
     * server query object
     *
     * @var SSH2
     */
    public $queryObj;

    #[Override]
    public function __construct(
        $hostname,
        $port,
        $username,
        #[SensitiveParameter]
        $password
    ) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    #[Override]
    public function __destruct()
    {
        if ($this->queryObj) {
            $this->execute('quit');
        }
    }

    #[Override]
    public function connect()
    {
        try {
            $this->queryObj = new RemoteFile($this->hostname, $this->port);
        } catch (SystemException $e) {
            throw new TeamSpeakException('Connection failed');
        }

        if (!$this->queryObj) {
            throw new TeamSpeakException('Connection failed');
        }

        if (StringUtil::trim($this->queryObj->gets()) != 'TS3') {
            throw new TeamSpeakException('Not a TeamSpeak server');
        }
        $motd = $this->queryObj->gets();

        // login to server query
        $this->login($this->username, $this->password);
    }

    #[Override]
    public function login(
        $username,
        #[SensitiveParameter]
        $password
    ) {
        $replyLines = $this->execute(
            'login client_login_name=' . TeamSpeakUtil::escape($username) . ' client_login_password='
            . TeamSpeakUtil::escape($password)
        );
        $error = $this->getError($replyLines);
        if ($error !== false && !empty($error['msg'])) {
            $msg = $error['msg'];
            if (!empty($error['extra_msg'])) {
                $msg .= ' - ' . $error['extra_msg'];
            }
            throw new TeamSpeakException($msg);
        }
    }

    protected function getError(array $replyLines)
    {
        foreach ($replyLines as $replyLine) {
            if (!\str_starts_with($replyLine, 'error')) {
                continue;
            }
            $errorRows = \explode(' ', $replyLine);
            $error = [];
            foreach ($errorRows as $errorRow) {
                if ($errorRow == 'error') {
                    continue;
                }
                $errorColumns = \explode('=', $errorRow, 2);
                if (\count($errorColumns) != 2) {
                    continue;
                }
                $error[$errorColumns[0]] = TeamSpeakUtil::unescape($errorColumns[1]);
            }
            if (!empty($error['msg']) && $error['msg'] == 'ok') {
                return false;
            }

            return $error;
        }

        return false;
    }

    #[Override]
    public function execute($command)
    {
        $result = [];
        $this->queryObj->puts($command . "\n");
        if ($command == 'quit') {
            return true;
        }
        do {
            $line = StringUtil::trim($this->queryObj->gets());
            $result[] = $line;
        } while ($line && \substr($line, 0, 5) != "error");

        return $result;
    }
}
