<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Stdlib\ResponseInterface;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\flood\FloodControl;
use wcf\system\request\RouteHandler;

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
    protected int $minecraftID;

    /**
     * Minecraft the request came from.
     * @var Minecraft
     */
    protected Minecraft $minecraft;

    /**
     * @inheritDoc
     */
    public function __run()
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

        return parent::__run();
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // validate minecraftID
        if (!array_key_exists('minecraftID', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'minecraftID\'.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        $this->minecraftID = intval($_POST['minecraftID']);
        if ($this->minecraftID === 0) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'minecraftID\' is no int.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }

        if (!in_array($this->minecraftID, $this->availableMinecraftIDs)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Requests not enabled for given \'minecraftID\'.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }

        // set minecraft
        $this->minecraft = new Minecraft($this->minecraftID);
        if ($this->minecraft === null) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Unknown \'minecraftID\'.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }

        // validate user
        if (!array_key_exists('user', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'user\'.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!is_string($_POST['user'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'user\' is no string.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }

        // validate password
        if (!array_key_exists('password', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'password\'.', 401);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
        if (!is_string($_POST['password'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'password\' no string.', 401);
            } else {
                return $this->send('Bad Request.', 400);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPermissions()
    {
        parent::checkPermissions();

        if (!hash_equals($this->minecraft->user, $_POST['user']) ||
            !hash_equals($this->minecraft->password, $_POST['password'])) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Gets the User from given UUID.
     * @param string $uuid UUID
     */
    protected function getUser(string $uuid): User
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        $minecraftUserList->readObjects();
        /** @var MinecraftUser */
        $minecraftUser = $minecraftUserList->getSingleObject();
        return new User($minecraftUser->userID);
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
        return new JsonResponse($data, $statusCode, $headers, $encodingOptions);
    }
}
