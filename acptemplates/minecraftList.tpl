{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftList'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\minecraft\\MinecraftAction', $('.jsRow'));
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='MinecraftAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList.add{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{hascontent}
	<div class="paginationTop">
		{content}
			{pages print=true assign=pagesLinks controller="MinecraftList" link="pageNo=%d"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>{lang}wcf.page.minecraftList.minecraftID{/lang}</th>
					<th>{lang}wcf.page.minecraftList.connectionName{/lang}</th>
					<th>{lang}wcf.page.minecraftList.type{/lang}</th>
					<th>{lang}wcf.page.minecraftList.hostname{/lang}</th>
					<th>{lang}wcf.page.minecraftList.rconPort{/lang}</th>
					<th>{lang}wcf.page.minecraftList.creationDate{/lang}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$objects item=object}
					<tr class="jsRow">
						<td class="columnIcon">
							<a href="{link controller='MinecraftEdit' id=$object->minecraftID}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon24 fa-pencil"></span></a>
							<a href="#" class="jsDeleteButton jsTooltip" title="{lang}wcf.global.button.delete{/lang}" data-confirm-message-html="{lang __encode=true}wcf.page.minecraftList.removeConnectionQuestion{/lang}" data-object-id="{@$object->minecraftID}"><span class="icon icon24 fa-times"></span></a>
							<a href="{link controller='MinecraftConsole' id=$object->minecraftID}{/link}" title="{lang}wcf.global.button.console{/lang}" class="jsTooltip"><span class="icon icon24 fa-terminal"></span></a>
							{event name='rowButtons'}
						</td>
						<td class="columnID">{#$object->minecraftID}</td>
						<td class="columnTitle">{$object->connectionName}</td>
						<td class="columnText">{$object->type}</td>
						<td class="columnText">{$object->hostname}</td>
						<td class="columnText">{$object->rconPort}</td>
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
