<?php

namespace wcf\acp\page;

use Override;
use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\TeamspeakGridView;

/**
 * list page for all saved teamspeak connections
 *
 * @author   Peter Lohse <hanashi@hanashi.eu>
 * @copyright    Hanashi
 * @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package  WoltLabSuite\Core\Acp\Page
 */
final class TeamspeakListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.teamspeak.canManageConnection'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.teamspeak.teamspeakList';

    #[Override]
    protected function createGridView(): AbstractGridView
    {
        return new TeamspeakGridView();
    }
}
