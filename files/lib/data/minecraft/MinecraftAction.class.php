<?php

namespace wcf\data\minecraft;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\SystemException;
use wcf\system\registry\RegistryHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;

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
    public function delete()
    {
        parent::delete();

        foreach ($this->getObjects() as $object) {
            $url = $object->url;
            $minecraftList = new MinecraftList();
            $minecraftList->getConditionBuilder()->add('url = ?', [$url]);
            $minecraftList->readObjectIDs();
            if (empty($minecraftList->getObjectIDs())) {
                $regHandler = RegistryHandler::getInstance();
                $package = "de.xxschrandxx.wsc.minecraft-api";
                $field = "minecraftFloodgate-$url";
                $regHandler->delete($package, $field);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        if (isset($this->parameters['data']['password']) && empty($this->parameters['data']['password'])) {
            unset($this->parameters['data']['password']);
        }

        parent::update();

        foreach ($this->getObjects() as $object) {
            $url = $object->url;
            $minecraftList = new MinecraftList();
            $minecraftList->getConditionBuilder()->add('url = ?', [$url]);
            $minecraftList->readObjectIDs();
            if (empty($minecraftList->getObjectIDs())) {
                $regHandler = RegistryHandler::getInstance();
                $package = "de.xxschrandxx.wsc.minecraft-api";
                $field = "minecraftFloodgate-$url";
                $regHandler->delete($package, $field);
            }
        }
    }

    /**
     * Validates permissions.
     */
    public function validateCheckStatus()
    {
        // validate permissions
        if (\is_array($this->permissionsCheckStatus) && !empty($this->permissionsCheckStatus)) {
            WCF::getSession()->checkPermissions($this->permissionsCheckStatus);
        } else {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Checking status of server.
     * @return array
     */
    public function checkStatus()
    {
        $responses = [];
        foreach ($this->getObjectIDs() as $minecraftID) {
            try {
                $minecraft = new Minecraft($minecraftID);
                $handler = $minecraft->getConnection();
                $response = $handler->call('GET');
// TODO turn into html
//                $responseBody = JSON::decode($response->getBody());
                $responseBody = $response->getBody()->getContents();
                $responses[$minecraftID] = $responseBody;
            } catch (ClientException $e) {
                switch ($e->getCode()) {
                    case 401:
                        $responses[$minecraftID] = "{'status': '" . $e->getMessage() . "', 'statusCode': " . $e->getCode() . "}";
                        break;
                    case 429:
                        if ($e->hasResponse()) {
                            $time = DateUtil::getDateTimeByTimestamp(TIME_NOW + (int) $e->getResponse()->getHeaderLine('Retry-After'));
                            $responses[$minecraftID] = "{'status': '" . $e->getMessage() . " wait until " . DateUtil::format($time, DateUtil::TIME_FORMAT) . "', 'statusCode': " . $e->getCode() . "}";
                        } else {
                            $responses[$minecraftID] = "{'status': '" . $e->getMessage() . "', 'statusCode': " . $e->getCode() . "}";
                        }
                        break;
                    default:
                        $responses[$minecraftID] = "{'status': '" . $e->getMessage() . "', 'statusCode': " . $e->getCode() . "}";
                        break;
                }
            } catch (GuzzleException $e) {
                $responses[$minecraftID] = "{'status': '" . $e->getMessage() . "', 'statusCode': " . $e->getCode() . "}";
            }
        }
        return $responses;
    }
}
