<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObjectEditor;

/**
 * Minecraft Editor class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
class MinecraftEditor extends DatabaseObjectEditor implements Minecraft
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Minecraft::class;
}
