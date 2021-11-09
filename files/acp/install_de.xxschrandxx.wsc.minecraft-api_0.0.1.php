<?php

use wcf\system\database\table\column\MediumintDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\WCF;

$tables = [
    DatabaseTable::create('wcf1_minecraft')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('minecraftID')
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
        ]),
];

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();
