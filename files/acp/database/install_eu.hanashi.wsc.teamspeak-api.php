<?php

use wcf\system\database\table\column\EnumDatabaseTableColumn;
use wcf\system\database\table\column\MediumintDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('wcf1_teamspeak')
        ->columns([
            ObjectIdDatabaseTableColumn::create('teamspeakID'),
            VarcharDatabaseTableColumn::create('connectionName')
                ->length(20),
            VarcharDatabaseTableColumn::create('hostname')
                ->length(50)
                ->notNull(),
            EnumDatabaseTableColumn::create('queryType')
                ->enumValues(['raw', 'ssh', 'http', 'https'])
                ->notNull(),
            MediumintDatabaseTableColumn::create('queryPort')
                ->length(5)
                ->notNull(),
            MediumintDatabaseTableColumn::create('virtualServerPort')
                ->length(5)
                ->notNull(),
            VarcharDatabaseTableColumn::create('username')
                ->length(30),
            VarcharDatabaseTableColumn::create('password')
                ->length(50)
                ->notNull(),
            VarcharDatabaseTableColumn::create('displayName')
                ->length(50),
            NotNullInt10DatabaseTableColumn::create('creationDate'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['teamspeakID']),
        ]),
];
