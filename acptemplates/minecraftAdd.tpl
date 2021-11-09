{include file='header' pageTitle='wcf.acp.menu.link.configuration.minecraft.minecraftList.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList.{$action}{/lang}</h1>
	</div>

    <nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='MinecraftList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.configuration.minecraft.minecraftList{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{@$form->getHtml()}

<script data-relocate="true">
	require(["Hanashi/Minecraft/ServerAdd", "Language"], function(ServerAdd, Language) {
		Language.addObject({
			'wcf.page.minecraftAdd.password': '{lang}wcf.page.minecraftAdd.password{/lang}',
		});
		new ServerAdd.default();
	});
</script>

{include file='footer'}
