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

{@$form->getHtml()}

<script data-relocate="true">
	require(["Hanashi/TeamSpeak/ServerAdd", "Language"], function({ ServerAdd }, Language) {
		Language.addObject({
			'wcf.page.teamspeakAdd.virtualServerPort': '{lang}wcf.page.teamspeakAdd.virtualServerPort{/lang}',
			'wcf.page.teamspeakAdd.virtualServerPort.description': '{lang}wcf.page.teamspeakAdd.virtualServerPort.description{/lang}',
			'wcf.page.teamspeakAdd.virtualServerID': '{lang}wcf.page.teamspeakAdd.virtualServerID{/lang}',
			'wcf.page.teamspeakAdd.virtualServerID.description': '{lang}wcf.page.teamspeakAdd.virtualServerID.description{/lang}',
			'wcf.page.teamspeakAdd.password': '{lang}wcf.page.teamspeakAdd.password{/lang}',
			'wcf.page.teamspeakAdd.apiKey': '{lang}wcf.page.teamspeakAdd.apiKey{/lang}'
		});
		new ServerAdd();
	});
</script>

{include file='footer'}
