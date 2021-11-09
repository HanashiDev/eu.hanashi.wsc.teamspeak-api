<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * custom option type for minecraft connections
 * name of option type: MinecraftConnectionSelect
 */
class MinecraftConnectionSelectOptionType extends AbstractOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value)
    {
        $minecraftList = new MinecraftList();
        $minecraftList->sqlOrderBy = 'connectionName ASC';
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
            if (!$minecraft->minecraftID) {
                throw new UserInputException($option->optionName);
            }
        }
    }
}
