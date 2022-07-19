{if $statusCode == 200}
	<p>{lang}wcf.page.minecraftList.button.status.result.applicationDescription{/lang}: {$applicationDescription}</p>
	<p>{lang}wcf.page.minecraftList.button.status.result.version{/lang}: {$version}</p>
	<p>{lang}wcf.page.minecraftList.button.status.result.platformName{/lang}: {$platformName}</p>
	<p>{lang}wcf.page.minecraftList.button.status.result.platformVersion{/lang}: {$platformVersion}</p>
	{hascontent}
		<p>{lang}wcf.page.minecraftList.button.status.result.modules{/lang}: </p>
		<ul>
			{content}
				{foreach from=$modules item=item}
					<li>{$item}</li>
				{/foreach}
				{event name='listItems'}
			{/content}
		</ul>
	{/hascontent}
	<p>{lang}wcf.page.minecraftList.button.status.result.applicationAuthor{/lang}: {$applicationAuthor}</p>
{elseif $statusCode == 0}
	<p>{lang}wcf.page.minecraftList.button.status.result.connectionFailed{/lang}</p>
	<p>{$status}</p>
{else}
	<p>{lang}wcf.page.minecraftList.button.status.result.connectionFailed{/lang}</p>
	<p>{$statusCode}: {$status}</p>
{/if}
{event name='minecraftStatus'}