<?php

namespace wcf\acp\page;

use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\page\MultipleLinkPage;

/**
 * MinecraftList Page class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Page
 */
class MinecraftListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftList::class;

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList';

    /**
     * @inheritDoc
     */
    public function __run()
    {
        $this->sortField = Minecraft::getDatabaseTableIndexName();
        parent::__run();
    }
}
