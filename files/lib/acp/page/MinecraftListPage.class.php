<?php

namespace wcf\acp\page;

use wcf\data\minecraft\MinecraftList;
use wcf\page\MultipleLinkPage;

class MinecraftListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $objectListClassName = MinecraftList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'minecraftID';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList';
}
