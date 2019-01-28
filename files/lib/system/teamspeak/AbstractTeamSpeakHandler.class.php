<?php
namespace wcf\system\teamspeak;
use wcf\data\teamspeak\Teamspeak;
use wcf\system\exception\TeamSpeakException;
use wcf\system\SingletonFactory;
use wcf\util\CryptoUtil;

/**
* Handler for saved TeamSpeak connection
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\System\TeamSpeak
*/
abstract class AbstractTeamSpeakHandler extends SingletonFactory {
    /**
     * ID of saved TeamSpeak connection
     * 
     * @var int
     */
    protected $teamspeakID;

    /**
     * TeamSpeak server instance
     * 
     * @var TeamSpeak
     */
    protected $tsObj;
    
    /**
     * hostname of teamspeak server
     * 
     * @var string
     */
    protected $hostname;

    /**
     * @inheritDoc
     */
    public function init() {
        $teamspeak = new Teamspeak($this->teamspeakID);
        if (!$teamspeak->teamspeakID || $teamspeak->teamspeakID != $this->teamspeakID) return;

        $this->hostname = $teamspeak->hostname;
        $this->tsObj = new \wcf\system\teamspeak\TeamSpeak($teamspeak->hostname, $teamspeak->queryPort, $teamspeak->username, $teamspeak->password, $teamspeak->queryType);
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
                } catch (TeamSpeakException $e) {}
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
    public function __call($method, $args) {
        if (count($args) > 0) {
            return $this->tsObj->$method($args[0]);
        } else {
            return $this->tsObj->$method();
        }
    }

    /**
     * get TS server instance
     */
    public function getTS() {
        return $this->tsObj;
    }

    /**
     * upload a file to teamspeak
     * dont forget to use "ftinitupload" before this method
     * 
     * @param   int     $channelID          ID TeamSpeak channel to upload the file
     * @param   mixed   $data               the content of file to upload
     * @param   string  $filename           the filename to save on TeamSpeak
     * @param   string  $path               the path on TeamSpeak to upload
     * @param   string  $channelPassword    the channel password, if channel has no password leave blank
     * @param   boolean $overwrite          overwrite file on TeamSpeak if exists
     * @param   boolean $resume             resume file upload if canceled
     * @throws  TeamSpeakException
     */
    public function uploadFile($channelID, $data, $filename, $path = '/', $channelPassword = '', $overwrite = true, $resume = false) {
        $size = strlen($data);

        $reply = $this->ftinitupload([
            'clientftfid' => CryptoUtil::randomInt(1, 10000),
            'name' => $path.$filename,
            'cid' => $channelID,
            'cpw' => $channelPassword,
            'size' => $size,
            'overwrite' => ($overwrite ? 1 : 0),
            'resume' => ($resume ? 1 : 0)
        ]);
        if (count($reply) != 1) {
            throw new TeamSpeakException('could not create file');
        }
        $filetransfer = new Filetransfer($this->hostname, $reply[0]['port']);
        $filetransfer->upload($reply[0]['ftkey'], $data, $reply[0]['seekpos']);
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
    public function downloadFile($channelID, $filename, $path = '/', $channelPassword = '') {
        $reply = $this->ftinitdownload([
            'clientftfid' => CryptoUtil::randomInt(1, 10000),
            'name' => $path.$filename,
            'cid' => $channelID,
            'cpw' => $channelPassword,
            'seekpos' => 0
        ]);
        if (count($reply) != 1) {
            throw new TeamSpeakException('could not find file');
        }
        $filetransfer = new Filetransfer($this->hostname, $reply[0]['port']);
        return $filetransfer->download($reply[0]['ftkey'], $reply[0]['size']);
    }
}
