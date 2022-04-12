<?php

use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\DatabaseTable;

return [
    DatabaseTable::create('minecraft')
        ->columns([
            ObjectIdDatabaseTableColumn::create('wcf' . WCF_N . '_minecraftID'),
            VarcharDatabaseTableColumn::create('name')
                ->length(20),
            NotNullVarchar255DatabaseTableColumn::create('url'),
            NotNullVarchar255DatabaseTableColumn::create('user'),
            NotNullVarchar255DatabaseTableColumn::create('password'),
            NotNullInt10DatabaseTableColumn::create('creationDate')
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['minecraftID'])
        ])
];
