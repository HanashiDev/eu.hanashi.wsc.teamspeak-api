<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;

/**
 * MinecraftConnectionMultiSelect OptionType class
 * Custom option type for multiple minecraft connections
 * Name of option type: MinecraftConnectionMultiSelect
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Option
 */
class MinecraftConnectionMultiSelectOptionType extends AbstractOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        $minecraftList = new MinecraftList();
        $minecraftList->sqlOrderBy = 'title ASC';
        $minecraftList->readObjects();

        WCF::getTPL()->assign([
            'minecraftList' => $minecraftList,
            'option' => $option,
            'value' => !is_array($value) ? explode("\n", $value) : $value
        ]);
        return WCF::getTPL()->fetch('minecraftConnectionMultiSelectOptionType');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }
        $newValue = ArrayUtil::toIntegerArray($newValue);

        $minecraftList = new MinecraftList();
        $minecraftList->setObjectIDs($newValue);
        $minecraftList->readObjectIDs();

        foreach ($newValue as $value) {
            if (!\in_array($value, $minecraftList->getObjectIDs())) {
                throw new UserInputException($option->optionName);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue)
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }
        return \implode("\n", ArrayUtil::toIntegerArray(StringUtil::unifyNewlines($newValue)));
    }
}
