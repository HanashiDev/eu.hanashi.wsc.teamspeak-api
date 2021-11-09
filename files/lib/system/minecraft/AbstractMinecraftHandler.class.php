<?php

namespace wcf\system\minecraft;

use wcf\data\minecraft\Minecraft;
use wcf\system\SingletonFactory;

abstract class AbstractMinecraftHandler extends SingletonFactory
{
    /**
     * ID of saved Minecraft connection
     *
     * @var int
     */
    protected $minecraftID;

    /**
     * Minecraft server instance
     *
     * @var Minecraft
     */
    protected $mcObj;

    /**
     * hostname of Minecraft server
     *
     * @var string
     */
    protected $hostname;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $minecraft = new Minecraft($this->minecraftID);
        if (!$minecraft->minecraftID || $minecraft->minecraftID != $this->minecraftID) {
            return;
        }

        $this->hostname = $minecraft->hostname;
        $this->mcObj = new \wcf\system\minecraft\MinecraftConnectionHandler($minecraft->hostname, $minecraft->rconPort, $minecraft->password);

    }

    /**
     * get MC server instance
     */
    public function getMC()
    {
        return $this->mcObj;
    }

}
