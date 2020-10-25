<?php
namespace wcf\system\option;
use wcf\data\option\Option;
use wcf\data\teamspeak\Teamspeak;
use wcf\data\teamspeak\TeamspeakList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * custom option type for multiple teamspeak connections
 * name of option type: TeamSpeakConnectionMultiSelect
 *
 * @author	Peter Lohse <hanashi@hanashi.eu>
 * @copyright	Hanashi
 * @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package	WoltLabSuite\Core\System\Option
 */
class TeamSpeakConnectionMultiSelectOptionType extends AbstractOptionType {
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value) {
        $teamspeakList = new TeamspeakList();
        $teamspeakList->sqlOrderBy = 'connectionName ASC';
        $teamspeakList->readObjects();

        WCF::getTPL()->assign([
			'teamspeakList' => $teamspeakList,
			'option' => $option,
			'value' => !is_array($value) ? explode("\n", $value) : $value
		]);
        return WCF::getTPL()->fetch('teamSpeakConnectionMultiSelectOptionType');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue) {
		if (!is_array($newValue)) $newValue = [];
		$newValue = ArrayUtil::toIntegerArray($newValue);

		$teamspeakList = new TeamspeakList();
		$teamspeakList->setObjectIDs($newValue);
		$teamspeakList->readObjectIDs();
		
		foreach ($newValue as $value) {
            if (!in_array($value, $teamspeakList->objectIDs)) {
                throw new UserInputException($option->optionName);
            }
        }
	}
	
	/**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue) {
		if (!is_array($newValue)) $newValue = [];
		return implode("\n", ArrayUtil::toIntegerArray(StringUtil::unifyNewlines($newValue)));
	}
}
