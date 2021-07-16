<?php

namespace wcf\acp\form;

use wcf\data\teamspeak\TeamspeakAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\TeamSpeakException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\teamspeak\SecretFormField;
use wcf\system\teamspeak\TeamSpeakConnectionHandler;

class TeamspeakAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.teamspeak.canManageConnection'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.teamspeak.teamspeakList.add';

     /**
     * @inheritDoc
     */
    public $objectActionClass = TeamspeakAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TextFormField::create('connectionName')
                        ->label('wcf.page.teamspeakAdd.connectionName')
                        ->description('wcf.page.teamspeakAdd.connectionName.description')
                        ->value('Default')
                        ->maximumLength(20)
                        ->required(),
                    TextFormField::create('hostname')
                        ->label('wcf.page.teamspeakAdd.hostname')
                        ->description('wcf.page.teamspeakAdd.hostname.description')
                        ->value('localhost')
                        ->maximumLength(50)
                        ->required()
                        ->addValidator(new FormFieldValidator('hostnameCheck', function(TextFormField $field) {
                            /** @var IntegerFormField $queryPortField */
                            $queryPortField = $field->getDocument()->getNodeById('queryPort');
                            /** @var TextFormField $usernameField */
                            $usernameField = $field->getDocument()->getNodeById('username');
                            /** @var TextFormField $passwordField */
                            $passwordField = $field->getDocument()->getNodeById('password');
                            /** @var IntegerFormField $virtualServerPortField */
                            $virtualServerPortField = $field->getDocument()->getNodeById('virtualServerPort');
                            /** @var SingleSelectionFormField $queryTypeField */
                            $queryTypeField = $field->getDocument()->getNodeById('queryType');

                            $password = $passwordField->getSaveValue();
                            if ($this->formAction == 'edit' && empty($password)) {
                                $password = $this->formObject->password;
                            }

                            try {
                                $ts = new TeamSpeakConnectionHandler($field->getSaveValue(), $queryPortField->getSaveValue(), $usernameField->getSaveValue(), $password, $virtualServerPortField->getSaveValue(), $queryTypeField->getSaveValue());
                                if (in_array($queryTypeField->getSaveValue(), ['http', 'https'])) {
                                    $ts->serverinfo();
                                }
                            } catch (TeamSpeakException $e) {
                                if (\ENABLE_DEBUG_MODE) {
                                    \wcf\functions\exception\logThrowable($e);
                                }
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.teamspeakAdd.cantConnectDynamic', ['msg' => $e->getMessage()])
                                );
                            } catch (\Exception $e) {
                                if (\ENABLE_DEBUG_MODE) {
                                    \wcf\functions\exception\logThrowable($e);
                                }
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.teamspeakAdd.cantConnect')
                                );
                            }
                        })),
                    SingleSelectionFormField::create('queryType')
                        ->label('wcf.page.teamspeakAdd.queryType')
                        ->description('wcf.page.teamspeakAdd.queryType.description')
                        ->options([
                            'raw' => 'raw',
                            'ssh' => 'ssh',
                            'http' => 'http',
                            'https' => 'https'
                        ])
                        ->required(),
                    IntegerFormField::create('queryPort')
                        ->label('wcf.page.teamspeakAdd.queryPort')
                        ->description('wcf.page.teamspeakAdd.queryPort.description')
                        ->minimum(1)
                        ->maximum(65535)
                        ->value(10011)
                        ->required(),
                    IntegerFormField::create('virtualServerPort')
                        ->label('wcf.page.teamspeakAdd.virtualServerPort')
                        ->description('wcf.page.teamspeakAdd.virtualServerPort.description')
                        ->minimum(1)
                        ->maximum(65535)
                        ->value(9987)
                        ->required(),
                    TextFormField::create('username')
                        ->label('wcf.page.teamspeakAdd.username')
                        ->addDependency(
                            ValueFormFieldDependency::create('usernameQueryTypeDependency')
                                ->fieldId('queryType')
                                ->values(['raw', 'ssh'])
                        )
                        ->value('serveradmin')
                        ->required(),
                    SecretFormField::create('password')
                        ->label('wcf.page.teamspeakAdd.password')
                        ->placeholder(($this->formAction == 'edit') ? 'wcf.acp.updateServer.loginPassword.noChange' : '')
                        ->required(($this->formAction == 'edit') ? false : true),
                    TextFormField::create('displayName')
                        ->label('wcf.page.teamspeakAdd.displayName')
                        ->description('wcf.page.teamspeakAdd.displayName.description')
                        ->value('WSC')
                        ->required()
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['creationDate'] = \TIME_NOW;
        }

        parent::save();
    }
}
