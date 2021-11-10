<?php

namespace wcf\acp\page;

use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\MinecraftConnectionHandler;
use wcf\data\minecraft\Minecraft;
use wcf\page\AbstractPage;

/**
 * MinecraftConsole Page class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Page
 */
class MinecraftConsolePage extends AbstractPage
{

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList';

    /**
     * @inheritDoc
     */
    public $minecraft;

    /**
     * @inheritDoc
     */
    public $error = false;

    /**
     * @inheritDoc
     */
    public $command;

    /**
     * @inheritDoc
     */
    public $response;

    public $connection;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $minecraftID = (int)$_REQUEST['id'];
        }
        if (!isset($minecraftID)) {
            throw new IllegalLinkException();
        }

        $this->minecraft = new Minecraft($minecraftID);
        if (!$this->minecraft->minecraftID) {
            throw new IllegalLinkException();
        }

        try {
            $this->connection = $this->minecraft->getConnection();
        } catch (MinecraftException $e) {
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            $this->error = true;
        } catch (\Exception $e) {
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            $this->error = true;
        }

        if (isset($_POST['command'])) {
            try {
                $this->response = $this->connection->call($_POST['command']);
            } catch (MinecraftException $e) {
                if (\ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
                $this->error = true;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'minecraftID' => $this->minecraft->minecraftID,
            'connectionName' => $this->minecraft->connectionName,
            'response' => $this->response,
            'error' => $this->error
        ]);
    }
}
