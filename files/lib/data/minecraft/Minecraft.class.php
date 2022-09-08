<?php

namespace wcf\data\minecraft;

use wcf\data\DatabaseObject;

/**
 * Minecraft Data class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Data\Minecraft
 */
class Minecraft extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'minecraft';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'minecraftID';

     /**
      * Returns name
      * @return ?string
      */
      public function getName()
      {
        return $this->name;
      }

      /**
       * Check user an password
       * @return bool
       */
      public function check(string $auth)
      {
        return \hash_equals($this->getAuth(), $auth);
      }

      /**
       * Returns auth data
       * @return ?string
       */
      public function getAuth()
      {
        return $this->auth;
      }

      /**
       * Returns user
       * @return ?string
       */
      public function getUser()
      {
        return explode(':', \base64_decode($this->getAuth()))[0];
      }

      /**
       * Returns password
       * @return ?string
       */
      public function getPassword()
      {
        return explode(':', \base64_decode($this->getAuth()))[1];
      }

    /**
     * Returns createdTimestamp
     * @return ?int
     */
    public function getCreatdDate()
    {
        return $this->createdDate;
    }
}
