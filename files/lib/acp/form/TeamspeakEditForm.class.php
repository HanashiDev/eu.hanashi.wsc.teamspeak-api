<?php

namespace wcf\acp\form;

use CuyZ\Valinor\Mapper\MappingError;
use Override;
use wcf\data\teamspeak\Teamspeak;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;

class TeamspeakEditForm extends TeamspeakAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    #[Override]
    public function readParameters()
    {
        parent::readParameters();

        try {
            $queryParameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
            $this->formObject = new Teamspeak($queryParameters['id']);

            if (!$this->formObject->teamspeakID) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }
}
