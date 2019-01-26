<?php
namespace wcf\system\teamspeak;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\SingletonFactory;

/**
* Handler for saved TeamSpeak connection
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\System\TeamSpeak
*/
abstract class AbstractTeamSpeakHandler extends SingletonFactory {
    /**
     * ID of saved TeamSpeak connection
     * 
     * @var int
     */
    protected $teamspeakID;

    /**
     * TeamSpeak server instance
     * 
     * @var TeamSpeak
     */
    protected $tsObj;

    /**
     * @inheritDoc
     */
    public function init() {
        $teamspeak = new Teamspeak($this->teamspeakID);
        if (!$teamspeak->teamspeakID || $teamspeak->teamspeakID != $this->teamspeakID) return;

        $this->tsObj = new TeamSpeak($teamspeak->hostname, $teamspeak->queryPort, $teamspeak->username, $teamspeak->password, $teamspeak->queryType);
        $this->tsObj->use(['port' => $teamspeak->virtualServerPort]);
    }

    /**
     * Magic Method since PHP 5
     * 
     * @param   string      $method     method name to execute
     * @param   array       $args       method parameters
     * @return  boolean|null
     * @throws  TeamSpeak3Exception
     */
    public function __call($method, $args) {
        if (count($args) > 0) {
            return $this->tsObj->$method($args[0]);
        } else {
            return $this->tsObj->$method();
        }
    }

    /**
     * get TS server instance
     */
    public function getTS() {
        return $this->tsObj;
    }
}
