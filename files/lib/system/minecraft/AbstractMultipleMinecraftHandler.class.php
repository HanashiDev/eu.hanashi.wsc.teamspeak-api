<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\RequestInterface;
use wcf\data\minecraft\Minecraft;
use wcf\data\minecraft\MinecraftList;
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
     * @var Minecraft[]
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
     * @return Minecraft
     * @throws InvalidArgumentException Weather Minecraft with id is found
     */
    public function getMinecraft(int $minecraftID): Minecraft
    {
        if (!array_key_exists($minecraftID, $this->getMinecrafts())) {
            throw new InvalidArgumentException("Unknown server with id " . $minecraftID);
        }
        return $this->getMinecrafts()[$minecraftID];
    }

    /**
     * get list of managed minecraft
     * @return Minecraft[]
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
     * @return ResponseInterface|ResponseInterface[] weather minecraftID is returns one or multiple
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @see \wcf\system\minecraft\IMinecraftHandler#call(string $httpMethod, string $method = '', array $args = [])
     */
    public function call(string $httpMethod, string $method = '', array $args = [], ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $results = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                try {
                    $results[$minecraftID] = $this->call($httpMethod, $method, $args, $minecraftID);
                } catch (GuzzleException | InvalidArgumentException $e) {
                    $results[$minecraftID] = new JsonResponse(['status' => $e->getMessage(), 'statusCode' => $e->getCode()], $e->getCode());
                }
            }
            return $results;
        } else {
            return $this->getMinecraft($minecraftID)->getConnection()->call($httpMethod, $method, $args);
        }
    }

    /**
     * call request on minecrafts
     * @param RequestInterface $request Request to call
     * @param ?int $minecraftID MinecraftID to call
     * @return ResponseInterface|ResponseInterface[] weather minecraftID is returns one or multiple
     * @throws GuzzleException
     * @see \wcf\system\minecraft\IMinecraftHandler#callRequest(RequestInterface $request)
     */
    public function callRequest(RequestInterface $request, ?int $minecraftID = null)
    {
        if ($minecraftID === null) {
            $results = [];
            foreach ($this->minecraftIDs as $minecraftID) {
                try {
                    $results[$minecraftID] = $this->callRequest($request, $minecraftID);
                } catch (GuzzleException | InvalidArgumentException $e) {
                    $results[$minecraftID] = new JsonResponse(['status' => $e->getMessage(), 'statusCode' => $e->getCode()], $e->getCode());
                }
            }
            return $results;
        } else {
            return $this->getMinecraft($minecraftID)->getConnection()->callRequest($request);
        }
    }

    /**
     * Broadcasts the given message
     * @param string $message The message to send
     * @param string $hover The hovermessage to show
     * @param ?int $minecraftID MinecraftID to call
     * @return ResponseInterface|ResponseInterface[] weather minecraftID is returns one or multiple
     * @throws GuzzleException
     * @see \wcf\system\minecraft\AbstractMultipleMinecraftHandler#call(string $httpMethod, string $method = '', array $args = [], ?int $minecraftID = null)
     */
    public function broadcast(string $message, string $hover, ?int $minecraftID = null)
    {
        return $this->call('POST', 'broadcast', [
            'message' => $message,
            'hover' => $hover
        ], $minecraftID);
    }

    /**
     * Sends the given message to given uuid
     * @param string $uuid
     * @param string $message The message to send
     * @param string $hover
     * @param ?int $minecraftID MinecraftID to call
     * @return ResponseInterface|ResponseInterface[] weather minecraftID is returns one or multiple
     * @throws GuzzleException
     * @see \wcf\system\minecraft\AbstractMultipleMinecraftHandler#call(string $httpMethod, string $method = '', array $args = [], ?int $minecraftID = null)
     */
    public function message(string $uuid, string $message, string $hover = '', ?int $minecraftID = null)
    {
        return $this->call('POST', 'message', [
            'uuid' => $uuid,
            'message' => $message,
            'hover' => $hover
        ], $minecraftID);
    }
}
