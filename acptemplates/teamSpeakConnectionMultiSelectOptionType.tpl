{if $teamspeakList|count}
	<ul class="scrollableCheckboxList" id="{$option->optionName}" style="height: 100px;">
		{foreach from=$teamspeakList item=teamspeak}
			<li>
				<label><input type="checkbox" name="values[{$option->optionName}][]" value="{@$teamspeak->teamspeakID}"{if $teamspeak->teamspeakID|in_array:$value} checked{/if}> {$teamspeak->connectionName}</label>
			</li>
		{/foreach}
	</ul>

	<script data-relocate="true">
		require(['WoltLabSuite/Core/Ui/ItemList/Filter'], function(UiItemListFilter) {
			{jsphrase name='wcf.global.filter.button.visibility'}
			{jsphrase name='wcf.global.filter.button.clear'}
			{jsphrase name='wcf.global.filter.error.noMatches'}
			{jsphrase name='wcf.global.filter.placeholder'}
			{jsphrase name='wcf.global.filter.visibility.activeOnly'}
			{jsphrase name='wcf.global.filter.visibility.highlightActive'}
			{jsphrase name='wcf.global.filter.visibility.showAll'}
			
			new UiItemListFilter('{$option->optionName|encodeJS}');
		});
	</script>
{else}
	<p class="info">{lang}wcf.acp.teamSpeakSelectOptionType.noBot{/lang}</p>
{/if}
