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
