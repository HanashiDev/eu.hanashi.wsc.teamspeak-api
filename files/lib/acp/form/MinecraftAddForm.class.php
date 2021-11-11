<?php

namespace wcf\acp\form;

use wcf\data\minecraft\MinecraftAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\MinecraftException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\minecraft\SecretFormField;
use wcf\system\minecraft\MinecraftConnectionHandler;

/**
 * MinecraftAdd Form class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.minecraft.canManageConnection'];

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.configuration.minecraft.minecraftList.add';

     /**
     * @inheritDoc
     */
    public $objectActionClass = MinecraftAction::class;

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
                        ->label('wcf.page.minecraftAdd.connectionName')
                        ->description('wcf.page.minecraftAdd.connectionName.description')
                        ->value('Default')
                        ->maximumLength(20)
                        ->required(),
                    TextFormField::create('hostname')
                        ->label('wcf.page.minecraftAdd.hostname')
                        ->description('wcf.page.minecraftAdd.hostname.description')
                        ->value('localhost')
                        ->maximumLength(50)
                        ->required()
                        ->addValidator(new FormFieldValidator('hostnameCheck', function (TextFormField $field) {
                            /** @var IntegerFormField $rconPortField */
                            $rconPortField = $field->getDocument()->getNodeById('rconPort');
                            /** @var TextFormField $passwordField */
                            $passwordField = $field->getDocument()->getNodeById('password');

                            $password = $passwordField->getSaveValue();
                            if ($this->formAction == 'edit' && empty($password)) {
                                $password = $this->formObject->password;
                            }

                            try {
                                $mc = new MinecraftConnectionHandler($field->getSaveValue(), $rconPortField->getSaveValue(), $password);
                                if (!$mc->login()) {
                                    $field->addValidationError(
                                        new FormFieldValidationError('wrongPassword', 'wcf.page.minecraftAdd.cantConnect')
                                    );
                                }
                            } catch (MinecraftException $e) {
                                if (\ENABLE_DEBUG_MODE) {
                                    \wcf\functions\exception\logThrowable($e);
                                }
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => $e->getMessage()])
                                );
                            } catch (\Exception $e) {
                                if (\ENABLE_DEBUG_MODE) {
                                    \wcf\functions\exception\logThrowable($e);
                                }
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnect')
                                );
                            }
                        })),
                    IntegerFormField::create('rconPort')
                        ->label('wcf.page.minecraftAdd.rconPort')
                        ->description('wcf.page.minecraftAdd.rconPort.description')
                        ->minimum(1)
                        ->maximum(65535)
                        ->value(25575)
                        ->required(),
                    SecretFormField::create('password')
                        ->label('wcf.page.minecraftAdd.password')
                        ->placeholder(($this->formAction == 'edit') ? 'wcf.acp.updateServer.loginPassword.noChange' : '')
                        ->required(($this->formAction == 'edit') ? false : true),
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
