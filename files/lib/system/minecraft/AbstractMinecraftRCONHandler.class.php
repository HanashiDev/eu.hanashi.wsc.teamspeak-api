<?php

namespace wcf\system\minecraft;

/**
 * MinecraftRCONHandler abstract class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
abstract class AbstractMinecraftRCONHandler implements IMinecraftHandler
{

    /**
     * the hostname/ip of your Minecraft server
     *
     * @var string
     */
    protected $hostname;

    /**
     * the server rcon port of your Minecraft server (standard = 25575)
     *
     * @var int
     */
    protected $port;

    /**
     * Password of server rcon
     *
     * @var string
     */
    protected $password;

    /**
     * @inheritDoc
     */
    public function __construct($hostname, $port, $password)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->password = $password;

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function __destruct()
    {
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
        $this->login();
    }

    /**
     * @inheritDoc
     */
    public function login()
    {
    }

    /**
     * @inheritDoc
     */
    public function parseResult()
    {
    }

    /**
     * @inheritDoc
     */
    public function execute($command)
    {
    }

    /**
     * @inheritDoc
     */
    public function call($command)
    {
        $this->execute($command);
    }
}
