<?php

namespace wcf\data\teamspeak;

use wcf\data\AbstractDatabaseObjectAction;

/**
* TeamSpeak data action class
*
* @author   Peter Lohse <hanashi@hanashi.eu>
* @copyright    Hanashi
* @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package  WoltLabSuite\Core\Data\TeamSpeak
*/
class TeamspeakAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.teamspeak.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.teamspeak.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.teamspeak.canManageConnection'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];
}
