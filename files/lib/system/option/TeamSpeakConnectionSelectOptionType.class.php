<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\teamspeak\Teamspeak;
use wcf\data\teamspeak\TeamspeakList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * custom option type for teamspeak connections
 * name of option type: TeamSpeakConnectionSelect
 *
 * @author  Peter Lohse <hanashi@hanashi.eu>
 * @copyright   Hanashi
 * @license Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package WoltLabSuite\Core\System\Option
 */
final class TeamSpeakConnectionSelectOptionType extends AbstractOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        $teamspeakList = new TeamspeakList();
        $teamspeakList->sqlOrderBy = 'connectionName ASC';
        $teamspeakList->readObjects();

        WCF::getTPL()->assign([
            'teamspeakList' => $teamspeakList,
            'option' => $option,
            'value' => $value,
        ]);

        return WCF::getTPL()->fetch('teamSpeakConnectionSelectOptionType');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!empty($newValue)) {
            $teamspeak = new Teamspeak($newValue);
            if (!$teamspeak->teamspeakID) {
                throw new UserInputException($option->optionName);
            }
        }
    }
}
