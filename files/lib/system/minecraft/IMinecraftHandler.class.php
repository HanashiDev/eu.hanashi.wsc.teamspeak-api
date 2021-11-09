<?php

namespace wcf\system\minecraft;

/**
 * MinecraftHandler interface
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
interface IMinecraftHandler
{
    /**
     * Construct for Minecraft class
     *
     * @param   string  $hostname       the hostname/ip of your Minecraft server
     * @param   int     $port           the server rcon port of your Minecraft server (standard = 25575)
     * @param   string  $password       Password of server rcon
     */
    public function __construct($hostname, $port, $password);

    /**
     * Destruct of Minecraft class
     */
    public function __destruct();

    /**
     * Connect to Minecraft server rcon
     */
    public function connect();

    /**
     * Method to execute server rcon commands
     *
     * @param   string  $command        Command to execute on server
     * @return  array
     */
    public function execute($command);

    /**
     * Authenticates with the Minecraft Server instance using given ServerRCON login credentials.
     *
     * @param   string  $password       Password of server rcon
     */
    public function login($password);

    /**
     * Execute a command from server rcon
     *
     * Example:
     * <code>
     * $mc->call('list uuids')
     * </code>
     *
     * @param   string  $command     Command to execute on server
     * @return  array|null
     */
    public function call($command);

    /**
     * Parse the results from Minecraft
     *
     * @return  array
     * @throws  MinecraftException
     */
    public function parseResult();
}
