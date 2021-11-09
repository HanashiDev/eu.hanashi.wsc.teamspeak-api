<?php

namespace wcf\acp\form;

use wcf\data\minecraft\Minecraft;
use wcf\system\exception\IllegalLinkException;

/**
 * MinecraftEdit Form class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftEditForm extends MinecraftAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $minecraftID = 0;
        if (isset($_REQUEST['id'])) {
            $minecraftID = (int)$_REQUEST['id'];
        }
        $this->formObject = new Minecraft($minecraftID);
        if (!$this->formObject->minecraftID) {
            throw new IllegalLinkException();
        }
    }
}
