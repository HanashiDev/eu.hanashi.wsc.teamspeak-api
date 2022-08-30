<?php

namespace wcf\action;

use Laminas\Diactoros\Response\JsonResponse;
use wcf\data\minecraft\Minecraft;
use wcf\data\user\minecraft\MinecraftUser;
use wcf\data\user\minecraft\MinecraftUserList;
use wcf\data\user\User;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\flood\FloodControl;

/**
 * MinecraftPasswordCheck action class
 *
 * @author   xXSchrandXx
 * @license  Apache License 2.0 (https://www.apache.org/licenses/LICENSE-2.0)
 * @package  WoltLabSuite\Core\Action
 */
class AbstractMinecraftAction extends AbstractAction
{

    private string $floodgate = 'de.xxschrarndxx.wsc.minecraft-api.floodgate';

    /**
     * MinecraftID the request came from.
     * @var Minecraft
     */
    protected $minecraft;

    /**
     * @inheritDoc
     */
    public function __run()
    {
        if (empty($_SERVER['HTTPS'])) {
            return $this->send('SSL Certificate Required', 496, false);
        }

        // Flood control
        if (MINECRAFT_FLOODGATE_MAXREQUESTS > 0) {
            FloodControl::getInstance()->registerContent($this->d);

            $secs = MINECRAFT_FLOODGATE_RESETTIME * 60;
            $time = \ceil(TIME_NOW / $secs) * $secs;
            $data = FloodControl::getInstance()->countContent($this->d, new \DateInterval('PT' . MINECRAFT_FLOODGATE_RESETTIME . 'M'), $time);
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
        // validate minecraftID
        if (!array_key_exists('minecraftID', $_POST)) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. Missing \'minecraftID\'.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        if (!is_int($_POST['minecraftID'])) {
            if (ENABLE_DEBUG_MODE) {
                return $this->send('Bad Request. \'minecraftID\' is no int.', 401);
            } else {
                return $this->send('Bad Request.', 401);
            }
        }
        $this->minecraft = new Minecraft($_POST['minecraftID']);
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

        parent::readParameters();
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
     * @inheritDoc
     */
    public function execute()
    {
        try {
            parent::execute();
        } catch (PermissionDeniedException | IllegalLinkException $e) {
            return $this->send($e->getMessage(), $e->getCode());
        }
    }

    protected function getUser(string $uuid): User
    {
        $minecraftUserList = new MinecraftUserList();
        $minecraftUserList->getConditionBuilder()->add('minecraftUUID = ?', [$uuid]);
        $minecraftUserList->readObjects();
        /** @var MinecraftUser */
        $minecraftUser = $minecraftUserList->getSingleObject();
        return new User($minecraftUser->userID);
    }

    protected function send($status, $statusCode, $data = [], $headers = [], $encodingOptions = JsonResponse::DEFAULT_JSON_FLAGS): JsonResponse
    {
        if ($statusCode < 100 && $statusCode > 599) {
            $statusCode = 400;
        }
        if (!array_key_exists('status', $data)) {
            $data['status'] = $status;
        }
        if (!array_key_exists('statusCode', $data)) {
            $data['statusCode'] = $statusCode;
        }
        return new JsonResponse($data, $statusCode, $headers, $encodingOptions);
    }
}
