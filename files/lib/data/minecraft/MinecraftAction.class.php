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

    // TODO turn response into html
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
                $responseBody = $response->getBody()->getContents();
                $responses[$minecraftID] = JSON::decode($responseBody);
            } catch (ClientException $e) {
                switch ($e->getCode()) {
                    case 429:
                        if ($e->hasResponse()) {
                            $time = DateUtil::getDateTimeByTimestamp(TIME_NOW + (int) $e->getResponse()->getHeaderLine('Retry-After'));
                            $responses[$minecraftID] = [
                                'status' => $e->getMessage() . " wait until " . DateUtil::format($time, DateUtil::TIME_FORMAT),
                                'statusCode' => $e->getCode(),
                                'Retry-After' => DateUtil::format($time, DateUtil::TIME_FORMAT)
                            ];
                        } else {
                            $responses[$minecraftID] =  [
                                'status' => $e->getMessage(),
                                'statusCode' => $e->getCode()
                            ];
                        }
                        break;
                    default:
                        $responses[$minecraftID] = [
                            'status' => $e->getMessage(),
                            'statusCode' => $e->getCode()
                        ];
                        break;
                }
            } catch (GuzzleException $e) {
                $responses[$minecraftID] = [
                    'status' => $e->getMessage(),
                    'statusCode' => $e->getCode()
                ];
            } catch (SystemException $e) {
                $responses[$minecraftID] = [
                    'status' => $e->getMessage(),
                    'statusCode' => $e->getCode()
                ];
            }
        }
        $templates = [];
        foreach ($responses as $minecraftID => $response) {
            $variables = $response;
            $variables['minecraftID'] = $minecraftID;
            $templates[$minecraftID] = WCF::getTPL()->fetch('minecraftStatus', 'wcf', $variables);
        }
        return $templates;
    }
}
