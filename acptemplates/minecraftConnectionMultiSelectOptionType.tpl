{if $minecraftList|count}
    <ul class="scrollableCheckboxList" id="{$option->optionName}" style="height: 100px;">
        {foreach from=$minecraftList item=minecraft}
            <li>
                <label><input type="checkbox" name="values[{$option->optionName}][]" value="{@$minecraft->getObjectID()}"
                        {if $minecraft->getObjectID()|in_array:$value} checked{/if}> {$minecraft->getTitle()}</label>
            </li>
        {/foreach}
    </ul>

    <script data-relocate="true">
        require(['Language', 'WoltLabSuite/Core/Ui/ItemList/Filter'], function(Language, UiItemListFilter) {
            Language.addObject({
                'wcf.global.filter.button.visibility': '{jslang}wcf.global.filter.button.visibility{/jslang}',
                'wcf.global.filter.button.clear': '{jslang}wcf.global.filter.button.clear{/jslang}',
                'wcf.global.filter.error.noMatches': '{jslang}wcf.global.filter.error.noMatches{/jslang}',
                'wcf.global.filter.placeholder': '{jslang}wcf.global.filter.placeholder{/jslang}',
                'wcf.global.filter.visibility.activeOnly': '{jslang}wcf.global.filter.visibility.activeOnly{/jslang}',
                'wcf.global.filter.visibility.highlightActive': '{jslang}wcf.global.filter.visibility.highlightActive{/jslang}',
                'wcf.global.filter.visibility.showAll': '{jslang}wcf.global.filter.visibility.showAll{/jslang}'
            });

            new UiItemListFilter('{$option->optionName|encodeJS}');
        });
    </script>
{else}
    <p class="info">{lang}wcf.acp.minecraftSelectOptionType.noServer{/lang}</p>
{/if}