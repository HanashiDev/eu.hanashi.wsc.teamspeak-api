<?php

namespace wcf\system\minecraft;

interface IMinecraftHandler
{
    /**
     * construct for Minecraft class
     *
     * @param   string  $hostname       the hostname/ip of your Minecraft server
     * @param   int     $port           the server rcon port of your Minecraft server (standard = 25575)
     * @param   string  $password       Password of server rcon
     */
    public function __construct($hostname, $port, $password);

    /**
     * destruct of Minecraft class
     */
    public function __destruct();

    /**
     * connect to Minecraft server rcon
     */
    public function connect();

    /**
     * method to execute server rcon commands
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
     * execute a command from server rcon
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
     * parse the results from Minecraft
     *
     * @return  array
     * @throws  MinecraftException
     */
    public function parseResult();
}
