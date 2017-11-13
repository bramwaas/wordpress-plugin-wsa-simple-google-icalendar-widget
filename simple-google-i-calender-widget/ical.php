<?php

class IcsParsingException extends Exception {}

/**
 * a simple ICS parser.
 *
 * note that this class does not implement all ICS functionality.
 *   bw 20171109 enkele verbeteringen voor start en end in ical.php
 * Version: 0.2.1

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
                $e = $this->parseVevent($eventStr);
                $events[] = $e;
// expand repeating events
				if (isset($e->rrule) && $e->rrule > ' ') {
					// FREQ=MONTHLY;UNTIL=20201108T225959Z;BYMONTHDAY=8
					$rrulel = explode (";", $e->rrule);
					foreach ($rrulel as $rel) {
						$kv = explode("=", $rel);
						$key = $kv[0];
						$value = '';
						$freq = '';
						$mday = 0;
						$from = $e->start;
						$until = '';
						if (count($kv) > 1) {
							$value = $kv[1];
						}
						switch ($key){
							case "FREQ"	:
								$freq = $value;
							break;
							case "UNTIL"	:
								$until = $this->parseIcsDateTime($value);
							break;
							case "BYMONTHDAY"	:
								$mday = $value;
							break;
						} 
					}
					if ($freq = 'MONTHLY') {
						$en = $e;
						$i = 0;
						do  {
						$i = $i + 1;
						$en->start = strtotime("+1 month", $en->start);
						$en->end = strtotime("+1 month", $en->end);
						$events[] = $en;
						} while (/* $en->start < $until && */ $i <12);
					}
					
				}

//
                $parsedUntil = strpos($curstr, self::TOKEN_END_VEVENT) + strlen(self::TOKEN_END_VEVENT) + 1;
                $curstr = substr($curstr, $parsedUntil);
            } else {
                $haveVevent = false;
            }
        } while($haveVevent);

       usort($events, array($this, "eventSortComparer"));

        $this->events = $events;
    }

    public function getFutureEvents($period = 366) {
        // events are already sorted
        $newEvents = array();
        $now = time();
        $enddate = strtotime("+$period day");

        foreach ($this->events as $e) {
            if ((($e->start >= $now) || ($e->end >= $now))
                && $e->start <= $enddate) {
                    $newEvents[] = $e;
                }
        }

        return $newEvents;
    }

    public function getAll() {
        return $this->events;
    }

    private function parseIcsDateTime($datetime, $tzid = '') {
        if (strlen($datetime) < 8) {
            return -1;
        }

        $year = substr($datetime, 0, 4);
        $month = substr($datetime, 4, 2);
        $day = substr($datetime, 6, 2);
        if (strlen($datetime) >= 13)  {
            $hour = substr($datetime, 9, 2);
            $minute = substr($datetime, 11, 2);
        } else {
            $hour = 0;
            $minute = 0;
        }    
        
        // check if it is GMT
        $lastChar = $datetime[strlen($datetime) - 1];

        if ($lastChar == 'Z') {
        	$time = gmmktime($hour, $minute, 0, $month, $day, $year);
        } else {
        	// TODO: correctly handle this.
        	if ($tzid > ' ') {
        		date_default_timezone_set($tzid);
        	} else {
        		date_default_timezone_set(get_option('timezone_string'));
        	}
        	$time = mktime($hour, $minute, 0, $month, $day, $year);
        	date_default_timezone_set('UTC');
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
            $token = "";
            $value = "";
            $tzid = '';
            //bw 20171108 added, because sometimes there is timezone or other info after DTSTART, or DTEND 
//     eg. DTSTART;TZID=Europe/Amsterdam, or  DTSTART;VALUE=DATE:20171203
            $tl = explode(";", $list[0]);
            $token = $tl[0];
            if (count($tl) > 1 ){
            	$dtl = explode("=", $tl[1]);
            	if (count($dtl) > 1 ){
            		if ($dtl[0] == 'TZID') {
            			$tzid = $dtl[1];
            		}
            	}
            }
            if (count($list) > 1) {
                // trim() to remove \r
                $value = trim($list[1]);
                $desc = str_replace(array('\;', '\,', '\r\n', '\n', '\r'), array(';', ',', '<br>', '<br>', '<br>'), htmlspecialchars($value));
            }
            switch($token) {
                case "SUMMARY":
                	$eventObj->summary = $desc;
                    break;
                case "DESCRIPTION":
                	$eventObj->description = $desc;
                    break;
                case "LOCATION":
                	$eventObj->location = $desc;
                    break;
                case "DTSTART":
                    $eventObj->start = $this->parseIcsDateTime($value, $tzid);
                    if ($tzid > ' ') {
                    	$eventObj->tzid = $tzid;
                    }
                    break;
                case "DTEND":
                    $eventObj->end = $this->parseIcsDateTime($value, $tzid);
                    break;
                    // bw 20171108 toegevoegd UID RRULE
               case "UID":
                    $eventObj->uid = $value;
                    break;
               case "RRULE":
               		$eventObj->rrule = $value;
               	break;
               	
            }

        }

        return $eventObj;
    }
}

$p = new IcsParser();
