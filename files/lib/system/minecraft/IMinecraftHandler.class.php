<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use wcf\system\exception\MinecraftException;

/**
 * MinecraftHandler interface
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
interface IMinecraftHandler
{
    /**
     * Construct for Minecraft class and tries to connect.
     * @param string $url       the url to use.
     * @param string $user      the user to use.
     * @param string $password  the url to use.
     */
    public function __construct(string $url, string $user, string $password);

    /**
     * Call method on Minecraft.
     * @param string $httpMethod Method to call
     * @param string $method Method to call
     * @param array $args Arguments for method
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function call(string $httpMethod, string $method = '', array $args = []): ?ResponseInterface;
}
