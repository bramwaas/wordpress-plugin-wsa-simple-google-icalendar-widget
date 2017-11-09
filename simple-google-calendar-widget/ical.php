<?php

class IcsParsingException extends Exception {}

/**
 * a simple ICS parser.
 *
 * note that this class does not implement all ICS functionality.
 */
class IcsParser {

    const TOKEN_BEGIN_VEVENT = "\nBEGIN:VEVENT";
    const TOKEN_END_VEVENT = "\nEND:VEVENT";

    public function parse($str) {

        $curstr = $str;
        $haveVevent = true;
        $events = array();

        do {
            $startpos = strpos($curstr, self::TOKEN_BEGIN_VEVENT);
            if ($startpos !== false) {
                // remove BEGIN_VEVENT and END:VEVENT
                // +2: because \r\n at the end
                $eventStrStart = $startpos + strlen(self::TOKEN_BEGIN_VEVENT) + 2;
                $eventStr = substr($curstr, $eventStrStart);

                $endpos = strpos($eventStr, self::TOKEN_END_VEVENT) - 1;

                if ($endpos === false) {
                    throw new IcsParsingException('No valid END:VEVENT found');
                }

                $eventStr = substr($eventStr, 0, $endpos);
                $events[] = $this->parseVevent($eventStr);

                $parsedUntil = strpos($curstr, self::TOKEN_END_VEVENT) + strlen(self::TOKEN_END_VEVENT) + 1;
                $curstr = substr($curstr, $parsedUntil);
            } else {
                $haveVevent = false;
            }
        } while($haveVevent);

        usort($events, array($this, "eventSortComparer"));

        $this->events = $events;
    }

    public function getFutureEvents() {
        // events are already sorted
        $newEvents = array();
        $now = time();

        foreach ($this->events as $e) {
            if (($e->start >= $now) ||
                ($e->end >= $now)) {
                    $newEvents[] = $e;
                }
        }

        return $newEvents;
    }

    public function getAll() {
        return $this->events;
    }

    private function parseIcsDateTime($datetime) {
        if (strlen($datetime) < 13) {
            return -1;
        }

        $year = substr($datetime, 0, 4);
        $month = substr($datetime, 4, 2);
        $day = substr($datetime, 6, 2);

        $hour = substr($datetime, 9, 2);
        $minute = substr($datetime, 11, 2);

        // check if it is GMT
        $lastChar = $datetime[strlen($datetime) - 1];

        if ($lastChar == 'Z') {
            $time = gmmktime($hour, $minute, 0, $month, $day, $year);
        } else {
            // TODO: correctly handle this.
            $time = mktime($hour, $minute, 0, $month, $day, $year);
        }

        return $time;
    }

    private function eventSortComparer($a, $b) {
        if ($a->start == $b->start) {
            return 0;
        } else if($a->start > $b->start) {
            return 1;
        } else {
            return -1;
        }
    }

    public function parseVevent($eventStr) {
        $lines = explode("\n", $eventStr);
        $eventObj = new StdClass;

        foreach($lines as $l) {
            $list = explode(":", $l);

            // to avoid "undefined index..."
            $token = $list[0];
            $value = "";
            if (count($list) > 1) {
                // trim() to remove \r
                $value = trim($list[1]);
            }

            switch($token) {
                case "SUMMARY":
                    $eventObj->summary = $value;
                    break;
                case "DESCRIPTION":
                    $eventObj->description = $value;
                    break;
                case "LOCATION":
                    $eventObj->location = $value;
                    break;
                case "DTSTART":
                    $eventObj->start = $this->parseIcsDateTime($value);
                    break;
                case "DTEND":
                    $eventObj->end = $this->parseIcsDateTime($value);
                    break;
            }

        }

        return $eventObj;
    }
}

$p = new IcsParser();
