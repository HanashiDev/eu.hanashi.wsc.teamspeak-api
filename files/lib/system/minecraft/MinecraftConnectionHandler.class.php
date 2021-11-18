<?php

namespace wcf\system\minecraft;

/**
 * MinecraftConnectionHandler class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
class MinecraftConnectionHandler
{
    /**
     * Server rcon object
     *
     * @var MinecraftHandler
     */
    public $minecraftHandler;

    /**
     * Construct for MinecraftConnectionHandler class
     *
     * @param   string  $hostname       the hostname/ip of your Minecraft server
     * @param   int     $port           the server rcon port of your Minecraft server (standard = 25575)
     * @param   string  $password       Password of server rcon
     */
    public function __construct($hostname, $port, $password)
    {
        $this->minecraftHandler = new MinecraftHandler($hostname, $port, $password);
    }

    /**
     * Execute a command from server rcon
     *
     * Example:
     * <code>
     * $mc->call('list uuids')
     * </code>
     *
     * @param   string  $command    method name
     * @return  array|null
     */
    public function call($command)
    {
        return $this->minecraftHandler->call($command);
    }
}
