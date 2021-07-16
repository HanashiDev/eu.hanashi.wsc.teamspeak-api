{include file='header' pageTitle='wcf.acp.menu.link.configuration.teamspeak.teamspeakList'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\teamspeak\\TeamspeakAction', $('.jsRow'));
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TeamspeakAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList.add{/lang}</span></a></li>
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
		<table class="table">
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
					<tr class="jsRow">
						<td class="columnIcon">
							<a href="{link controller='TeamspeakEdit' id=$object->teamspeakID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon24 fa-pencil"></span></a>
							<a href="#" class="jsDeleteButton jsTooltip" title="{lang}wcf.global.button.delete{/lang}" data-confirm-message-html="{lang __encode=true}wcf.page.teamspeakList.removeConnectionQuestion{/lang}" data-object-id="{@$object->teamspeakID}"><span class="icon icon24 fa-times"></span></a>
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
						<td class="columnDate">{@$object->creationDate|time}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
