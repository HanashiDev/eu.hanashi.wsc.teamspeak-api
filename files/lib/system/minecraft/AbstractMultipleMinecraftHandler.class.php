<?php

namespace wcf\system\minecraft;

use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\MinecraftException;
use wcf\system\SingletonFactory;

abstract class AbstractMultipleMinecraftHandler extends SingletonFactory
{

    /**
     * list of minecraft ids
     *
     * @var array
     */
    protected $minecraftIDs = [];

    /**
     * list of minecrafts
     *
     * @var array
     */
    protected $minecrafts = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->minecraftIDs)) {
            return;
        }

        $minecraftList = new MinecraftList();
        $minecraftList->setObjectIDs($this->minecraftIDs);
        $minecraftList->readObjects();
        $this->minecrafts = $minecraftList->getObjects();
    }

    /**
     * get minecraft
     *
     * @param  int $minecraftID
     * @return Minecraft
     */
    public function getMinecraft($minecraftID)
    {
        if (empty($this->minecrafts[$minecraftID])) {
            if (ENABLE_DEBUG_MODE) {
                throw new MinecraftException('found no minecraft with this id');
            }
            return null;
        }
        return $this->minecrafts[$minecraftID];
    }

    public function getMinecrafts()
    {
        return $this->minecrafts;
    }

    /**
     * execute a command
     *
     * @param  int $minecraftID
     * @param  string $command
     * @return array
     */
    public function execute($minecraftID, $command)
    {
        if (empty($this->minecrafts[$minecraftID])) {
            if (ENABLE_DEBUG_MODE) {
                throw new MinecraftException('found no minecraft with this id');
            }
            return [];
        }

        return $this->minecrafts[$minecraftID]->getConnection()->call($command);
    }
}
