<?php

namespace wcf\system\minecraft;

class MinecraftConnectionHandler
{
    /**
     * server rcon object
     *
     * @var MinecraftHandler
     */
    protected $minecraftHandler;

    /**
     * construct for Minecraft class
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
     * execute a command from server rcon
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
