<?php

namespace wcf\system\gridView\admin;

use Override;
use wcf\acp\form\TeamspeakEditForm;
use wcf\data\DatabaseObjectList;
use wcf\data\teamspeak\TeamspeakList;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\interaction\admin\TeamspeakInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\WCF;

final class TeamspeakGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('teamspeakID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('connectionName')
                ->label('wcf.page.teamspeakList.connectionName')
                ->titleColumn()
                ->sortable()
                ->filter(new TextFilter()),
            GridViewColumn::for('hostname')
                ->label('wcf.page.teamspeakList.hostname')
                ->sortable()
                ->filter(new TextFilter()),
            GridViewColumn::for('queryType')
                ->label('wcf.page.teamspeakList.queryType')
                ->sortable(),
            GridViewColumn::for('queryPort')
                ->label('wcf.page.teamspeakList.queryPort')
                ->sortable(),
            GridViewColumn::for('virtualServerPort')
                ->label('wcf.page.teamspeakList.virtualServerPort')
                ->sortable(),
            GridViewColumn::for('username')
                ->label('wcf.page.teamspeakList.username')
                ->sortable()
                ->filter(new TextFilter()),
            GridViewColumn::for('displayName')
                ->label('wcf.page.teamspeakList.displayName')
                ->sortable()
                ->filter(new TextFilter()),
            GridViewColumn::for('creationDate')
                ->label('wcf.page.teamspeakList.creationDate')
                ->renderer(new TimeColumnRenderer())
                ->sortable()
                ->filter(new TimeFilter()),
        ]);

        $provider = new TeamspeakInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(TeamspeakEditForm::class),
        ]);
        $this->setInteractionProvider($provider);

        $this->setSortField('teamspeakID');
        $this->setSortOrder('ASC');
    }

    #[Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.teamspeak.canManageConnection');
    }

    #[Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new TeamspeakList();
    }
}
