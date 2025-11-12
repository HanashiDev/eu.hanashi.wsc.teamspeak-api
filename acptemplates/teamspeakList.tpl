{include file='header' pageTitle='wcf.acp.menu.link.configuration.teamspeak.teamspeakList'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TeamspeakAdd'}{/link}" class="button">{icon size=16 name='plus'} <span>{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList.add{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
