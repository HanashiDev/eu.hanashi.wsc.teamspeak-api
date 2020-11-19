{if $teamspeakList|count}
    <select id="{$option->optionName}" name="values[{$option->optionName}][]" multiple size="10">
        {foreach from=$teamspeakList item=teamspeak}
            <option value="{@$teamspeak->teamspeakID}"{if $teamspeak->teamspeakID|in_array:$value} selected{/if}>{$teamspeak->connectionName}</option>
        {/foreach}
    </select>
{else}
    <p class="info">{lang}wcf.acp.teamSpeakSelectOptionType.noBot{/lang}</p>
{/if}