<?php

namespace wcf\data\minecraft;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\util\CryptoUtil;

/**
 * Minecraft Action class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
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
    public function create()
    {
        if (!CryptoUtil::validateSignedString($this->parameters['data']['password'])) {
            $this->parameters['data']['password'] = CryptoUtil::createSignedString($this->parameters['data']['password']);
        }

        parent::create();
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        if (isset($this->parameters['data']['password'])) {
            if (empty($this->parameters['data']['password'])) {
                unset($this->parameters['data']['password']);
            } else if (!CryptoUtil::validateSignedString($this->parameters['data']['password'])) {
                $this->parameters['data']['password'] = CryptoUtil::createSignedString($this->parameters['data']['password']);
            }
        }

        parent::update();
    }
}
