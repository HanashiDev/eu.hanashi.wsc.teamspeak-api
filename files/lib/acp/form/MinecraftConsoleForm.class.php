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
     * Minecraft
     */
    public $minecraft;

    /**
     * Command to execute.
     */
    public $command;

    /**
     * Response from server.
     */
    public $response;

    /**
     * Proxy Debug information.
     */
    public $proxyDebug;

    /**
     * Exception Message.
     */
    public $errorMessage;

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
                $connection = $this->minecraft->getConnection();
                $tmpResponse = $connection->call($command);
            } catch (MinecraftException $e) {
                switch ($e->getCode()) {
                    case 100:
                        $this->errorType = 'proxyError';
                        break;
                    default:
                        $this->errorType = 'cantConnect';
                        break;
                }
                $this->errorField = 'command';
                $this->errorMessage = $e->getMessage();
                if (\ENABLE_DEBUG_MODE) {
                    \wcf\functions\exception\logThrowable($e);
                }
            }
            if (\ENABLE_DEBUG_MODE && isset($connection)) {
                if (isset($connection->minecraftHandler->proxyDebug)) {
                    $this->proxyDebug = $connection->minecraftHandler->proxyDebug;
                }
            }
            if (empty($tmpResponse)) {
                $this->errorType = 'cantConnect';
                $this->errorField = 'command';
            } else {
                if ($tmpResponse['Response'] == 0) {
                    $this->response = $tmpResponse['CMD'];
                } else {
                    $this->errorType = 'cantRead';
                    $this->errorField = 'command';
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
            'errorMessage' => $this->errorMessage,
            'minecraftID' => $this->minecraft->minecraftID,
            'connectionName' => $this->minecraft->connectionName,
            'proxyDebug' => $this->proxyDebug,
            'response' => $this->response
        ]);
    }
}
