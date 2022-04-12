{if $minecraftList|count}
    <select id="{$option->optionName}" name="values[{$option->optionName}]">
        <option></option>
        {foreach from=$minecraftList item=minecraft}
            <option value="{@$minecraft->minecraftID}" {if $minecraft->minecraftID == $value} selected{/if}>
                {$minecraft->name}</option>
        {/foreach}
    </select>
{else}
    <p class="info">{lang}wcf.acp.minecraftSelectOptionType.noServer{/lang}</p>
{/if}