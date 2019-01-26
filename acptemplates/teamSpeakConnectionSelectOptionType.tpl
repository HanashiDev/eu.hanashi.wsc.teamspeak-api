<select id="{$option->optionName}" name="values[{$option->optionName}]">
    <option></option>
    {foreach from=$teamspeakList item=teamspeak}
        <option value="{@$teamspeak->teamspeakID}"{if $teamspeak->teamspeakID == $value} selected{/if}>{$teamspeak->connectionName}</option>
    {/foreach}
</select>