<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use wcf\system\io\HttpFactory;

/**
 * MinecraftHandler class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\System\Minecraft
 */
class MinecraftHandler implements IMinecraftHandler
{

    protected string $url;

    protected string $user;

    protected string $password;

    /**
     * @inheritDoc
     */
    public function __construct(string $url, string $user, string $password)
    {
        $this->url = $url;

        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function call(string $httpMethod, string $method = '', array $args = []): ResponseInterface
    {
        /** @var \GuzzleHttp\ClientInterface */
        $client = HttpFactory::getDefaultClient();

        if (str_ends_with($this->url, '/')) {
            $requestUrl = $this->url . $method;
        } else {
            $requestUrl = $this->url . '/' . $method;
        }
        $options = [
            'auth' => [
                $this->user,
                $this->password
            ],
            'json' => $args
        ];
        /** @var ResponseInterface */
        $response = $client->request($httpMethod, $requestUrl, $options);

        if ($response->getHeader('Content-Type') === 'application/json') {
            throw new GuzzleException('Unsupported Media Type: Content-Type is not application/json', 415);
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function callRequest(RequestInterface $request): ResponseInterface
    {
        /** @var \GuzzleHttp\ClientInterface */
        $client = HttpFactory::getDefaultClient();

        // Set Uri information
        /** @var \Psr\Http\Message\UriInterface */
        $baseUri = new Uri($this->url);

        /** @var \Psr\Http\Message\UriInterface */
        $uri = $baseUri->withPath($request->getUri()->getPath());

        /** @var \Psr\Http\Message\RequestInterface */
        $request = $request->withUri($uri);

        // Set Auth information
        /** @var \Psr\Http\Message\RequestInterface */
        $request = $request->withAddedHeader('Authorization', 'Basic' . base64_encode($this->user . ':' . $this->password));

        /** @var ResponseInterface */
        $response = $client->send($request);

        if ($response->getHeader('Content-Type') === 'application/json') {
            throw new GuzzleException('Unsupported Media Type: Content-Type is not application/json', 415);
        }

        return $response;
    }
}
