<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
use wcf\system\exception\MinecraftException;
use wcf\system\exception\UserInputException;
use wcf\system\SingletonFactory;

/**
 * MultipleMinecraftHandler abstract class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
abstract class AbstractMultipleMinecraftHandler extends SingletonFactory
{
    /**
     * list of minecraft ids
     * Override with minecraft ids to use.
     *
     * @var array
     */
    protected array $minecraftIDs = [];

    /**
     * list of minecrafts
     *
     * @var array[Minecraft]
     */
    protected array $minecrafts = [];

    /**
     * @inheritDoc
     */
    public function init(): void
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
     * get {@link Minecraft} with given id
     * @param  int $minecraftID
     * @return Minecraft|null
     * @throws InvalidArgumentException Weather Minecraft with id is found
     */
    public function getMinecraft(int $minecraftID): ?Minecraft
    {
        if (empty($this->minecrafts[$minecraftID])) {
            return null;
        } else {
            return $this->minecrafts[$minecraftID];
        }
    }

    /**
     * get list of managed minecraft
     * @return array[Minecraft]
     */
    public function getMinecrafts(): array
    {
        return $this->minecrafts;
    }

    /**
     * call method on minecrafts
     * @param string $httpMethod Method to call
     * @param string $method Method to call
     * @param array $args Arguments for method
     * @param ?int $minecraftID MinecraftID to call
     * @return array|ResponseInterface|null
     * @throws GuzzleException
     * @throws MinecraftException
     * @see \wcf\system\minecraft\IMinecraftHandler#call
     */
    public function call(string $httpMethod, string $method = '', array $args = [], ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $results = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                try {
                    $results[$minecraftID] = $this->call($httpMethod, $method, $args, $minecraftID);
                } catch (GuzzleException | MinecraftException $e) {
                    $results[$minecraftID] = null;
                }
            }
            return $results;
        } else {
            if (empty($this->minecrafts[$minecraftID])) {
                return null;
            } else {
                return $this->minecrafts[$minecraftID]->getConnection()->call($httpMethod, $method, $args);
            }
        }
    }

    /**
     * call request on minecrafts
     * @param RequestInterface $request Request to call
     * @param ?int $minecraftID MinecraftID to call
     * @return array|ResponseInterface|null
     * @throws GuzzleException
     * @throws MinecraftException
     * @see \wcf\system\minecraft\IMinecraftHandler#callRequest
     */
    public function callRequest(RequestInterface $request, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $results = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                try {
                    $results[$minecraftID] = $this->callRequest($request, $minecraftID);
                } catch (GuzzleException | MinecraftException $e) {
                    $results[$minecraftID] = null;
                }
            }
            return $results;
        } else {
            if (empty($this->minecrafts[$minecraftID])) {
                return null;
            } else {
                return $this->minecrafts[$minecraftID]->getConnection()->callRequest($request);
            }
        }
    }
}
