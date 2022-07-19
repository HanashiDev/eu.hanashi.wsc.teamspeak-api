{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftList'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li><a href="{link controller='MinecraftAdd'}{/link}" class="button"><span
                        class="icon icon16 fa-plus"></span>
                    <span>{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList.add{/lang}</span></a></li>
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
        <table class="table jsObjectActionContainer" data-object-action-class-name="wcf\data\minecraft\MinecraftAction">
            <thead>
                <tr>
                    <th></th>
                    <th>{lang}wcf.page.minecraftList.minecraftID{/lang}</th>
                    <th>{lang}wcf.page.minecraftList.name{/lang}</th>
					<th>{lang}wcf.page.minecraftList.url{/lang}</th>
                    <th>{lang}wcf.page.minecraftList.creationDate{/lang}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$objects item=object}
                    <tr class="jsObjectActionObject" data-object-id="{@$object->minecraftID}">
                        <td class="columnIcon">
                            <a href="{link controller='MinecraftEdit' id=$object->minecraftID}{/link}"
                                title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">
								<span class="icon icon16 fa-pencil"></span>
							</a>
                            {objectAction action="delete" objectTitle=$object->name}
							<span class="icon icon16 fa-wifi minecraftStatusButton pointer jsTooltip" 
								title="{lang}wcf.page.minecraftList.button.status{/lang}">
							</span>
                            {event name='rowButtons'}
                        </td>
                        <td class="columnID">{#$object->minecraftID}</td>
                        <td class="columnTitle">{$object->name}</td>
                        <td class="columnText">{$object->url}</td>
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

<script data-relocate="true">
	require(["xXSchrandXx/Minecraft/MinecraftStatus", "Language"], function(MinecraftStatus, Language) {
		Language.addObject({
			'wcf.page.minecraftList.button.status.result': '{lang}wcf.page.minecraftList.button.status.result{/lang}'
		});
		new MinecraftStatus.default();
	});
</script>
