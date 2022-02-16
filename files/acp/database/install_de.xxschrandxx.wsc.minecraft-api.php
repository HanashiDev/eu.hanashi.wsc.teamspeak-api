<?php

use wcf\system\database\table\column\EnumDatabaseTableColumn;
use wcf\system\database\table\column\MediumintDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\DatabaseTable;

return [
    DatabaseTable::create('wcf' . WCF_N . '_minecraft')
        ->columns([
            ObjectIdDatabaseTableColumn::create('minecraftID')
                ->autoIncrement(),
            VarcharDatabaseTableColumn::create('connectionName')
                ->length(20),
            VarcharDatabaseTableColumn::create('hostname')
                ->length(50)
                ->notNull(),
            MediumintDatabaseTableColumn::create('rconPort')
                ->length(5)
                ->notNull(),
            VarcharDatabaseTableColumn::create('password')
                ->length(50)
                ->notNull(),
            NotNullInt10DatabaseTableColumn::create('creationDate'),
            EnumDatabaseTableColumn::create('type')
                ->enumValues(['vanilla', 'spigot', 'bungee']),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['minecraftID']),
        ]),
];