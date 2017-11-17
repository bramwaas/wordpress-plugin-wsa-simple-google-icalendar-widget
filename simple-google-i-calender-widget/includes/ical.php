<?php

class IcsParsingException extends Exception {}

/**
 * a simple ICS parser.
 *
 * note that this class does not implement all ICS functionality.
 *   bw 20171109 enkele verbeteringen voor start en end in ical.php
 * Version: 0.3.3

 */
class IcsParser {

    const TOKEN_BEGIN_VEVENT = "\nBEGIN:VEVENT";
    const TOKEN_END_VEVENT = "\nEND:VEVENT";

    public function parse($str ,  $penddate,  $pcount) {

        $curstr = $str;
        $haveVevent = true;
        $events = array();
        $now = time();
        
        

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
// Recurring event?
		if (isset($e->rrule) && $e->rrule !== '') {
/* Recurring event, parse RRULE in associative array add appropriate duplicate events
 * only for frequencies YEARLY, MONTHLY,  WEEKLY and DAYLY
 * frequency by multiplied by INTERVAL (default INTERVAL = 1)
 * in a period starting after today() and after the first instance of the event, ending
 * not after the last day of the displayed period, and not after the last instance defined by UNTIL or COUNT*Frequency*INTERVAL
 * BY: only parse BYDAY, BYMONTH, BYMONTHDAY, possibly with multiple instances (eg BYDAY=TU,WE or BYMONTHDAY=1,2,3,4,5)
 * not parsed: BYYEARDAY, BYSETPOS, BYHOUR, BYMINUTE, WKST
 * examples:
 * FREQ=MONTHLY;UNTIL=20201108T225959Z;BYMONTHDAY=8 Every 8th of the month until 20201108
 * FREQ=MONTHLY;UNTIL=20201010T215959Z;BYDAY=2SA Monthly  2nde saturday until 20201010.
 * FREQ=MONTHLY;BYMONTHDAY=5 Monthly the 5th
 * FREQ=WEEKLY;INTERVAL=3;BYDAY=SU,SA Every 3 weeks on sunday and saturday
 * FREQ=WEEKLY;COUNT=10;BYDAY=MO,TU,WE,TH,FR Every week 10 times on weekdays
 * FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU every year last sunday of october
 * FREQ=DAILY;COUNT=5;INTERVAL=7 Every 7 days,5 times

 */
			$edtstart = new DateTime('@' . $e->start, $timezone);
			$edtendd   = new DateTime('@' . $e->end, $timezone);
			$eivlength = $edtstart->diff($edtendd);
			
			$rrules = array();
                $rruleStrings = explode(';', $e->rrule);
                foreach ($rruleStrings as $s) {
                    list($k, $v) = explode('=', $s);
                    $rrules[strtolower ($k)] = strtoupper ($v);
                }
                // Get frequency and other values when set
                $frequency = $rrules['freq'];
                $interval = (isset($rrules['interval']) && $rrules['interval'] !== '') ? $rrules['interval'] : 1;
                $until = (isset($rrules['until'])) ? $this->parseIcsDateTime($rrules['until']) : $penddate;
               	$count = (isset($rrules['count'])) ? $rrules['count'] : 0;
               	$byday = (isset($rrules['byday'])) ? $rrules['byday'] : '';
               	$bymonth = (isset($rrules['bymonth'])) ? $rrules['bymonth'] : '';
               	$bymonthday = (isset($rrules['bymonthday'])) ? $rrules['bymonthday'] : '';
               	$timezone = new DateTimeZone((isset($e->tzid)&& $e->tzid !== '') ? $e->tzid : get_option('timezone_string'));
               	
                // Get Start timestamp
                /*
                $startTimestamp = $initialStart->getTimestamp();
                if (isset($anEvent['DTEND'])) {
                    $endTimestamp = $initialEnd->getTimestamp();
                } elseif (isset($anEvent['DURATION'])) {
                    $duration = end($anEvent['DURATION_array']);
                    $endTimestamp = $this->parseDuration($anEvent['DTSTART'], $duration);
                } else {
                    $endTimestamp = $anEvent['DTSTART_array'][2];
                }
                $eventTimestampOffset = $endTimestamp - $startTimestamp;
                // Get Interval
                $interval = (isset($rrules['INTERVAL']) && $rrules['INTERVAL'] !== '') ? $rrules['INTERVAL'] : 1;
             */
               	$i = 1;
               	$cen = 0;
               	switch ($frequency){
               		case "YEARLY"	:
               		case "MONTHLY"	:
               		case "WEEKLY"	:
               		case "DAILY"	:
               			$dateinterval = new DateInterval('P' . $interval . substr($frequency,0,1));
        
               			$newstart = clone $edtstart;
               			
               			$tzoffsetprev = $timezone->getOffset ( $newstart);
               			$newstart->add($dateinterval);
               			$newend = clone $newstart;
               			$newend->add($eivlength);
               			while ( $newstart->getTimestamp() < $until
           						 &&   $i < 12
               					&& ($count == 0 || $i < $count  )            						)
           				{
           					$tzadd = $tzoffsetprev - $timezone->getOffset ( $newstart);
           					$tzoffsetprev = $timezone->getOffset ( $newstart);
           					$newend->add($dateinterval);
           					$newend->add($eivlength);
           					if ($tzadd != 0) {
           						$tziv = new DateInterval('PT' . abs($tzadd) . 'S');
           						if ($tzadd < 0) {
           							$tziv->invert = 1;
           						}
           						$newstart->add($tziv);
           						$newend->setTimestamp($newstart->getTimestamp()) ;
           						$newend->add($eivlength);
           					}
           					if ($newstart->getTimestamp() >= $now 
           						&& $newstart->getTimestamp() <= $penddate
           						&& $cen < $pcount) {		
           						$cen++;
           						$en =  clone $e;
           						$en->start = $newstart->getTimestamp();
           						$en->end = $newend->getTimestamp();
           						$en->uid = $i . $e->uid;
           						$en->summary = 'nr:' . $i . ' cen:' . $cen . ' '. $e->summary;
           						$events[] = $en;
           						}
           					$i++;
           					$newstart->add($dateinterval);
           				} 
                 	}
               	/* oud
					// 
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
						} while ( $en->start < $until &&  $i <12);
					}
		    */
					
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

    public function getFutureEvents($penddate ) {
        // events are already sorted
        $newEvents = array();
        $now = time();

        foreach ($this->events as $e) {
            if ((($e->start >= $now) || ($e->end >= $now))
            		&& $e->start <= $penddate) {
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
