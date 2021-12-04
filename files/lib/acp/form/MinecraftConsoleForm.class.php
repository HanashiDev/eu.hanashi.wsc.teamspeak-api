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

    public $rsp;

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
        if (\ENABLE_DEBUG_MODE && isset($this->connection->minecraftHandler->rsp)) {
            $this->rsp = $this->connection->minecraftHandler->rsp;
        }
    }

    /**
     * @inheritDoc
     */
    public function readFormParameters()
    {
        if (isset($_POST['command'])) {
            $tmpResponse = [];
            try {
                $tmpResponse = $this->connection->call($_POST['command']);
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
                    $this->response = $tmpResponse['S1'];
                    if (!empty($tmpResponse['S2'])) {
                        $this->response = $this->response . '\n' . $tmpResponse['S2'];
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
            'rsp' => $this->rsp,
            'response' => $this->response
        ]);
    }
}
