<?php

namespace wcf\system\teamspeak;

/**
* Api for connection with TeamSpeak server query.
*
* @author   Peter Lohse <hanashi@hanashi.eu>
* @copyright    Hanashi
* @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package  WoltLabSuite\Core\System\TeamSpeak
*/
class TeamSpeakConnectionHandler
{
    /**
     * server query object
     *
     * @var TeamSpeakRawHandler
     * @var TeamSpeakSshHandler
     * @var TeamSpeakLibSsh2Handler
     * @var TeamSpeakHttpHandler
     * @var TeamSpeakHttpsHandler
     */
    protected $teamSpeakHandler;

    /**
     * query protocol
     *
     * @var string
     */
    protected $queryProtocol;

    /**
     * construct for TeamSpeak class
     *
     * @param   string  $hostname       the hostname/ip of your TeamSpeak server
     * @param   int     $port           the server query port of your TeamSpeak server (standard: raw = 10011; ssh = 10022)
     * @param   string  $username       Username of server query (standard: serveradmin)
     * @param   string  $password       Password of server query
     * @param   int     $virtualServerPort  virtual server port or id
     * @param   string  $queryProtocol  Select the query protocol (raw = use raw server query; ssh = use ssh server query)
     */
    public function __construct($hostname, $port, $username, $password, $virtualServerPort, $queryProtocol = 'raw')
    {
        $this->queryProtocol = $queryProtocol;

        if ($queryProtocol == 'raw') {
            $this->teamSpeakHandler = new TeamSpeakRawHandler($hostname, $port, $username, $password);
        } elseif ($queryProtocol == 'ssh') {
            if (\function_exists('ssh2_connect') && \function_exists('ssh2_auth_password') && \function_exists('ssh2_shell')) {
                $this->teamSpeakHandler = new TeamSpeakLibSsh2Handler($hostname, $port, $username, $password);
            } else {
                $this->teamSpeakHandler = new TeamSpeakSshHandler($hostname, $port, $username, $password);
            }
        } elseif ($queryProtocol == 'http') {
            $this->teamSpeakHandler = new TeamSpeakHttpHandler($hostname, $port, $username, $password);
            $this->teamSpeakHandler->setVirtualServerID($virtualServerPort);
        } elseif ($queryProtocol == 'https') {
            $this->teamSpeakHandler = new TeamSpeakHttpsHandler($hostname, $port, $username, $password);
            $this->teamSpeakHandler->setVirtualServerID($virtualServerPort);
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
    public function __call($method, $args)
    {
        return $this->teamSpeakHandler->call($method, $args);
    }
}
