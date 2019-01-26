<?php
namespace wcf\acp\page;
use wcf\data\teamspeak\TeamspeakList;
use wcf\page\MultipleLinkPage;

/**
* list page for all saved teamspeak connections
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\Acp\Page
*/
class TeamspeakListPage extends MultipleLinkPage {
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = TeamspeakList::class;
	
	/**
	 * @inheritDoc
	 */
	public $sortField = 'teamspeakID';
	
	/**
	 * @inheritDoc
	 */
	public $sortOrder = 'ASC';
	
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.configuration.teamspeak.teamspeakList';
}