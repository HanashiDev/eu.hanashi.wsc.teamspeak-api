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
     * VarcharDatabaseTableColumn $user length = 255
     * VarcharDatabaseTableColumn $password length = 255

     * NotNullInt10DatabaseTableColumn $creationDate
     */

}
