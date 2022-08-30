<?php

use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\DatabaseTable;

return [
    DatabaseTable::create('wcf_minecraft')
        ->columns([
            NotNullVarchar255DatabaseTableColumn::create('url')
                ->drop()
        ])
];
