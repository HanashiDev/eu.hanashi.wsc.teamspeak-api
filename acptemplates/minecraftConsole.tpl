{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftConsole'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftConsole{/lang} {$connectionName}<h1>
	</div>
</header>

<div class="section tabularBox">
	{if $error == 'true'}
	<div class="error">{lang}wcf.page.minecraftConsole.cantConnect{/lang}</div>
	{else}
	{if $response|isset}
	<div class="success">
		{assign var=rows value="\n"|explode:$response}
		{foreach from=$rows item=row}
		<p>{$row}</p>
		{/foreach}
	</div>
	<br />
	<br />
	{/if}
	<form action="/acp/index.php?minecraft-console/{$minecraftID}/" method="post">
		<div class="inputAddon">
			<input type="text" class="long" name="command" />
			<button type="submit" class="button inputSuffix"><span class="icon fa-caret-right" /></button>
		</div>
	</form>
	<small>{lang}wcf.page.minecraftConsole.description{/lang}</small>
	{/if}
</div>

<div class="section tabularBox">
	<a href="https://minecraft.fandom.com/wiki/Commands" target="_blank">{lang}wcf.page.minecraftConsole.commandListDefault{/lang}</a><br />
	<a href="https://bukkit.fandom.com/wiki/CraftBukkit_Commands" target="_blank">{lang}wcf.page.minecraftConsole.commandListBukkit{/lang}</a><br />
	<a href="https://www.spigotmc.org/wiki/spigot-commands/" target="_blank">{lang}wcf.page.minecraftConsole.commandListSpigot{/lang}</a><br />
	<a href="https://www.spigotmc.org/wiki/bungeecord-commands/" target="_blank">{lang}wcf.page.minecraftConsole.commandListBungeeCord{/lang}</a>
</div>

{include file='footer'}
