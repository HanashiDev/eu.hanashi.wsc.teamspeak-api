<?php

namespace wcf\util;

/**
 * Minecrat util class
 *
 * @author   xXSchrandXx
 * @license  Creative Commons Zero v1.0 Universal (http://creativecommons.org/publicdomain/zero/1.0/)
 * @package  WoltLabSuite\Core\Util
 * @see https://wiki.vg/Mojang_API
 */
class MinecraftUtil
{
    const UUID_PATTERN = '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$';

    /**
     * Check weather given uuid is a valid uuid
     * @param string $uuid uuid to check
     * @return int|false
     * @see \preg_match
     */
    public static function validUUID(string $uuid)
    {
        return \preg_match('/' . self::UUID_PATTERN . '/', $uuid);
    }
}
