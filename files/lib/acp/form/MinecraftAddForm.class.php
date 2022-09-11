<?php

namespace wcf\acp\form;

use wcf\data\minecraft\MinecraftAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\TitleFormField;

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
     * @var \wcf\data\minecraft\Minecraft
     */
    public $formObject;

    /**
     * @inheritDoc
     */
    protected function createForm()
    {
        parent::createForm();

        $this->form->appendChild(
            FormContainer::create('data')
                ->appendChildren([
                    TitleFormField::create()
                        ->value('Default')
                        ->maximumLength(20)
                        ->required(),
                    TextFormField::create('user')
                        ->label('wcf.acp.form.minecraftAdd.user')
                        ->placeholder()
                        ->required(),
                    PasswordFormField::create('password')
                        ->label('wcf.acp.form.minecraftAdd.password')
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

        $user = $this->form->getData()['data']['user'];

        $password = $this->form->getData()['data']['password'];
        if ($this->formAction == 'edit' && empty($password)) {
            $password = $this->formObject->getPassword();
        }

        $this->additionalFields['auth'] = \base64_encode($user . ':' . $password);

        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'user',
                true
            )
        );
        $this->form->getDataHandler()->addProcessor(
            new VoidFormDataProcessor(
                'password',
                true
            )
        );

        parent::save();
    }
}
