Quicklinks: [General](#general) | [API](#api) | [Links](#links) | [License](https://github.com/xXSchrandXx/de.xxschrarndxx.wsc.minecraft-api/blob/main/LICENSE)

"Minecraft"™ is a trademark of Mojang Synergies AB. This Resource ist not affiliate with Mojang.

# General
## Description
This plugin is an interface between other plugins and Minecraft servers. Minecraft API is mostly relevant to developers. It allows you to send and get data of your Minecraft server.
## Requirements
[WSC-Minecraft-Bridge](#links) installed on your Bukkit- / Spigot- / BungeeCord-Server.
# API
## package.xml
```XML
<requiredpackage minversion="2.0.0">de.xxschrarndxx.wsc.minecraft-api</requiredpackage >
```
## API-Example:
```PHP
use wcf\data\minecraft\Minecraft;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ID des Minecraft-Eintrages im ACP.
 * 
 * @var int
 */
$id = 1;

/**
 * DatabaseObject der Minecraft-ID.
 * 
 * @var Minecraft
 */
$minecraft = new Minecraft($id);

/**
 * ConnectionHandler des Minecraft-Servers.
 * 
 * @var MinecraftConnectionHandler
 */
$connection = $minecraft->getConnection();

/**
 * Antwort auf den gesendeten Befehl.
 * 
 * @var ?ResponseInterface
 */
$response = null;

try {
    $response = $connection->call('POST', 'path', ['foo' => 'bar']);
} catch (GuzzleException $e) {
    if (\ENABLE_DEBUG_MODE) {
        \wcf\functions\exception\logThrowable($e);
    }
}
```
# Links
## GitHub
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-api](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-linker)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-sync)
* [xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile](https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-profile)
* [xXSchrandXx/WSC-Minecraft-Bridge](https://github.com/xXSchrandXx/WSC-Minecraft-Bridge)
* [xXSchrandXx/WSC-Minecraft-Authenticator](https://github.com/xXSchrandXx/WSC-Minecraft-Authenticator)

## WoltLab
* [Plugin-Store/Minecraft-API](https://www.woltlab.com/pluginstore/file/7077-minecraft-api/)
* [Plugin-Store/Minecraft-Linker](https://www.woltlab.com/pluginstore/file/7093-minecraft-linker/)
## Spigot
* [Resources/WSC-Minecraft-Bridge](https://www.spigotmc.org/resources/wsc-minecraft-bridge.100716/)
* [Resources/WSC-Minecraft-Authenticator](https://www.spigotmc.org/resources/wsc-minecraft-authenticator.101169/)
## Donate
* [PayPal](https://www.paypal.com/donate/?hosted_button_id=RFYYT7QSAU7YJ)
## JavaDocs
* [Docs/wscbridge](https://maven.gamestrike.de/docs/wscbridge/)
* [Docs/wscauthenticator](https://maven.gamestrike.de/docs/wscauthenticator/)
## Maven
```XML
<repository>
	<id>schrand-repo</id>
	<url>https://maven.gamestrike.de/mvn/</url>
</repository>
```