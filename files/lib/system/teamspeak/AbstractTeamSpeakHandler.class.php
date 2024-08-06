<?php

namespace wcf\system\teamspeak;

use Override;
use SensitiveParameter;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\exception\TeamSpeakException;
use wcf\system\SingletonFactory;

/**
 * Handler for saved TeamSpeak connection
 *
 * @author   Peter Lohse <hanashi@hanashi.eu>
 * @copyright    Hanashi
 * @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package  WoltLabSuite\Core\System\TeamSpeak
 */
abstract class AbstractTeamSpeakHandler extends SingletonFactory
{
    /**
     * ID of saved TeamSpeak connection
     *
     * @var int
     */
    protected $teamspeakID;

    /**
     * TeamSpeak server instance
     *
     * @var TeamSpeakConnectionHandler
     */
    protected $tsObj;

    /**
     * hostname of teamspeak server
     *
     * @var string
     */
    protected $hostname;

    #[Override]
    public function init()
    {
        $teamspeak = new Teamspeak($this->teamspeakID);
        if (!$teamspeak->teamspeakID || $teamspeak->teamspeakID != $this->teamspeakID) {
            return;
        }

        $this->hostname = $teamspeak->hostname;
        $this->tsObj = new TeamSpeakConnectionHandler(
            $teamspeak->hostname,
            $teamspeak->queryPort,
            $teamspeak->username,
            $teamspeak->password,
            $teamspeak->virtualServerPort,
            $teamspeak->queryType
        );
        if (\in_array($teamspeak->queryType, ['http', 'https'])) {
            return;
        }

        $this->tsObj->use(['port' => $teamspeak->virtualServerPort]);
        if (!empty($teamspeak->displayName)) {
            // Wenn Namen vergeben ist, dann hinten eine Nummer dran hängen, maximal 20 Versuche
            for ($i = 0; $i < 20; $i++) {
                try {
                    $name = $teamspeak->displayName;
                    if ($i > 0) {
                        $name .= $i;
                    }
                    $this->tsObj->clientupdate(['client_nickname' => $name]);
                    break;
                } catch (TeamSpeakException $e) {
                }
            }
        }
    }

    /**
     * Magic Method since PHP 5
     *
     * @param   string      $method     method name to execute
     * @param   array       $args       method parameters
     * @return  boolean|null
     * @throws  TeamSpeakException
     */
    public function __call($method, $args)
    {
        if (\count($args) > 0) {
            return $this->tsObj->{$method}($args[0]);
        } else {
            return $this->tsObj->{$method}();
        }
    }

    /**
     * get TS server instance
     */
    public function getTS()
    {
        return $this->tsObj;
    }

    /**
     * upload a file to teamspeak
     * dont forget to use "ftinitupload" before this method
     *
     * @param   int     $channelID          ID TeamSpeak channel to upload the file
     * @param   mixed   $filepath           local webserver path of this file
     * @param   string  $filename           the filename to save on TeamSpeak
     * @param   string  $path               the path on TeamSpeak to upload
     * @param   string  $channelPassword    the channel password, if channel has no password leave blank
     * @param   boolean $overwrite          overwrite file on TeamSpeak if exists
     * @param   boolean $resume             resume file upload if canceled
     * @throws  TeamSpeakException
     */
    public function uploadFile(
        $channelID,
        $filepath,
        $filename,
        $path = '/',
        #[SensitiveParameter]
        $channelPassword = '',
        $overwrite = true,
        $resume = false
    ) {
        if (!\file_exists($filepath)) {
            throw new TeamSpeakException('cant find file on local storage');
        }

        $size = \filesize($filepath);

        $reply = $this->ftinitupload([
            'clientftfid' => \random_int(1, 10000),
            'name' => $path . $filename,
            'cid' => $channelID,
            'cpw' => $channelPassword,
            'size' => $size,
            'overwrite' => ($overwrite ? 1 : 0),
            'resume' => ($resume ? 1 : 0),
        ]);
        if (\count($reply) != 1 || empty($reply[0]) || empty($reply[0]['port'])) {
            throw new TeamSpeakException('could not create file');
        }
        $filetransfer = new Filetransfer($this->hostname, $reply[0]['port']);
        $filetransfer->upload($reply[0]['ftkey'], $filepath, $reply[0]['seekpos']);
    }

    /**
     * upload a file from teamspeak
     * dont forget to use "ftinitupload" before this method
     *
     * @param   int     $channelID          ID TeamSpeak channel to upload the file
     * @param   string  $filename           the filename to save on TeamSpeak
     * @param   string  $path               the path on TeamSpeak to upload
     * @param   string  $channelPassword    the channel password, if channel has no password leave blank
     * @return  string  file path to temporary downloaded file
     * @throws  TeamSpeakException
     */
    public function downloadFile(
        $channelID,
        $filename,
        $path = '/',
        #[SensitiveParameter]
        $channelPassword = ''
    ) {
        $reply = $this->ftinitdownload([
            'clientftfid' => \random_int(1, 10000),
            'name' => $path . $filename,
            'cid' => $channelID,
            'cpw' => $channelPassword,
            'seekpos' => 0,
        ]);
        if (
            \count($reply) < 1
            || \count($reply) > 2
            || empty($reply[0])
            || (
                \count($reply) == 1
                && empty($reply[0]['port'])
            )
            || (
                \count($reply) == 2
                && empty($reply[1]['port'])
            )
        ) {
            throw new TeamSpeakException('could not find file');
        }

        $replyTmp = $reply[0];
        if (\count($reply) == 2) {
            $replyTmp = $reply[1];
        }
        $filetransfer = new Filetransfer($this->hostname, $replyTmp['port']);

        return $filetransfer->download($replyTmp['ftkey'], $replyTmp['size']);
    }

    /**
     * method to create a new snapshot of TeamSpeak
     *
     * @return  string
     * @throws  TeamSpeakException
     */
    public function createSnapshot()
    {
        $result = $this->tsObj->execute('serversnapshotcreate', true);
        if (\count($result) != 2) {
            throw new TeamSpeakException('could not create snapshot');
        }

        return $result[0];
    }

    /**
     * method to deploy a created snapshot of TeamSpeak
     *
     * @param   string  $snapshot   the content of the snapshot
     * @throws  TeamSpeakException
     */
    public function deploySnapshot($snapshot)
    {
        return $this->tsObj->execute('serversnapshotdeploy -mapping ' . $snapshot);
    }
}
