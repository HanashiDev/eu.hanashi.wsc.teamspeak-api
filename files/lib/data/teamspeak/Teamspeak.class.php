<?php
namespace wcf\data\teamspeak;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
* TeamSpeak data class
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\Data\TeamSpeak
*/
class Teamspeak extends DatabaseObject {
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'teamspeak';
    
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'teamspeakID';
}