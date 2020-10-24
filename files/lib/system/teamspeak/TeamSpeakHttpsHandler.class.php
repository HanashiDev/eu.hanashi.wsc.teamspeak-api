<?php
namespace wcf\system\teamspeak;

/**
 * Api for connection with TeamSpeak https query.
 *
 * @author	Peter Lohse <hanashi@hanashi.eu>
 * @copyright	Hanashi
 * @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package	WoltLabSuite\Core\System\TeamSpeak
 */
class TeamSpeakHttpsHandler extends TeamSpeakHttpHandler {	
	/**
	 * @inheritDoc
	 */
	protected $protocol = 'https';
}
