<?php

namespace wcf\acp\form;

use wcf\data\IStorableObject;
use wcf\data\minecraft\Minecraft;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\IFormDocument;

/**
 * MinecraftEdit Form class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Acp\Form
 */
class MinecraftEditForm extends MinecraftAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        $minecraftID = 0;
        if (isset($_REQUEST['id'])) {
            $minecraftID = (int)$_REQUEST['id'];
        }
        $this->formObject = new Minecraft($minecraftID);
        if (!$this->formObject->minecraftID) {
            throw new IllegalLinkException();
        }
    }

    /**
     * @inheritDoc
     */
    public function setFormObjectData()
    {

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'addUser',
                null,
                function (IFormDocument $document, array $data, IStorableObject $object) {
                    $user = explode(':', base64_decode($object->auth))[0];
                    $data['user'] = $user;
                    return $data;
                }
            )
        );

        parent::setFormObjectData();

        /** @var PasswordFormField $passwordField */
        $passwordField = $this->form->getNodeById('password');
        $passwordField->value('');
    }
}
