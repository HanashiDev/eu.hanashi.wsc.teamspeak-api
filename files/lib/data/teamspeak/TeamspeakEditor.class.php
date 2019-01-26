<?php
namespace wcf\data\teamspeak;
use wcf\data\DatabaseObjectEditor;

/**
* TeamSpeak data editor class
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\Data\TeamSpeak
*/
class TeamspeakEditor extends DatabaseObjectEditor {
    /**
     * @inheritDoc
     */
    protected static $baseClass = Teamspeak::class;
}