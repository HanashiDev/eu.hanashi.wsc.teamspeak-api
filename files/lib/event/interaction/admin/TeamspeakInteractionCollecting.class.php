<?php

namespace wcf\event\interaction\admin;

use wcf\event\IPsr14Event;
use wcf\system\interaction\admin\TeamspeakInteractions;

final class TeamspeakInteractionCollecting implements IPsr14Event
{
    public function __construct(public readonly TeamspeakInteractions $provider)
    {
    }
}
