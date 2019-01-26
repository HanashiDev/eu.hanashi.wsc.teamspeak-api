{include file='header' pageTitle='wcf.acp.menu.link.configuration.teamspeak.teamspeakList.'|concat:$action}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList.{$action}{/lang}</h1>
	</div>

    <nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='TeamspeakList'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}wcf.acp.menu.link.configuration.teamspeak.teamspeakList{/lang}</span></a></li>
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
{/if}

<form method="post" action="{if $action == 'add'}{link controller='TeamspeakAdd'}{/link}{else}{link controller='TeamspeakEdit' id=$teamspeakID}{/link}{/if}">
	<section class="section">
		<dl{if $errorField == 'connectionName'} class="formError"{/if}>
			<dt><label for="connectionName">{lang}wcf.page.teamspeakAdd.connectionName{/lang}</label></dt>
			<dd>
				<input type="text" name="connectionName" id="connectionName" value="{$connectionName}" required>
				{if $errorField == 'connectionName'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{/if}
					</small>
				{/if}
                <small>{lang}wcf.page.teamspeakAdd.connectionName.description{/lang}</small>
			</dd>
		</dl>
		<dl{if $errorField == 'hostname'} class="formError"{/if}>
			<dt><label for="hostname">{lang}wcf.page.teamspeakAdd.hostname{/lang}</label></dt>
			<dd>
				<input type="text" name="hostname" id="hostname" value="{$hostname}" required>
				{if $errorField == 'hostname'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else if $errorType == 'cantConnect'}
							{lang}wcf.page.teamspeakAdd.cantConnect{/lang}
						{/if}
					</small>
				{/if}
                <small>{lang}wcf.page.teamspeakAdd.hostname.description{/lang}</small>
			</dd>
		</dl>
		<dl{if $errorField == 'queryType'} class="formError"{/if}>
			<dt><label for="queryType">{lang}wcf.page.teamspeakAdd.queryType{/lang}</label></dt>
			<dd>
				<select id="queryType" name="queryType" required>
                    <option value="raw"{if $queryType == 'raw'} selected{/if}>raw</option>
                    <option value="ssh"{if $queryType == 'ssh'} selected{/if}>ssh</option>
                </select>
				{if $errorField == 'queryType'}
					<small class="innerError">
						{if $errorType == 'invalid'}
							{lang}wcf.page.teamspeakAdd.invalidQueryType{/lang}
						{/if}
					</small>
				{/if}
                <small>{lang}wcf.page.teamspeakAdd.queryType.description{/lang}</small>
			</dd>
		</dl>
		<dl{if $errorField == 'queryPort'} class="formError"{/if}>
			<dt><label for="queryPort">{lang}wcf.page.teamspeakAdd.queryPort{/lang}</label></dt>
			<dd>
				<input type="number" name="queryPort" id="queryPort" value="{$queryPort}" min="1" max="65535" required>
				{if $errorField == 'queryPort'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else if $errorType == 'noNumber'}
							{lang}wcf.page.teamspeakAdd.numberOutOfRange{/lang}
						{else if $errorType == 'invalid'}
							{lang}wcf.page.teamspeakAdd.notANumber{/lang}
						{/if}
					</small>
				{/if}
                <small>{lang}wcf.page.teamspeakAdd.queryPort.description{/lang}</small>
			</dd>
		</dl>
		<dl{if $errorField == 'virtualServerPort'} class="formError"{/if}>
			<dt><label for="virtualServerPort">{lang}wcf.page.teamspeakAdd.virtualServerPort{/lang}</label></dt>
			<dd>
				<input type="number" name="virtualServerPort" id="virtualServerPort" value="{$virtualServerPort}" min="1" max="65535" required>
				{if $errorField == 'virtualServerPort'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{else if $errorType == 'noNumber'}
							{lang}wcf.page.teamspeakAdd.numberOutOfRange{/lang}
						{else if $errorType == 'invalid'}
							{lang}wcf.page.teamspeakAdd.notANumber{/lang}
						{/if}
					</small>
				{/if}
                <small>{lang}wcf.page.teamspeakAdd.virtualServerPort.description{/lang}</small>
			</dd>
		</dl>
		<dl{if $errorField == 'username'} class="formError"{/if}>
			<dt><label for="username">{lang}wcf.page.teamspeakAdd.username{/lang}</label></dt>
			<dd>
				<input type="text" name="username" id="username" value="{$username}" required>
				{if $errorField == 'username'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>
		<dl{if $errorField == 'password'} class="formError"{/if}>
			<dt><label for="password">{lang}wcf.page.teamspeakAdd.password{/lang}</label></dt>
			<dd>
				<input type="password" name="password" id="password" value="{$password}" required>
				{if $errorField == 'username'}
					<small class="innerError">
						{if $errorType == 'empty'}
							{lang}wcf.global.form.error.empty{/lang}
						{/if}
					</small>
				{/if}
			</dd>
		</dl>
	</section>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}