<?php

namespace wcf\acp\form;

use wcf\system\WCF;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\MinecraftException;
use wcf\system\minecraft\MinecraftConnectionHandler;
use wcf\data\minecraft\Minecraft;
use wcf\form\AbstractForm;

/**
 * MinecraftConsole Form class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftConsoleForm extends AbstractForm
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
    public $command;

    /**
     * @inheritDoc
     */
    public $response;

    public $proxyDebug;

    /**
     * The MinecraftConnectionHandler for the Action.
     * @var MinecraftConnectionHandler
     */
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
            $this->errorType = 'cantConnect';
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
        } catch (\Exception $e) {
            $this->errorType = 'cantConnect';
            if (\ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
        }
        if (\ENABLE_DEBUG_MODE && isset($this->connection->minecraftHandler->proxyDebug)) {
            $this->proxyDebug = $this->connection->minecraftHandler->proxyDebug;
        }
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (isset($_POST['command'])) {
            $tmpResponse = [];
            $command = $_POST['command'];
            try {
                $tmpResponse = $this->connection->call($command);
            } catch (MinecraftException $e) {
                $this->errorType = 'cantConnect';
                if (\ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
            }
            if (empty($tmpResponse)) {
                $this->errorType = 'cantConnect';
            } else {
                if ($tmpResponse['Response'] == 0) {
                    foreach ($tmpResponse as $key => $value) {
                        if ($key == 'Response' || $key == 'Length') {
                            continue;
                        }
                        if ($this->response == null) {
                            $this->response = $value;
                        } else {
                            $this->response .= '\n' . $value;
                        }
                    }
                } else {
                    $this->errorType = 'cantRead';
                }
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
            'proxyDebug' => $this->proxyDebug,
            'response' => $this->response
        ]);
    }
}
