<?php

namespace wcf\system\teamspeak;

use wcf\data\teamspeak\TeamspeakList;
use wcf\system\exception\TeamSpeakException;
use wcf\system\SingletonFactory;

abstract class AbstractMultipleTeamSpeakHandler extends SingletonFactory
{
    /**
     * list of teamspeak ids
     *
     * @var array
     */
    protected $teamspeakIDs = [];

    /**
     * list of teamspeaks
     *
     * @var array
     */
    protected $teamspeaks = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->teamspeakIDs)) {
            return;
        }

        $teamspeakList = new TeamSpeakList();
        $teamspeakList->setObjectIDs($this->teamspeakIDs);
        $teamspeakList->readObjects();
        $this->teamspeaks = $teamspeakList->getObjects();
    }

    /**
     * get teamspeak
     *
     * @param  int $teamspeakID
     * @return Teamspeak
     */
    public function getTeamspeak($teamspeakID)
    {
        if (empty($this->teamspeaks[$teamspeakID])) {
            if (ENABLE_DEBUG_MODE) {
                throw new TeamSpeakException('found no teamspeak with this id');
            }

            return null;
        }

        return $this->teamspeaks[$teamspeakID];
    }

    public function getTeamspeaks()
    {
        return $this->teamspeaks;
    }

    /**
     * execute a method
     *
     * @param  int $teamspeakID
     * @param  string $method
     * @param  array $args
     * @return array
     */
    public function execute($teamspeakID, $method, $args = [])
    {
        if (empty($this->teamspeaks[$teamspeakID])) {
            if (ENABLE_DEBUG_MODE) {
                throw new TeamSpeakException('found no teamspeak with this id');
            }

            return [];
        }

        if (\count($args) > 0) {
            return $this->teamspeaks[$teamspeakID]->getConnection()->{$method}($args[0]);
        } else {
            return $this->teamspeaks[$teamspeakID]->getConnection()->{$method}();
        }
    }
}
