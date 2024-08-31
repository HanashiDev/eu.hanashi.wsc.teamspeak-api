{include file='header' pageTitle='wcf.acp.menu.link.configuration.teamspeak.teamspeakList.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList.{$action}{/lang}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TeamspeakList'}{/link}" class="button">{icon size=16 name='list'} <span>{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{unsafe:$form->getHtml()}

<script data-relocate="true">
	require(["Hanashi/TeamSpeak/ServerAdd"], function({ ServerAdd }) {
		{jsphrase name='wcf.page.teamspeakAdd.virtualServerPort'}
		{jsphrase name='wcf.page.teamspeakAdd.virtualServerPort.description'}
		{jsphrase name='wcf.page.teamspeakAdd.virtualServerID'}
		{jsphrase name='wcf.page.teamspeakAdd.virtualServerID.description'}
		{jsphrase name='wcf.page.teamspeakAdd.password'}
		{jsphrase name='wcf.page.teamspeakAdd.apiKey'}

		new ServerAdd();
	});
</script>

{include file='footer'}
