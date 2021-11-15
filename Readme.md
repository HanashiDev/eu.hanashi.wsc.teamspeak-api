Languages: [German](#----german) | [English](#----english)
<h1>
    English
</h1>
<h2>
	General
</h2>
<h3>
	Plugin description
</h3>
<p>This plugin is an interface between other plugins and Minecraft servers.</p>
<p>Minecraft API is mostly relevant to developers. It allows you to send commands to your Minecraft server via <a href="https://wiki.vg/RCON">RCON</a>and receive responses from it.</p>
<h3>
	Links
</h3>
<p>GitHub: <a href="https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api">xXSchrandXx/de.xxschrandxx.wsc.minecraft-api</a></p>
<h2>
	Setting up a server
</h2>
<h3>
	Minecraft server
</h3>
<p>In order for the Minecraft server to receive commands, the following settings must be made in the server.properties.</p>

``` PROPERTIES
enable-rcon=true
rcon.password=<your password>
rcon.port=<1-65535>
```
<h3>
	BungeeCord-Server
</h3>
<p>BungeeCord servers can also be used via plugins such as <a href="https://www.spigotmc.org/resources/bungeecord-rcon.5817/">BungeeCord-RCON</a>.</p>
<h3>
	ACP
</h3>
<p>After RCON has been activated on the server and it is "running", it can be added in the ACP.</p>

![ServerAdd.png](https://raw.github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api/master/pictures/ServerAdd.png)

<h2>
	Support
</h2>
<p>This is my first APU for WLS.If there are problems with this plugin, you can <a href="https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api/issues">create an "Issue" via GitHub</a>.</p>
<h2>
	Usage
</h2>
<h3>
	Package name
</h3>
<p>Make sure that you use my typing error in the package name in the required packages.</p>

``` XML
<requiredpackage minversion="1.0.0" file="requirements/de.xxschrandxx.wsc.minecraft-api.tar.gz">de.xxschrandxx.wsc.minecraft-api</requiredpackage>
```
<h3>
	API example:
</h3>

``` PHP
use wcf\data\minecraft\​Minecraft;
use wcf\system\exception\MinecraftException;

/**
 * ID of the Minecraft-Entry in the ACP.
 * 
 * @var int
 */
$id = 1;

/**
 * Command to execute.
 *
 * @var string
 */
$command = "say Hello world";

/**
 * DatabaseObject from the Minecraft-ID.
 * 
 * @var Minecraft
 */
$minecraft = new Minecraft($id);

/**
 * ConnectionHandler for the Minecraft-Servers.
 * 
 * @var MinecraftConnectionHandler
 */
$connection = $minecraft->getConnection();

/**
 * Response for the command.
 * 
 * @var array
 */
$response = null;

try {
    ​​$response = $connection->call($command);
} catch (MinecraftException $e) {
    if (\ENABLE_DEBUG_MODE) {
        \wcf\functions\exception\logThrowable($e);
    }
}
```
<h1>
    German
</h1>
<h2>
	Allgemeines
</h2>
<h3>
	Plugin-Beschreibung
</h3>
<p>Dieses Plugin ist eine Schnittstelle zwischen anderen Plugins und Minecraft-Servern.</p>
<p>Minecraft-API ist vor allem relevant für Entwickler. Es ermöglicht Befehle an Ihren Minecraft-Server per <a href="https://wiki.vg/RCON">RCON</a> zu senden und Antworten von diesem zu empfangen.</p>
<h3>
	Links
</h3>
<p>Github: <a href="https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api">xXSchrandXx/de.xxschrandxx.wsc.minecraft-api</a></p>
<h2>
	Einrichtung eines Servers
</h2>
<h3>
	Minecraft-Server
</h3>
<p>Damit der Minecraft-Server Befehle empfangen kann, muss in der server.properties folgende Einstellungen vorgenommen werden.</p>

``` PROPERTIES
enable-rcon=true
rcon.password=<your password>
rcon.port=<1-65535>
```
<h3>
	BungeeCord-Server
</h3>
<p>BungeeCord-Server können über Plugins wie <a href="https://www.spigotmc.org/resources/bungeecord-rcon.5817/">BungeeCord-RCON</a> auch verwendet werden.</p>
<h3>
	ACP
</h3>
<p>Nachdem RCON auf dem Server aktiviert wurde und dieser "läuft", kann man ihn im ACP hinzufügen.</p>

![ServerAdd.png](https://raw.github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api/master/pictures/ServerAdd.png)
<h2>
	Support
</h2>
<p>Dies ist meine erste API für das WLS. Wenn es Probleme mit dem Plugin gibt, könnt ihr über <a href="https://github.com/xXSchrandXx/de.xxschrandxx.wsc.minecraft-api/issues">GitHub einen "Issue" erstellen</a>.</p>
<h2>
	Gebrauch
</h2>
<h3>
	Paketname
</h3>
<p>Achtet darauf, dass ihr in den benötigten Paketen meinen Schreibfehler im Paketnamen übernehmt.</p>

``` XML
<requiredpackage minversion="1.0.0" file="requirements/de.xxschrandxx.wsc.minecraft-api.tar.gz">de.xxschrandxx.wsc.minecraft-api</requiredpackage>
```
<h3>
	API-Beispiel:
</h3>

``` PHP
use wcf\data\minecraft\​Minecraft;
use wcf\system\exception\MinecraftException;

/**
 * ID des Minecraft-Eintrages im ACP.
 * 
 * @var int
 */
$id = 1;

/**
 * Befehl für den Server.
 *
 * @var string
 */
$command = "say Hallo Welt";

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
 * @var array
 */
$response = null;

try {
    ​​$response = $connection->call($command);
} catch (MinecraftException $e) {
    if (\ENABLE_DEBUG_MODE) {
        \wcf\functions\exception\logThrowable($e);
    }
}
```