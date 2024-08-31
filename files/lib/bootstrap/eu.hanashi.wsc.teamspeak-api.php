<?php

use wcf\acp\form\TeamspeakAddForm;
use wcf\acp\page\TeamspeakListPage;
use wcf\event\acp\menu\item\ItemCollecting;
use wcf\system\event\EventHandler;
use wcf\system\menu\acp\AcpMenuItem;
use wcf\system\request\LinkHandler;
use wcf\system\style\FontAwesomeIcon;
use wcf\system\WCF;

return static function (): void {
    EventHandler::getInstance()->register(ItemCollecting::class, static function (ItemCollecting $event) {
        $event->register(
            new AcpMenuItem(
                'wcf.acp.menu.link.configuration.teamspeak',
                '',
                'wcf.acp.menu.link.configuration'
            )
        );

        $event->register(
            new AcpMenuItem(
                'wcf.acp.menu.link.management.teamspeak',
                '',
                'wcf.acp.menu.link.configuration'
            )
        );

        if (WCF::getSession()->getPermission('admin.teamspeak.canManageConnection')) {
            $event->register(
                new AcpMenuItem(
                    'wcf.acp.menu.link.configuration.teamspeak.teamspeakList',
                    '',
                    'wcf.acp.menu.link.configuration.teamspeak',
                    LinkHandler::getInstance()->getControllerLink(TeamspeakListPage::class)
                )
            );

            $event->register(
                new AcpMenuItem(
                    'wcf.acp.menu.link.configuration.teamspeak.teamspeakList.add',
                    '',
                    'wcf.acp.menu.link.configuration.teamspeak.teamspeakList',
                    LinkHandler::getInstance()->getControllerLink(TeamspeakAddForm::class),
                    FontAwesomeIcon::fromString('plus;false')
                )
            );
        }
    });
};
