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

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="TeamspeakList" link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\teamspeak\TeamspeakAction">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.page.teamspeakList.teamspeakID{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.connectionName{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.hostname{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.queryType{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.queryPort{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.virtualServerPort{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.username{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.displayName{/lang}</th>
					<th>{lang}wcf.page.teamspeakList.creationDate{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=object}
					<tr class="jsObjectActionObject" data-object-id="{$object->teamspeakID}">
						<td class="columnIcon">
							<a href="{link controller='TeamspeakEdit' id=$object->teamspeakID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">{icon size=16 name='pencil'}</a>
							{objectAction action="delete" objectTitle=$object->connectionName}
							{event name='rowButtons'}
						</td>
						<td class="columnID">{#$object->teamspeakID}</td>
						<td class="columnTitle">{$object->connectionName}</td>
						<td class="columnText">{$object->hostname}</td>
						<td class="columnText">{$object->queryType}</td>
						<td class="columnText">{$object->queryPort}</td>
						<td class="columnText">{$object->virtualServerPort}</td>
						<td class="columnText">{$object->username}</td>
						<td class="columnText">{$object->displayName}</td>
						<td class="columnDate">{unsafe:$object->creationDate|time}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
