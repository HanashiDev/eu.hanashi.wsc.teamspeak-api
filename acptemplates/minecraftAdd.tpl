{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftList.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
				<a href="{link controller='MinecraftList'}{/link}" class="button">
					<span class="icon icon16 fa-list"></span>
                	<span>{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList{/lang}</span>
				</a>
			</li>
            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}