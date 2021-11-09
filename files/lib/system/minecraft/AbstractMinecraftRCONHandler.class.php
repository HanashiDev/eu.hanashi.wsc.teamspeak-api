<?php

namespace wcf\system\minecraft;

abstract class AbstractMinecraftRCONHandler implements IMinecraftHandler
{

    /**
     * the hostname/ip of your Minecraft server
     *
     * @var string
     */
    protected $hostname;

    /**
     * the server rcon port of your Minecraft server (standard: raw = 10011; ssh = 10022)
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
    public function __construct($hostname, $port, $password) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->password = $password;

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function __destruct() { }

    /**
     * @inheritDoc
     */
    public function connect() { }

    /**
     * @inheritDoc
     */
    public function login($password) { }

    /**
     * @inheritDoc
     */
    public function parseResult() { }

    /**
     * @inheritDoc
     */
    public function execute($command) { }

    /**
     * @inheritDoc
     */
    public function call($command) { }
}
