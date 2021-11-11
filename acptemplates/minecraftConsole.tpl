{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftConsole'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftConsole{/lang} {$connectionName}<h1>
	</div>
</header>

{if $errorType == 'cantConnect'}
	<div class="error">
		{lang}wcf.page.minecraftConsole.error.{@$errorType}{/lang}
	</div>
{else if $errorType == 'cantRead'}
	<div class="error">
		{lang}wcf.page.minecraftConsole.error.{@$errorType}{/lang}
	</div>
{else}
	{if $response|isset}
	<div class="section">
		<dt><label for="response">{lang}wcf.page.minecraftConsole.response{/lang}</label></dt>
		<dd>
			<textarea type="text" id="response" name="response" style="resize: vertical; max-height: 20rem; min-height: 5rem" readonly >{$response}</textarea>
		</dd>
	</div>
	{/if}

	<form method="post" action="{link controller='MinecraftConsole' id=$minecraftID}{/link}">
		<div class="section">
			<dl{if $errorField == 'command'} class="formError"{/if}>
				<dt><label for="command">{lang}wcf.page.minecraftConsole.command{/lang}</label></dt>
				<dd>
   					<input type="text" id="command" name="command" class="long" required autofocus>
   					{if $errorField == 'command'}
   						<small class="innerError">
   							{if $errorType == 'empty'}
   								{lang}wcf.global.form.error.empty{/lang}
   							{else}
								{lang}wcf.page.minecraftConsole.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}wcf.page.minecraftConsole.command.description{/lang}</small>
				</dd>
			</dl>

			{event name='dataFields'}
		</div>

		{event name='sections'}

		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
			{csrfToken}
		</div>
	</form>
{/if}

<div class="section tabularBox">
	<a href="https://minecraft.fandom.com/wiki/Commands" target="_blank">{lang}wcf.page.minecraftConsole.commandListDefault{/lang}</a><br />
	<a href="https://bukkit.fandom.com/wiki/CraftBukkit_Commands" target="_blank">{lang}wcf.page.minecraftConsole.commandListBukkit{/lang}</a><br />
	<a href="https://www.spigotmc.org/wiki/spigot-commands/" target="_blank">{lang}wcf.page.minecraftConsole.commandListSpigot{/lang}</a><br />
	<a href="https://www.spigotmc.org/wiki/bungeecord-commands/" target="_blank">{lang}wcf.page.minecraftConsole.commandListBungeeCord{/lang}</a>
</div>

{include file='footer'}
