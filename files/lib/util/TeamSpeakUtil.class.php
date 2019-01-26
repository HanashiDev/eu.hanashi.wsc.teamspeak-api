<?php
namespace wcf\util;

/**
* TeamSpeak utils
*
* @author	Peter Lohse <hanashi@hanashi.eu>
* @copyright	Hanashi
* @license	Freie Lizenz (https://hanashi.eu/freie-lizenz/)
* @package	WoltLabSuite\Core\Util
*/
class TeamSpeakUtil {
    /**
     * sort an multidimensional array by key
     * 
     * @param   array   $array      an array being sorted
     * @param   string  $key        sorted by this key
     * @param   mixed   $sort       the sort order (default: SORT_ASC)
     */
    public static function arraySort($array, $key, $sort = SORT_ASC) {
		$indexes = [];
		foreach ($array as $rowKey => $row) {
			$indexes[$rowKey] = $row[$key];
		}
		array_multisort($indexes, $sort, $array);
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
    public static function getChilds($array, $searchKey, $searchValue, $childColumnName = 'cid') {
		$childKeys = array_keys(array_column($array, $searchKey), $searchValue);
		$childs = [];
		
		foreach ($childKeys as $childKey) {
			$child = $array[$childKey];
			$child['childs'] = self::getChilds($array, $searchKey, $child[$childColumnName], $childColumnName);
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
    public static function escape($rawString) {
        $originalChars = ["\\", '/', ' ', '|', "\a", "\b", "\f", "\n", "\r", "\t", "\v"];
		$escapedChars = ['\\', '\/', '\s', '\p', '\a', '\b', '\f', '\n', '\r', '\t', '\v'];
		return str_replace($originalChars, $escapedChars, $rawString);
    }

    /**
     * unescape an escaped string for TeamSpeak query
     * 
     * @param   string  $escapedString  the escaped string to unescape
     * @return  string
     */
    public static function unescape($escapedString) {
        $escapedChars = ['\\', '\/', '\s', '\p', '\a', '\b', '\f', '\n', '\r', '\t', '\v'];
		$originalChars = ["\\", '/', ' ', '|', "\a", "\b", "\f", "\n", "\r", "\t", "\v"];
		return str_replace($escapedChars, $originalChars, $escapedString);
    }
}