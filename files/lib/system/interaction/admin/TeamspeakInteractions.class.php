<?php

namespace wcf\system\interaction\admin;

use Override;
use wcf\data\teamspeak\Teamspeak;
use wcf\event\interaction\admin\TeamspeakInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;

final class TeamspeakInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new DeleteInteraction('hanashi/teamspeak/%s'),
        ]);

        EventHandler::getInstance()->fire(
            new TeamspeakInteractionCollecting($this)
        );
    }

    #[Override]
    public function getObjectClassName(): string
    {
        return Teamspeak::class;
    }
}
