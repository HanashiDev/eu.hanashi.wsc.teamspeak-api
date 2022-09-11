<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * MinecraftConnectionSelect OptionType class
 * Custom option type for minecraft connections
 * Name of option type: MinecraftConnectionSelect
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Option
 */
class MinecraftConnectionSelectOptionType extends AbstractOptionType
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
            'value' => $value
        ]);
        return WCF::getTPL()->fetch('minecraftConnectionSelectOptionType');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue)
    {
        if (!empty($newValue)) {
            $minecraft = new Minecraft($newValue);
            if (!$minecraft->getObjectID()) {
                throw new UserInputException($option->optionName);
            }
        }
    }
}
