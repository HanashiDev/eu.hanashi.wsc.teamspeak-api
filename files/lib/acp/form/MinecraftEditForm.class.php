<?php

namespace wcf\acp\form;

use wcf\data\minecraft\Minecraft;
use wcf\system\exception\IllegalLinkException;

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
