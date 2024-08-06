<?php

namespace wcf\data\teamspeak;

use Override;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * TeamSpeak data action class
 *
 * @author   Peter Lohse <hanashi@hanashi.eu>
 * @copyright    Hanashi
 * @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package  WoltLabSuite\Core\Data\TeamSpeak
 *
 * @method  TeamspeakEditor[]    getObjects()
 * @method  TeamspeakEditor  getSingleObject()
 */
final class TeamspeakAction extends AbstractDatabaseObjectAction
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

    #[Override]
    public function update()
    {
        if (isset($this->parameters['data']['password']) && empty($this->parameters['data']['password'])) {
            unset($this->parameters['data']['password']);
        }

        parent::update();
    }
}
