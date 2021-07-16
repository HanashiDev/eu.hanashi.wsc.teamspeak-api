<?php

namespace wcf\acp\form;

use wcf\data\teamspeak\Teamspeak;
use wcf\system\exception\IllegalLinkException;

class TeamspeakEditForm extends TeamspeakAddForm
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

        $teamspeakID = 0;
        if (isset($_REQUEST['id'])) {
            $teamspeakID = (int)$_REQUEST['id'];
        }
        $this->formObject = new Teamspeak($teamspeakID);
        if (!$this->formObject->teamspeakID) {
            throw new IllegalLinkException();
        }
    }
}
