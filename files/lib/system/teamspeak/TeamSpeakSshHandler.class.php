<?php
namespace wcf\system\teamspeak;
use phpseclib\Net\SSH2;
use wcf\system\exception\ErrorException;
use wcf\system\exception\TeamSpeakException;
use wcf\util\StringUtil;
use wcf\util\TeamSpeakUtil;

/**
* Api for connection with TeamSpeak ssh server query.
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\System\TeamSpeak
*/
class TeamSpeakSshHandler extends AbstractTeamSpeakQueryHandler {
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
    protected $queryObj;

    /**
     * @inheritDoc
     */
    public function __construct($hostname, $port, $username, $password) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function __destruct() {
        if ($this->queryObj) {
            $this->execute('quit');
        }
    }

    /**
     * @inheritDoc
     */
    public function connect() {
        require_once(WCF_DIR.'lib/system/api/phpseclib/autoload.php');
        $this->queryObj = new SSH2($this->hostname, $this->port);

        if (!$this->queryObj) {
            throw new TeamSpeakException('Connection failed');
        }

        // login to server query
        $this->login($this->username, $this->password);
    }

    /**
     * @inheritDoc
     */
    public function login($username, $password) {
        try {
            if (!$this->queryObj->login($username, $password)) {
                throw new TeamSpeakException('Authentication failed...');
            }
        } catch (ErrorException $e) {
            throw new TeamSpeakException('Connection failed');
        }
        $header = StringUtil::trim($this->queryObj->read("\n"));
        if ($header != 'TS3') {
            throw new TeamSpeakException('Not a TeamSpeak server');
        }
        $motd = StringUtil::trim($this->queryObj->read("\n"));
    }

    /**
     * @inheritDoc
     */
    public function execute($command) {
        $result = [];
        $this->queryObj->write($command."\n");
        $commandLine = $this->queryObj->read("\n");
        if ($command == 'quit') {
            return [];
        }
        do {
            $line = StringUtil::trim($this->queryObj->read("\n"));
            $result[] = $line;
        } while ($line && substr($line, 0, 5) != "error");
        
        return $result;
    }
}