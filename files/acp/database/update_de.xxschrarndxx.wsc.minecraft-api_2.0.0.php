<?php

use wcf\system\database\table\DatabaseTable;

return [
    DatabaseTable::create('wcf' . WCF_N . '_minecraft')
        ->drop()
];
