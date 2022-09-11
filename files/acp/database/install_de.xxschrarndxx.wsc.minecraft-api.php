<?php

use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\DatabaseTable;

return [
    DatabaseTable::create('wcf1_minecraft')
        ->columns([
            ObjectIdDatabaseTableColumn::create('minecraftID'),
            VarcharDatabaseTableColumn::create('title')
                ->length(20),
            NotNullVarchar255DatabaseTableColumn::create('auth'),
            NotNullInt10DatabaseTableColumn::create('creationDate')
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['minecraftID'])
        ])
];
