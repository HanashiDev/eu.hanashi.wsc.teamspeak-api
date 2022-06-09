<?php

namespace wcf\acp\form;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use wcf\data\minecraft\MinecraftAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\UrlFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\minecraft\MinecraftHandler;
use wcf\util\DateUtil;
use wcf\util\JSON;

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
                    TextFormField::create('name')
                        ->label('wcf.page.minecraftAdd.name')
                        ->description('wcf.page.minecraftAdd.name.description')
                        ->value('Default')
                        ->maximumLength(20)
                        ->required(),
                    UrlFormField::create('url')
                        ->label('wcf.page.minecraftAdd.url')
                        ->description('wcf.page.minecraftAdd.url.description')
                        ->required()
                        ->addValidator(new FormFieldValidator('connectionCheck', function (UrlFormField $field) {
                            /** @var TextFormField $userField */
                            $userField = $field->getDocument()->getNodeById('user');
                            $user = $userField->getSaveValue();
                            /** @var PasswordFormField $passwordField */
                            $passwordField = $field->getDocument()->getNodeById('password');
                            if (empty($passwordField->getSaveValue())) {
                                $password = $this->formObject->password;
                            } else {
                                $password = $passwordField->getSaveValue();
                            }

                            /** @var MinecraftHandler */
                            $handler = new MinecraftHandler($field->getSaveValue(), $user, $password);
                            try {
                                $response = $handler->call('GET');
                                $responseBody = JSON::decode($response->getBody());
                                if (!array_key_exists('applicationDescription', $responseBody)) {
                                    $field->addValidationError(
                                        new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => "Response not WSC-Minecraft-Bridge."])
                                    );
                                }
                                if ($responseBody['applicationDescription'] != "WSC-Minecraft-Bridge") {
                                    $field->addValidationError(
                                        new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => "'applicationDescription' not 'WSC-Minecraft-Bridge'"])
                                    );
                                }
                            } catch (SystemException $e) {
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => "Could not decode JSON"])
                                );
                            } catch (ClientException $e) {
                                switch ($e->getCode()) {
                                    case 401:
                                        $field->addValidationError(
                                            new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.wrongPassword')
                                        );
                                        break;
                                    case 429:
                                        if ($e->hasResponse()) {
                                            $time = DateUtil::getDateTimeByTimestamp(TIME_NOW + (int) $e->getResponse()->getHeaderLine('Retry-After'));
                                            $field->addValidationError(
                                                new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.tooManyConnectionsDynamic', ['time' => $time])
                                            );
                                        } else {
                                            $field->addValidationError(
                                                new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => $e->getMessage()])
                                            );
                                        }
                                        break;
                                    default:
                                        $field->addValidationError(
                                            new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => $e->getMessage()])
                                        );
                                        break;
                                }
                            } catch (GuzzleException $e) {
                                $field->addValidationError(
                                    new FormFieldValidationError('cantConnect', 'wcf.page.minecraftAdd.cantConnectDynamic', ['msg' => $e->getMessage()])
                                );
                            }
                        })),
                    TextFormField::create('user')
                        ->label('wcf.page.minecraftAdd.user')
                        ->required(),
                    PasswordFormField::create('password')
                        ->label('wcf.page.minecraftAdd.password')
                        ->placeholder(($this->formAction == 'edit') ? 'wcf.acp.updateServer.loginPassword.noChange' : '')
                        ->required($this->formAction !== 'edit')
                ])
        );
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        if ($this->formAction == 'create') {
            $this->additionalFields['creationDate'] = TIME_NOW;
        }

        parent::save();
    }
}
