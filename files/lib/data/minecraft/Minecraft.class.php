<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObject;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\IMinecraftHandler;
use wcf\system\minecraft\MinecraftConnectionHandler;

class Minecraft extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftID';

    /**
     * minecrraft connection
     *
     * @var IMinecraftHandler
     */
    protected $connection;

    /**
     * getConnection
     *
     * @return IMinecraftHandler
     */
    public function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = new MinecraftConnectionHandler($this->hostname, $this->rconPort, $this->password);
        }
        return $this->connection;
    }
}
