<?php

namespace wcf\system\teamspeak;

use Override;
use wcf\system\exception\TeamSpeakException;
use wcf\util\TeamSpeakUtil;

/**
 * Handler for TeamSpeak connection
 *
 * @author   Peter Lohse <hanashi@hanashi.eu>
 * @copyright    Hanashi
 * @license  Freie Lizenz (https://hanashi.eu/freie-lizenz/)
 * @package  WoltLabSuite\Core\System\TeamSpeak
 */
abstract class AbstractTeamSpeakQueryHandler implements ITeamSpeakHandler
{
    #[Override]
    public function call($method, $args)
    {
        $command = $method;
        if (\count($args) > 0) {
            foreach ($args as $arg) {
                if (\is_array($arg)) {
                    foreach ($arg as $key => $val) {
                        if (\is_numeric($key)) {
                            $command .= ' ' . $val;
                        } else {
                            $command .= ' ' . $key . '=' . TeamSpeakUtil::escape($val);
                        }
                    }
                } else {
                    $command .= ' ' . $arg;
                }
            }
        }
        $result = $this->execute($command);

        return $this->parseResult($result);
    }

    #[Override]
    public function parseResult($result)
    {
        $resultArr = [];
        $error = [];

        foreach ($result as $resultPart) {
            $resultSplitted = \explode('|', $resultPart);
            foreach ($resultSplitted as $resultRow) {
                $row = [];
                $rowSplitted = \explode(' ', $resultRow);
                if (\count($rowSplitted) == 0) {
                    continue;
                }
                if ($rowSplitted[0] == 'error') {
                    $error = $this->parseRow($rowSplitted);
                } else {
                    $row = $this->parseRow($rowSplitted);
                    if (\count($row) > 0) {
                        $resultArr[] = $row;
                    }
                }
            }
        }
        if (empty($error['msg'])) {
            throw new TeamSpeakException('Unknown teamspeak result: ' . \print_r($result, true));
        }
        if ($error['msg'] != 'ok') {
            throw new TeamSpeakException($error['msg']);
        }

        return $resultArr;
    }

    /**
     * parse reply row
     *
     * @param   array       $row        Row of result
     * @return  array
     */
    protected function parseRow($row)
    {
        $rowArr = [];
        foreach ($row as $column) {
            $columnSplitted = \explode('=', $column, 2);
            if (\count($columnSplitted) > 1) {
                $rowArr[$columnSplitted[0]] = TeamSpeakUtil::unescape($columnSplitted[1]);
            }
        }

        return $rowArr;
    }
}
