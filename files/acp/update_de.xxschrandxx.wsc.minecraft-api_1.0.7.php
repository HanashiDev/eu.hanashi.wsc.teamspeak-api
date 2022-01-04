<?php

use wcf\data\minecraft\MinecraftEditor;
use wcf\data\minecraft\MinecraftList;
use wcf\system\database\table\column\EnumDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\DatabaseTableChangeProcessor;
use wcf\system\WCF;

$tables = [
    DatabaseTable::create('wcf1_minecraft')
        ->columns([
            VarcharDatabaseTableColumn::create('password')
                ->length(255)
                ->notNull(),
        ]),
];

$minecraftList = new MinecraftList();
$minecraftList->readObjects();
$minecrafts = $minecraftList->getObjects();

(new DatabaseTableChangeProcessor(
    $this->installation->getPackage(),
    $tables,
    WCF::getDB()->getEditor()
))->process();

foreach ($minecrafts as &$minecraft) {
    $minecraftEditor = new MinecraftEditor($minecraft);
    $minecraftEditor->update();
}