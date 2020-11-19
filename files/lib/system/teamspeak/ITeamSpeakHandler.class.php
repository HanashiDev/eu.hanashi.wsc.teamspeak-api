<?php
namespace wcf\system\teamspeak;

interface ITeamSpeakHandler {
    /**
     * construct for TeamSpeak class
     * 
     * @param   string  $hostname       the hostname/ip of your TeamSpeak server
     * @param   int     $port           the server query port of your TeamSpeak server (standard: raw = 10011; ssh = 10022)
     * @param   string  $username       Username of server query (standard: serveradmin)
     * @param   string  $password       Password of server query
     */
    public function __construct($hostname, $port, $username, $password);

    /**
     * destruct of TeamSpeak class
     */
    public function __destruct();

    /**
     * connect to TeamSpeak server query
     */
    public function connect();

    /**
     * method to execute server query commands
     * 
     * @param   string  $command        Command to execute on server
     * @param   string  $returnRaw      get raw return
     * @return  array
     */
    public function execute($command);

    /**
     * Authenticates with the TeamSpeak Server instance using given ServerQuery login credentials.
     * 
     * @param   string  $username       Username of server query (standard: serveradmin)
     * @param   string  $password       Password of server query
     */
    public function login($username, $password);
    
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
    public function call($method, $args);

    /**
     * parse the results from TeamSpeak
     * 
     * @param   array   $result         result of TeamSpeak server query
     * @return  array
     * @throws  TeamSpeakException
     */
    public function parseResult($result);
}