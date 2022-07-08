<?php

namespace wcf\system\util;

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

const URL = 'url';
const HEADERS = 'headers';
const BODY = 'body';
const VERSION = 'version';

const UUID_PATTERN = '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$';

/**
 * MojangUtil class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Util
 * @see https://wiki.vg/Mojang_API
 */
class MojangUtil
{
    /**
     * Check weather given uuid is a valid uuid
     * @param string $uuid uuid to check
     * @return int|false
     * @see \preg_match
     */
    public static function validUUID(string $uuid)
    {
        return \preg_match('/' . UUID_PATTERN . '/', $uuid);
    }

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
        if (!array_key_exists(URL, $args)) {
            throw new BadMethodCallException('Url not given.');
        } else if (!(is_string($args[URL]) || $args[URL] instanceof UriInterface)) {
            throw new BadMethodCallException('Url not string or UriInterface');
        }

        // Check headers
        if (!array_key_exists(HEADERS, $args)) {
            $args[HEADERS] = [];
        } else if (!is_array($args[HEADERS])) {
            throw new BadMethodCallException('Header is no array.');
        }

        // Check body
        if (!array_key_exists(BODY, $args)) {
            $args[BODY] = null;
        } else if (is_array($args[BODY])) {
            $args[BODY] = JSON::encode($args[BODY]);
        } else if (!( is_string($args[BODY]) || is_resource($args[BODY]) || $args[BODY] instanceof StreamInterface)) {
            throw new BadMethodCallException('Unknown body.');
        }

        // Check version
        if (!array_key_exists(VERSION, $args)) {
            $args[VERSION] = '1.1';
        } else if ($args[VERSION] !== '1.0' || $args[VERSION] !== '1.1') {
            throw new BadMethodCallException('Unknown http version.');
        }

        // Get client
        if ($this->client === null) {
            $this->client = HttpFactory::getDefaultClient();
        }

        // Create request
        $request = new Request($httpMethod, $args[URL], $args[HEADERS], $args[BODY], $args[VERSION]);
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
            URL => new Uri($url)
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Usernames_to_UUIDs
     */
    public function namesToUUIDs(array $names)
    {
        return $this->POST([
            URL => new Uri("https://api.mojang.com/profiles/minecraft"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => $names
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#UUID_to_Name_History
     */
    public function nameHistory(string $uuid)
    {
        if (!self::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->GET([
            URL => new Uri("https://api.mojang.com/user/profiles/$uuid/names")
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#UUID_to_Profile_and_Skin.2FCape
     */
    public function uuidToProfile(string $uuid)
    {
        if (!self::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->GET([
            URL => new Uri("https://sessionserver.mojang.com/session/minecraft/profile/$uuid")
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Blocked_Servers
     */
    public function blockedServer()
    {
        return $this->GET([
            URL => new Uri("https://sessionserver.mojang.com/blockedservers")
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication
     */
    public function authStatus()
    {
        return $this->GET([
            URL => new Uri("https://authserver.mojang.com/")
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
            URL => new Uri("https://authserver.mojang.com/authenticate"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => $body
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
            URL => new Uri("https://authserver.mojang.com/refresh"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => $body
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
            URL => new Uri("https://authserver.mojang.com/validate"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Authentication#Signout
     */
    public function signout(string $name, string $password)
    {
        return $this->POST([
            URL => new Uri("https://authserver.mojang.com/signout"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => [
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
            URL => new Uri("https://authserver.mojang.com/invalidate"),
            HEADERS => [
                'Content-Type' => 'application/json'
            ],
            BODY => $body
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Profile_Information
     */
    public function profile(string $bearerToken)
    {
        return $this->GET([
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/player/attributes"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/privacy/blocklist"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/player/certificates"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/namechange"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/productvoucher/giftcode"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/name/$name/available"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/name/$name"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/skins"),
            BODY => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/skins"),
            HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => "image/$format"
            ],
            BODY => $data
        ]);
    }

    /**
     * @param string $uuid
     * @throws BadMethodCallException on invalid uuid
     * @see https://wiki.vg/Mojang_API#Reset_Skin
     */
    public function resetSkin(string $uuid, string $bearerToken)
    {
        if (!self::validUUID($uuid)) {
            throw new BadMethodCallException('Given uuid not valid.');
        }
        return $this->DELETE([
            URL => new Uri("https://api.mojang.com/user/profile/$uuid/skin"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/capes/active"),
            HEADERS => [
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
            URL => new Uri("https://api.minecraftservices.com/minecraft/profile/capes/active"),
            HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ],
            BODY => [
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
            URL => new Uri("https://api.mojang.com/user/security/location"),
            HEADERS => [
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
            URL => new Uri("https://api.mojang.com/user/security/challenges"),
            HEADERS => [
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
            URL => new Uri("https://api.mojang.com/user/security/location"),
            HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ],
            BODY => $payload
        ]);
    }

    /**
     * @see https://wiki.vg/Mojang_API#Get_Account_Migration_Information
     */
    public function getAccountMigrationInformation(string $bearerToken)
    {
        return $this->GET([
            URL => new Uri("https://api.minecraftservices.com/rollout/v1/msamigration"),
            HEADERS => [
                'Authorization' => "Bearer $bearerToken",
                'Content-Type' => 'application/json'
            ]
        ]);
    }
}
