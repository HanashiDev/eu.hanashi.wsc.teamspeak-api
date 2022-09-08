<?php

namespace wcf\util;

use BadMethodCallException;
use wcf\system\io\HttpFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Laminas\Stdlib\ResponseInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use wcf\util\JSON;

/**
 * Mojang util class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Util
 * @see https://wiki.vg/Mojang_API
 */
class MojangUtil
{
    const URL = 'url';
    const HEADERS = 'headers';
    const BODY = 'body';
    const VERSION = 'version';

    /**
     * Valid httpMethods
     * @var array
     */
    protected $functions = [
        'GET',
        'POST',
        'PUT',
        'DELETE'
    ];

    /**
     * @var PsrClientInterface&ClientInterface
     * @see HttpFactory#getDefaultClient()
     */
    protected $client = null;

    /**
     * @return ResponseInterface&PsrResponseInterface
     * @throws BadMethodCallException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    protected function __call($httpMethod, $args)
    {
        // Check method
        $httpMethod = strtoupper($httpMethod);
        if (!in_array($httpMethod, $this->functions)) {
            throw new BadMethodCallException('Unknown http method.');
        }

        // Check uri
        if (!array_key_exists(self::URL, $args)) {
            throw new BadMethodCallException('Url not given.');
        } else if (!(is_string($args[self::URL]) || $args[self::URL] instanceof UriInterface)) {
            throw new BadMethodCallException('Url not string or UriInterface');
        }

        // Check headers
        if (!array_key_exists(self::HEADERS, $args)) {
            $args[self::HEADERS] = [];
        } else if (!is_array($args[self::HEADERS])) {
            throw new BadMethodCallException('Header is no array.');
        }

        // Check body
        if (!array_key_exists(self::BODY, $args)) {
            $args[self::BODY] = null;
        } else if (is_array($args[self::BODY])) {
            $args[self::BODY] = JSON::encode($args[self::BODY]);
        } else if (!( is_string($args[self::BODY]) || is_resource($args[self::BODY]) || $args[self::BODY] instanceof StreamInterface)) {
            throw new BadMethodCallException('Unknown body.');
        }

        // Check version
        if (!array_key_exists(self::VERSION, $args)) {
            $args[self::VERSION] = '1.1';
        } else if ($args[self::VERSION] !== '1.0' || $args[self::VERSION] !== '1.1') {
            throw new BadMethodCallException('Unknown http version.');
        }

        // Get client
        if ($this->client === null) {
            $this->client = HttpFactory::getDefaultClient();
        }

        // Create request
        $request = new Request($httpMethod, $args[self::URL], $args[self::HEADERS], $args[self::BODY], $args[self::VERSION]);
        if ($this->client instanceof ClientInterface) {
            return $this->client->send($request);
        } else {
            return $this->client->sendRequest($request);
        }
    }

    /**
     * @see https://wiki.vg/Mojang_API#Username_to_UUID
     */
    public function nameToUUID(string $name, ?int $timestamp = null)
    {
        $url = "https://api.mojang.com/users/profiles/minecraft/$name";
        if ($timestamp === null) {
            $url .= "?at=$timestamp";
        }
        return $this->GET([
            self::URL => new Uri($url)
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Usernames_to_UUIDs
     */
    public function namesToUUIDs(array $names)
    {
        return $this->POST([
            self::URL => new Uri("https://api.mojang.com/profiles/minecraft"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => $names
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#UUID_to_Name_History
     */
    public function nameHistory(string $uuid)
    {
        if (!MinecraftUtil::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->GET([
            self::URL => new Uri("https://api.mojang.com/user/profiles/$uuid/names")
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#UUID_to_Profile_and_Skin.2FCape
     */
    public function uuidToProfile(string $uuid)
    {
        if (!MinecraftUtil::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->GET([
            self::URL => new Uri("https://sessionserver.mojang.com/session/minecraft/profile/$uuid")
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Blocked_Servers
     */
    public function blockedServer()
    {
        return $this->GET([
            self::URL => new Uri("https://sessionserver.mojang.com/blockedservers")
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication
     */
    public function authStatus()
    {
        return $this->GET([
            self::URL => new Uri("https://authserver.mojang.com/")
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Authenticate
     */
    public function authenticate(string $name, string $password, ?string $clientToken = null, bool $requestUser = false)
    {
        $body = [
            'agent' => [
                'name' => 'Minecraft',
                'version' => 1
            ],
            'username' => $name,
            'password' => $password,
            'requestUser' => $requestUser
        ];
        if ($clientToken !== null) {
            $body['clientToken'] = $clientToken;
        }
        return $this->POST([
            self::URL => new Uri("https://authserver.mojang.com/authenticate"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Refresh
     */
    public function refresh(string $accessToken, ?string $clientToken = null, ?int $selectedProfileId = null, ?int $selectedProfileName = null, bool $requestUser = false)
    {
        $body = [
            'accessToken' => $accessToken,
            'requestUser' => $requestUser
        ];
        if ($selectedProfileId !== null && $selectedProfileName !== null) {
            $body['selectedProfile'] = [
                'id' => $selectedProfileId,
                'name' => $selectedProfileName
            ];
        }
        return $this->POST([
            self::URL => new Uri("https://authserver.mojang.com/refresh"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Validate
     */
    public function validate(string $accessToken, ?string $clientToken = null)
    {
        $body = [
            'accessToken' => $accessToken
        ];
        if ($clientToken !== null) {
            $body['clientToken'] = $clientToken;
        }
        return $this->POST([
            self::URL => new Uri("https://authserver.mojang.com/validate"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Signout
     */
    public function signout(string $name, string $password)
    {
        return $this->POST([
            self::URL => new Uri("https://authserver.mojang.com/signout"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => [
                'username' => $name,
                'password' => $password
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Invalidate
     */
    public function invalidate(string $accessToken, ?string $clientToken = null)
    {
        $body = [
            'accessToken' => $accessToken
        ];
        if ($clientToken !== null) {
            $body['clientToken'] = $clientToken;
        }
        return $this->POST([
            self::URL => new Uri("https://authserver.mojang.com/invalidate"),
            self::HEADERS => [
                'Content-Type' => 'application/json'
            ],
            self::BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Profile_Information
     */
    public function profile(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Player_Attributes
     */
    public function playerAttributes(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/player/attributes"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Player_Blocklist
     */
    public function playerBlocklist(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/privacy/blocklist"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Player_Certificates
     */
    public function playerCertificates(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/player/certificates"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Profile_Name_Change_Information
     */
    public function playerNameChangeInformation(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/namechange"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Check_Product_Voucher
     */
    public function checkProtuctVoucher(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/productvoucher/giftcode"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Name_Availability
     */
    public function nameAvailability(string $name, string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/name/$name/available"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Change_Name
     */
    public function changeName(string $name, string $bearerToken)
    {
        return $this->PUT([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/name/$name"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Change_Skin
     */
    public function changeSkin(string $variant, string $url)
    {
        return $this->POST([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/skins"),
            self::BODY => [
                'variant' => $variant,
                'url' => $url
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Upload_Skin
     */
    public function uploadSkin(string $bearerToken, StreamInterface $data, string $format = 'png')
    {
        return $this->POST([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/skins"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => "image/$format"
            ],
            self::BODY => $data
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#Reset_Skin
     */
    public function resetSkin(string $uuid, string $bearerToken)
    {
        if (!MinecraftUtil::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->DELETE([
            self::URL => new Uri("https://api.mojang.com/user/profile/$uuid/skin"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Hide_Cape
     */
    public function hideCape(string $bearerToken)
    {
        return $this->DELETE([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/capes/active"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Show_Cape
     */
    public function showCape(string $id, string $bearerToken)
    {
        return $this->PUT([
            self::URL => new Uri("https://api.minecraftservices.com/minecraft/profile/capes/active"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ],
            self::BODY => [
                'capeId' => $id
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Verify_Security_Location
     */
    public function verifySecurityLocation(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.mojang.com/user/security/location"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Get_Security_Questions
     */
    public function getSecurityQuerstions(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.mojang.com/user/security/challenges"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken"
            ]
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Send_Security_Answers
     */
    public function sendSecurityAnswers(array $payload, string $bearerToken)
    {
        return $this->POST([
            self::URL => new Uri("https://api.mojang.com/user/security/location"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ],
            self::BODY => $payload
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Get_Account_Migration_Information
     */
    public function getAccountMigrationInformation(string $bearerToken)
    {
        return $this->GET([
            self::URL => new Uri("https://api.minecraftservices.com/rollout/v1/msamigration"),
            self::HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ]
        ]);
    }
}
