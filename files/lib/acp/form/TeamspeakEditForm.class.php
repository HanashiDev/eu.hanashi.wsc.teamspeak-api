<?php
namespace wcf\acp\form;
use wcf\data\teamspeak\Teamspeak;
use wcf\data\teamspeak\TeamspeakAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
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
class TeamspeakEditForm extends TeamspeakAddForm {
    protected $teamspeak;

    /**
     * @inheritDoc
     */
    public function readParameters() {
        parent::readParameters();

        if (isset($_REQUEST['id'])) $this->teamspeakID = intval($_REQUEST['id']);
        $this->teamspeak = new Teamspeak($this->teamspeakID);
        if (!$this->teamspeak->teamspeakID) {
			throw new IllegalLinkException();
        }
        
        $this->connectionName = $this->teamspeak->connectionName;
        $this->hostname = $this->teamspeak->hostname;
        $this->queryType = $this->teamspeak->queryType;
        $this->queryPort = $this->teamspeak->queryPort;
        $this->virtualServerPort = $this->teamspeak->virtualServerPort;
        $this->username = $this->teamspeak->username;
        $this->password = $this->teamspeak->password;
        $this->displayName = $this->teamspeak->displayName;
    }
    
    /**
     * @inheritDoc
     */
	public function save() {
        AbstractForm::save();
        
        $action = new TeamspeakAction([$this->teamspeak], 'update', ['data' => [
			'connectionName' => $this->connectionName,
			'hostname' => $this->hostname,
			'queryType' => $this->queryType,
			'queryPort' => $this->queryPort,
			'virtualServerPort' => $this->virtualServerPort,
			'username' => $this->username,
            'password' => $this->password,
            'displayName' => $this->displayName
		]]);
		$action->executeAction();
		
		$this->saved();
    }
    
    /**
     * @inheritDoc
     */
    public function saved() {
        AbstractForm::save();

        WCF::getTPL()->assign('success', true);
    }
    
    /**
     * @inheritDoc
     */
	public function assignVariables() {
        parent::assignVariables();
        
        WCF::getTPL()->assign([
            'action' => 'edit'
        ]);
	}
}
