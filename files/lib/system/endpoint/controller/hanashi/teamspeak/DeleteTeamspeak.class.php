<?php

namespace wcf\system\endpoint\controller\hanashi\teamspeak;

use Laminas\Diactoros\Response\JsonResponse;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\teamspeak\Teamspeak;
use wcf\data\teamspeak\TeamspeakAction;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

#[DeleteRequest('/hanashi/teamspeak/{id:\d+}')]
final class DeleteTeamspeak implements IController
{
    #[Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $bot = Helper::fetchObjectFromRequestParameter($variables['id'], Teamspeak::class);

        WCF::getSession()->checkPermissions(['admin.teamspeak.canManageConnection']);

        $action = new TeamspeakAction([$bot], 'delete');
        $action->executeAction();

        return new JsonResponse([]);
    }
}
