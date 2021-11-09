<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;

class MinecraftAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    /**
     * @inheritDoc
     */
    public function update()
    {
        if (isset($this->parameters['data']['password']) && empty($this->parameters['data']['password'])) {
            unset($this->parameters['data']['password']);
        }

        parent::update();
    }
}
