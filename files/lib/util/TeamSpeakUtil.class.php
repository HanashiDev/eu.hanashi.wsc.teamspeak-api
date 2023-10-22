<?php

namespace wcf\util;

/**
* TeamSpeak utils
*
* @author   Peter Lohse <hanashi@hanashi.eu>
* @copyright    Hanashi
* @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package  WoltLabSuite\Core\Util
*/
class TeamSpeakUtil
{
    /**
     * sort an multidimensional array by key
     *
     * @param   array   $array      an array being sorted
     * @param   string  $key        sorted by this key
     * @param   mixed   $sort       the sort order (default: SORT_ASC)
     */
    public static function arraySort($array, $key, $sort = \SORT_ASC)
    {
        $indexes = [];
        foreach ($array as $rowKey => $row) {
            $indexes[$rowKey] = $row[$key];
        }
        \array_multisort($indexes, $sort, $array);

        return $array;
    }

    /**
     * get child of array
     *
     * @param   array   $array              the array with the elements
     * @param   string  $searchKey          the search key ordered by
     * @param   string  $searchValue        the value to searching
     * @param   string  $childColumnName    column name of child id
     * @return  array
     */
    public static function getChilds($array, $searchKey, $searchValue, $childColumnName = 'cid', $level = 1)
    {
        $childKeys = \array_keys(\array_column($array, $searchKey), $searchValue);
        $childs = [];

        foreach ($childKeys as $childKey) {
            $child = $array[$childKey];
            $child['childs'] = self::getChilds(
                $array,
                $searchKey,
                $child[$childColumnName],
                $childColumnName,
                $level + 1
            );
            $child['level'] = $level;
            $childs[] = $child;
        }

        return $childs;
    }

    /**
     * Escape the string for TeamSpeak query
     *
     * @param   string  $rawString      the raw string to escape
     * @return  string
     */
    public static function escape($rawString)
    {
        $originalChars = ["\\", '/', ' ', '|', "\\a", "\\b", "\f", "\n", "\r", "\t", "\v"];
        $escapedChars = ['\\', '\/', '\s', '\p', '\a', '\b', '\f', '\n', '\r', '\t', '\v'];

        return \str_replace($originalChars, $escapedChars, $rawString);
    }

    /**
     * unescape an escaped string for TeamSpeak query
     *
     * @param   string  $escapedString  the escaped string to unescape
     * @return  string
     */
    public static function unescape($escapedString)
    {
        $escapedChars = ['\\', '\/', '\s', '\p', '\a', '\b', '\f', '\n', '\r', '\t', '\v'];
        $originalChars = ["\\", '/', ' ', '|', "\\a", "\\b", "\f", "\n", "\r", "\t", "\v"];

        return \str_replace($escapedChars, $originalChars, $escapedString);
    }

    /**
     * convert a string to binary
     *
     * @param   string  $str    input string
     * @return  string  binary string
     */
    public static function strbin($str)
    {
        if (!\is_string($str)) {
            return false;
        }

        $ret = '';
        for ($i = 0; $i < \strlen($str); $i++) {
            $temp = \decbin(\ord($str[$i]));
            $ret .= \str_repeat("0", 8 - \strlen($temp)) . $temp;
        }

        return $ret;
    }

    /**
     * convert a client unique identifier to a client base64 uid
     *
     * @param   string  $clientUID  client unique identifier
     * @return  string  client base64 uid
     */
    public static function generateClientBase64UID($clientUID)
    {
        $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p'];
        $binaryArr = \str_split(self::strbin(\base64_decode($clientUID)), 4);

        $ret = '';
        foreach ($binaryArr as $binary) {
            $ret .= $chars[\bindec($binary)];
        }

        return $ret;
    }

    /**
     * convert the negative icon ID to the real icon ID
     *
     * @param   int     $iconID     negative icon id
     * @return  int
     */
    public static function getCorrectIconID($iconID)
    {
        return ($iconID < 0) ? (2 ** 32) - ($iconID * -1) : $iconID;
    }
}
