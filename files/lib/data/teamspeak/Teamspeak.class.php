<?php
namespace wcf\data\teamspeak;
use wcf\data\DatabaseObject;
use wcf\system\teamspeak\ITeamSpeakHandler;
use wcf\system\teamspeak\TeamSpeakConnectionHandler;
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
    
    /**
     * teamspeak connection
     *
     * @var ITeamSpeakHandler
     */
    protected $connection;
    
    /**
     * getConnection
     *
     * @return ITeamSpeakHandler
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connection = new TeamSpeakConnectionHandler($this->hostname, $this->queryPort, $this->username, $this->password, $this->virtualServerPort, $this->queryType);
            if (!in_array($this->queryType, ['http', 'https'])) {
                $this->connection->use(['port' => $this->virtualServerPort]);
                if (!empty($this->displayName)) {
                    // Wenn Namen vergeben ist, dann hinten eine Nummer dran hängen, maximal 20 Versuche
                    for ($i = 0; $i < 20; $i++) {
                        try {
                            $name = $this->displayName;
                            if ($i > 0) {
                                $name .= $i;
                            }
                            $this->connection->clientupdate(['client_nickname' => $name]);
                            break;
                        } catch (TeamSpeakException $e) {}
                    }
                }
            }
        }
        return $this->connection;
    }
}