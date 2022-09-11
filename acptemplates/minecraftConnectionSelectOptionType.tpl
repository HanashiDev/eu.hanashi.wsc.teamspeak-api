{if $minecraftList|count}
    <select id="{$option->optionName}" name="values[{$option->optionName}]">
        <option></option>
        {foreach from=$minecraftList item=minecraft}
            <option value="{@$minecraft->getObjectID()}" {if $minecraft->getObjectID() == $value} selected{/if}>
                {$minecraft->getTitle()}</option>
        {/foreach}
    </select>
{else}
    <p class="info">{lang}wcf.acp.minecraftSelectOptionType.noServer{/lang}</p>
{/if}