<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use SystemException;
use wcf\data\minecraft\Minecraft;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\flood\FloodControl;
use wcf\system\request\RouteHandler;
use wcf\util\JSON;

/**
 * MinecraftPasswordCheck action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
abstract class AbstractMinecraftAction extends AbstractAction
{
    private string $floodgate = 'de.xxschrarndxx.wsc.minecraft-api.floodgate';

    /**
     * List of available minecraftIDs
     * @var int[]
     */
    protected array $availableMinecraftIDs;

    /**
     * Minecraft ID
     * @var int
     */
    protected int $minecraftID = 0;

    /**
     * Minecraft the request came from.
     * @var Minecraft
     */
    protected Minecraft $minecraft;

    /**
     * Request headers
     * @var false|array
     */
    protected $headers;

    /**
     * Request json data
     * @var array
     */
    protected $json = [];

    /**
     * Returns decoded Request-JSON
     * @return array
     */
    public function getJSON()
    {
        return $this->json;
    }

    /**
     * Returns request data
     * @return string|int
     */
    public function getData(string $name)
    {
        return $this->getJSON()[$name];
    }

    /**
     * @inheritDoc
     */
    public function __run(): ?JsonResponse
    {
        if (!RouteHandler::getInstance()->secureConnection()) {
            return $this->send('SSL Certificate Required', 496);
        }

        // Flood control
        if (MINECRAFT_FLOODGATE_MAXREQUESTS > 0) {
            FloodControl::getInstance()->registerContent($this->floodgate);

            $secs = MINECRAFT_FLOODGATE_RESETTIME * 60;
            $time = \ceil(TIME_NOW / $secs) * $secs;
            $data = FloodControl::getInstance()->countContent($this->floodgate, new \DateInterval('PT' . MINECRAFT_FLOODGATE_RESETTIME . 'M'), $time);
            if ($data['count'] > MINECRAFT_FLOODGATE_MAXREQUESTS) {
                return $this->send('Too Many Requests.', 429, [], ['retryAfter' => $time - TIME_NOW]);
            }
        }

        // validate minecraftID
        if (!array_key_exists('id', $_REQUEST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'id\'.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        $this->minecraftID = (int)$_REQUEST['id'];

        if (isset($this->availableMinecraftIDs)) {
            if (!in_array($this->minecraftID, $this->availableMinecraftIDs)) {
                if (ENABLE_DEBUG_MODE) {
                    return $this->send('Bad Request. Unknown \'Minecraft-Id\'.', 400);
                } else {
                    return $this->send('Bad Request.', 400);
                }
            }
        }

        $this->minecraft = new Minecraft($this->minecraftID);
        if (!$this->minecraft->getObjectID()) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Unknown \'Minecraft-Id\'.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        $this->headers = getallheaders();

        if (!is_array($this->headers) || empty($this->headers)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Could not read headers.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        $result = $this->readHeaders();
        if ($result !== null) {
            return $result;
        }
        $result = $this->readParameters();
        if ($result !== null) {
            return $result;
        }
        try {
            $result = $this->execute();
        } catch (PermissionDeniedException $e) {
            return $this->send($e->getMessage(), 401);
        } catch (IllegalLinkException $e) {
            return $this->send($e->getMessage(), 404);
        }
        if ($result === null) {
            return $this->send('Internal Error.', 500);
        }
        return $result;
    }

    /**
     * Reads header
     * @return ?JsonResponse
     */
    public function readHeaders(): ?JsonResponse
    {
        // validate Authorization
        if (!array_key_exists('Authorization', $this->headers)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'Authorization\' in headers.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        // validate Content-Type
        if (!array_key_exists('Content-Type', $this->headers)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'Content-Type\' in headers.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if ($this->headers['Content-Type'] !== 'application/json') {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Wrong \'Content-Type\'.', 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        return null;
    }

    /**
     * Reads the given parameters.
     * @return ?JsonResponse
     */
    public function readParameters(): ?JsonResponse
    {
        parent::readParameters();

        try {
            $this->json = JSON::decode(file_get_contents('php://input'));
        } catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send($e->getMessage(), 400);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        parent::checkPermissions();

        $auth = \explode(' ', $this->headers['Authorization'], 2);
        if ($auth[0] !== 'Basic') {
            throw new PermissionDeniedException();
        }
        if (!$this->minecraft->check($auth[1])) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Executes this action.
     * @return ?JsonResponse
     */
    public function execute(): ?JsonResponse
    {
        parent::execute();
        return null;
    }

    /**
     * Creates the JSON-Response
     * @param string $status Status-Message
     * @param int $statusCode Status-Code (between {@link JsonResponse::MIN_STATUS_CODE_VALUE} and {@link JsonResponse::MAX_STATUS_CODE_VALUE})
     * @param array $data JSON-Data
     * @param array $headers Headers
     * @param int $encodingOptions {@link JsonResponse::DEFAULT_JSON_FLAGS}
     * @throws Exception\InvalidArgumentException if unable to encode the $data to JSON or not valid $statusCode.
     */
    protected function send(string $status = 'OK', int $statusCode = 200, array $data = [], array $headers = [], int $encodingOptions = JsonResponse::DEFAULT_JSON_FLAGS): JsonResponse
    {
        if (!array_key_exists('status', $data)) {
            $data['status'] = $status;
        }
        if (!array_key_exists('statusCode', $data)) {
            $data['statusCode'] = $statusCode;
        }
        if (!array_key_exists('status', $headers)) {
            $headers['status-message'] = [$status];
        }
        return new JsonResponse($data, $statusCode, $headers, $encodingOptions);
    }
}
