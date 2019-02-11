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
class TeamSpeakConnectionHandler {
    /**
     * server query object
     * 
     * @var TeamSpeakRawHandler
     * @var TeamSpeakSshHandler
     */
    protected $teamSpeakHandler;

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
        if ($queryProtocol == 'raw') {
            $this->teamSpeakHandler = new TeamSpeakRawHandler($hostname, $port, $username, $password);
        } else if ($queryProtocol == 'ssh') {
            if (function_exists('ssh2_connect') && function_exists('ssh2_auth_password') && function_exists('ssh2_shell')) {
                $this->teamSpeakHandler = new TeamSpeakLibSsh2Handler($hostname, $port, $username, $password);
            } else {
                $this->teamSpeakHandler = new TeamSpeakSshHandler($hostname, $port, $username, $password);
            }
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
     * @param   string  $returnRaw      get raw return
     * @return  array
     */
    public function execute($command, $returnRaw = false) {
        $result = $this->teamSpeakHandler->execute($command);
        if ($returnRaw) return $result;
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