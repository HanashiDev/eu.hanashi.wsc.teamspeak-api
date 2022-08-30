<?php

namespace wcf\data\minecraft;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\JSON;
use wcf\util\StringUtil;

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
    protected $className = MinecraftEditor::class;

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
     * list of permissions required to check status
     * @var string[]
     */
    protected $permissionsCheckStatus = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update', 'checkStatus'];

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
