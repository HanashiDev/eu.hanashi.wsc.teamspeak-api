<?php
namespace wcf\acp\form;
use wcf\data\teamspeak\TeamspeakAction;
use wcf\form\AbstractForm;
use wcf\system\exception\TeamSpeakException;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\teamspeak\TeamSpeakConnectionHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
* form page to create a new teamspeak connection
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\Acp\Form
*/
class TeamspeakAddForm extends AbstractForm {
    /**
     * @inheritDoc
     */
	public $neededPermissions = ['admin.teamspeak.canManageConnection'];
    
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.teamspeak.teamspeakList.add';

    /**
     * TeamSpeak database ID
     * 
     * @var int
     */
    protected $teamspeakID;
    
    /**
     * custom name for teamspeak connection
     * 
     * @var string
     */
    protected $connectionName = 'Default';

    /**
     * hostname of teamspeak server
     * 
     * @var string
     */
    protected $hostname = 'localhost';

    /**
     * query type for connect to teamspeak server (raw or ssh)
     * default: raw
     * 
     * @var string
     */
    protected $queryType;

    /**
     * teamspeak server query port
     * raw port: 10011
     * ssh port: 10022
     * 
     * @var int
     */
    protected $queryPort = 10011;

    /**
     * port of virtal server
     * default port: 9987
     * 
     * @var int
     */
    protected $virtualServerPort = 9987;

    /**
     * username of server query
     * 
     * @var string
     */
    protected $username = 'serveradmin';

    /**
     * server query password
     * 
     * @var string
     */
    protected $password;

    /**
     * display name in TeamSpeak
     * 
     * @var string
     */
    protected $displayName = 'WSC';
    
    /**
     * @inheritDoc
     */
	public function readFormParameters() {
        parent::readFormParameters();
        
        if (isset($_POST['connectionName'])) $this->connectionName = StringUtil::trim($_POST['connectionName']);
        if (isset($_POST['hostname'])) $this->hostname = StringUtil::trim($_POST['hostname']);
        if (isset($_POST['queryType'])) $this->queryType = StringUtil::trim($_POST['queryType']);
        if (isset($_POST['queryPort'])) $this->queryPort = StringUtil::trim($_POST['queryPort']);
        if (isset($_POST['virtualServerPort'])) $this->virtualServerPort = StringUtil::trim($_POST['virtualServerPort']);
        if (isset($_POST['username'])) $this->username = StringUtil::trim($_POST['username']);
        if (isset($_POST['password'])) $this->password = StringUtil::trim($_POST['password']);
        if (isset($_POST['displayName'])) $this->displayName = StringUtil::trim($_POST['displayName']);
	}
    
    /**
     * @inheritDoc
     */
	public function validate() {
        parent::validate();
        
        if (empty($this->connectionName)) {
            throw new UserInputException('connectionName');
        }
        if (empty($this->hostname)) {
            throw new UserInputException('hostname');
        }
        if (!in_array($this->queryType, ['raw', 'ssh'])) {
            throw new UserInputException('queryType', 'invalid');
        }

        // queryPort
        if (empty($this->queryPort)) {
            throw new UserInputException('queryPort');
        }
        if (!is_numeric($this->queryPort)) {
            throw new UserInputException('queryPort', 'noNumber');
        }
        if ($this->queryPort < 1 || $this->queryPort > 65535) {
            throw new UserInputException('queryPort', 'invalid');
        }

        // virtualServerPort
        if (empty($this->virtualServerPort)) {
            throw new UserInputException('virtualServerPort');
        }
        if (!is_numeric($this->virtualServerPort)) {
            throw new UserInputException('virtualServerPort', 'noNumber');
        }
        if ($this->virtualServerPort < 1 || $this->virtualServerPort > 65535) {
            throw new UserInputException('virtualServerPort', 'invalid');
        }

        if (empty($this->username)) {
            throw new UserInputException('username');
        }
        if (empty($this->password)) {
            throw new UserInputException('password');
        }

        try {
            new TeamSpeakConnectionHandler($this->hostname, $this->queryPort, $this->username, $this->password, $this->queryType);
        } catch (TeamSpeakException $e) {
            throw new UserInputException('hostname', 'cantConnect');
        } catch (SystemException $e) {
            // wenn Debug Modus aktiv wird exception geloggt
            if (ENABLE_DEBUG_MODE) {
                $e->getExceptionID();
            }
            throw new UserInputException('hostname', 'cantConnect');
        }
	}
    
    /**
     * @inheritDoc
     */
	public function save() {
        parent::save();
        
        $action = new TeamspeakAction([], 'create', ['data' => [
			'connectionName' => $this->connectionName,
			'hostname' => $this->hostname,
			'queryType' => $this->queryType,
			'queryPort' => $this->queryPort,
			'virtualServerPort' => $this->virtualServerPort,
			'username' => $this->username,
            'password' => $this->password,
            'displayName' => $this->displayName,
			'creationDate' => TIME_NOW
		]]);
		$action->executeAction();
		
		$this->saved();
	}
    
    /**
     * @inheritDoc
     */
	public function saved() {		
		parent::saved();
		
        WCF::getTPL()->assign('success', true);

        // reset Data
        $this->teamspeakID = null;
        $this->connectionName = 'Default';
        $this->hostname = 'localhost';
        $this->queryType = 'raw';
        $this->queryPort = 10011;
        $this->virtualServerPort = 9987;
        $this->username = 'serveradmin';
        $this->password = null;
        $this->displayName = 'WSC';
	}
    
    /**
     * @inheritDoc
     */
	public function assignVariables() {
        parent::assignVariables();
        
        WCF::getTPL()->assign([
            'action' => 'add',
            'teamspeakID' => $this->teamspeakID,
            'connectionName' => $this->connectionName,
            'hostname' => $this->hostname,
            'queryType' => $this->queryType,
            'queryPort' => $this->queryPort,
            'virtualServerPort' => $this->virtualServerPort,
            'username' => $this->username,
            'password' => $this->password,
            'displayName' => $this->displayName
        ]);
	}
}
