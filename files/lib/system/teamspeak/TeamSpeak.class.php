<?php
namespace wcf\system\teamspeak;
use phpseclib\Net\SSH2;
use wcf\system\exception\SystemException;
use wcf\system\exception\TeamSpeakException;
use wcf\system\io\RemoteFile;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\TeamSpeakUtil;

/**
* Api for connection with TeamSpeak server query.
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\System\TeamSpeak
*/
class TeamSpeak {
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
     * Select the query protocol (raw = use raw server query; ssh = use ssh server query)
     * 
     * @var string
     */
    protected $queryProtocol;

    /**
     * server query object
     * 
     * @var RemoteFile
     * @var ressource
     */
    protected $queryObj;

    /**
     * construct for TeamSpeak class
     * 
     * @param   string  $hostname       the hostname/ip of your TeamSpeak server
     * @param   int     $port           the server query port of your TeamSpeak server (standard: raw = 10011; ssh = 10022)
     * @param   string  $username       Username of server query (standard: serveradmin)
     * @param   string  $password       Password of server query
     * @param   string  $queryProtocol  Select the query protocol (raw = use raw server query; ssh = use ssh server query)
     */
    public function __construct($hostname, $port, $username, $password, $queryProtocol = 'raw') {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->queryProtocol = $queryProtocol;
        $this->username = $username;
        $this->password = $password;

        $this->connect();
    }

    /**
     * destruct of TeamSpeak class
     */
    public function __destruct() {
        if ($this->queryObj) {
            $this->execute('quit');
        }
    }

    /**
     * connect to TeamSpeak server query over ssh or raw connection
     */
    protected function connect() {
        if ($this->queryProtocol == 'raw') {
            $this->queryObj = new RemoteFile($this->hostname, $this->port);
        } else if ($this->queryProtocol == 'ssh') {
            require_once(WCF_DIR.'lib/system/api/phpseclib/autoload.php');
            $this->queryObj = new SSH2($this->hostname, $this->port);
        }

        if (!$this->queryObj) {
            throw new TeamSpeakException('Connection failed');
        }
        if ($this->queryProtocol == 'raw') {
            if (StringUtil::trim($this->queryObj->gets()) != 'TS3') {
                throw new TeamSpeakException('Not a TeamSpeak server');
            }
            $motd = $this->queryObj->gets();
        }

        // login to server query
        $this->login($this->username, $this->password);
    }

    /**
     * Authenticates with the TeamSpeak Server instance using given ServerQuery login credentials.
     * 
     * @param   string  $username       Username of server query (standard: serveradmin)
     * @param   string  $password       Password of server query
     */
    public function login($username, $password) {
        if ($this->queryProtocol == 'raw') {
            $this->execute('login client_login_name='.TeamSpeakUtil::escape($username).' client_login_password='.TeamSpeakUtil::escape($password));
        } else if ($this->queryProtocol == 'ssh') {
            if (!$this->queryObj->login($username, $password)) {
                throw new TeamSpeakException('Authentication failed...');
            }
            $header = StringUtil::trim($this->queryObj->read("\n"));
            if ($header != 'TS3') {
                throw new TeamSpeakException('Not a TeamSpeak server');
            }
            $motd = StringUtil::trim($this->queryObj->read("\n"));
        }
    }
    
    /**
     * execute a command from server query
     * 
     * Example:
     * <code>
     * $ts->use(['port' => 9987, '-virtual'])
     * </code>
     * 
     * @param   string  $method     method name
     * @param   array   $args       paramaeter
     * @return  array|null
     */
    public function __call($method, $args) {
        $command = $method;
        if (count($args) > 0) {
            foreach ($args as $arg) {
                if (is_array($arg)) {
                    foreach ($arg as $key => $val) {
                        if (is_numeric($key)) {
                            $command .= ' '.$val;
                        } else {
                            $command .= ' '.$key.'='.TeamSpeakUtil::escape($val);
                        }
                    }
                } else {
                    $command .= ' '.$arg;
                }
            }
        }
        return $this->execute($command);
    }

    /**
     * method to execute server query commands
     * 
     * @param   string  $command        Command to execute on server
     * @return  array
     */
    protected function execute($command) {
        if ($this->queryProtocol == 'raw') {
            return $this->executeRaw($command);
        } else if ($this->queryProtocol == 'ssh') {
            return $this->executeSsh($command);
        }
    }

    /**
     * method to execute ssh server query commands
     * 
     * @param   string  $command        Command to execute on server
     * @return  array
     */
    protected function executeSsh($command) {
        $result = [];
        $this->queryObj->write($command."\n");
        $commandLine = $this->queryObj->read("\n");
        if ($command == 'quit') {
            return true;
        }
        do {
            $line = StringUtil::trim($this->queryObj->read("\n"));
            $result[] = $line;
        } while ($line && substr($line, 0, 5) != "error");
        return $this->parseResult($result);
    }

    /**
     * method to execute raw server query commands
     * 
     * @param   string  $command        Command to execute on server
     * @return  array
     */
    protected function executeRaw($command) {
        $result = [];
        $this->queryObj->puts($command."\n");
        if ($command == 'quit') {
            return true;
        }
        do {
			$line = StringUtil::trim($this->queryObj->gets());
            $result[] = $line;
        } while ($line && substr($line, 0, 5) != "error");
        return $this->parseResult($result);
    }

    /**
     * parse the results from TeamSpeak
     * 
     * @param   array   $result         result of TeamSpeak server query
     * @return  array
     * @throws  TeamSpeakException
     */
    protected function parseResult($result) {
        $resultArr = [];
        $error = [];

        foreach ($result as $resultPart) {
            $resultSplitted = explode('|', $resultPart);
            foreach ($resultSplitted as $resultRow) {
                $row = [];
                $rowSplitted = explode(' ', $resultRow);
                if (count($rowSplitted) == 0) {
                    continue;
                }
                if ($rowSplitted[0] == 'error') {
                    $error = $this->parseRow($rowSplitted);
                } else {
                    $row = $this->parseRow($rowSplitted);
                    if (count($row) > 0) {
                        $resultArr[] = $row;
                    }
                }
            }
        }
        if ($error['msg'] != 'ok') {
            throw new TeamSpeakException($error['msg']);
        }
        return $resultArr;
    }

    /**
     * parse reply row
     * 
     * @param   array       $row        Row of result
     * @return  array
     */
    protected function parseRow($row) {
        $rowArr = [];
        foreach ($row as $column) {
            $columnSplitted = explode('=', $column, 2);
            if (count($columnSplitted) > 1) {
                $rowArr[$columnSplitted[0]] = TeamSpeakUtil::unescape($columnSplitted[1]);
            }
        }
        return $rowArr;
    }
}