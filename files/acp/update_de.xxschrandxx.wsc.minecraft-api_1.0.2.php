<?php

use wcf\system\database\table\column\EnumDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\WCF;

$tables = [
    DatabaseTable::create('wcf1_minecraft')
        ->columns([
            EnumDatabaseTableColumn::create('type')
                ->enumValues(['vanilla', 'spigot', 'bungee']),
        ]),
];

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();
