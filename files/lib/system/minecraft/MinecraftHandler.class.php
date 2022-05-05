<?php

namespace wcf\system\minecraft;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use wcf\system\io\HttpFactory;
use wcf\util\StringUtil;

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
    public function call(string $httpMethod, string $method = '', array $args = []): ?ResponseInterface
    {
        /** @var \GuzzleHttp\ClientInterface */
        $client = HttpFactory::getDefaultClient();
        if (StringUtil::endsWith($this->url, '/')) {
//        if (str_ends_with($this->url, '/')) {
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
        return $client->request($httpMethod, $requestUrl, $options);
    }

    /**
     * @inheritDoc
     */
    public function callRequest(RequestInterface $request): ?ResponseInterface
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

        return $client->send($request);
    }
}
