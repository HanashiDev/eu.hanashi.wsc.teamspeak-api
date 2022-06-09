<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObject;
use wcf\system\minecraft\IMinecraftHandler;
use wcf\system\minecraft\MinecraftHandler;

/**
 * Minecraft Data class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
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

    /*
     * ObjectIdDatabaseTableColumn $minecraftID
     * VarcharDatabaseTableColumn $name length = 20
     *
     * NotNullVarchar255DatabaseTableColumn $url length = 255
     * EnumDatabaseTableColumn $type ['spigot', 'bungee']
     *
     * VarcharDatabaseTableColumn $user length = 255
     * VarcharDatabaseTableColumn $password length = 255

     * NotNullInt10DatabaseTableColumn $creationDate
     */

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
    public function getConnection(): IMinecraftHandler
    {
        if ($this->connection === null) {
            $this->connection = new MinecraftHandler($this->url, $this->user, $this->password);
        }
        return $this->connection;
    }
}
