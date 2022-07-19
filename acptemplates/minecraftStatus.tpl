{if $statusCode === 200}
	<p>applicationDescription: {$applicationDescription}</p>
	<p>{$platformName}</p>
	<p>{$platformVersion}</p>
	<p>{$applicationAuthor}</p>
	<p>{$version}</p>
	<p>{$modules}</p>
	{hascontent}
		<ul>
			{content}
				{foreach from=$modules item=item}
					<li>{$item}</li>
				{/foreach}
				{event name='listItems'}
			{/content}
		</ul>
	{/hascontent}
{else}
	<p>{lang}wcf.page.minecraftList.button.status.result.connectionFailed{/lang}</p>
	<p>{$statusCode}: {$status}</p>
{/if}
{event name='minecraftStatus'}