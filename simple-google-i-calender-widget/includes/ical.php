<?php

class IcsParsingException extends Exception {}

/**
 * a simple ICS parser.
 *
 * note that this class does not implement all ICS functionality.
 *   bw 20171109 enkele verbeteringen voor start en end in ical.php
 * Version: 0.4.0

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
			$timezone = new DateTimeZone((isset($e->tzid)&& $e->tzid !== '') ? $e->tzid : get_option('timezone_string'));
			$edtstart = new DateTime('@' . $e->start, $timezone);
			$edtstartmday = $edtstart->format('j');
			$edtstartmon = $edtstart->format('n');
			$egdstart = getdate($e->start);
			//      example 2017-11-16
			// 		$egdstart['weekday'] 'Monday' - 'Sunday' example 'Thursday'
			//		$egdstart['mon']  monthnr in year 1 - 12 example 11  (november) 
			//		$egdstart['mday'] day in the month 1 - 31 example 16
			$edtendd   = new DateTime('@' . $e->end, $timezone);
			$eduration = $edtstart->diff($edtendd);

			
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
               	$byday = explode(',', (isset($rrules['byday'])) ? $rrules['byday'] : ''); 
               	$bymonth = explode(',', (isset($rrules['bymonth'])) ? $rrules['bymonth'] : '');
               	$bymonthday = explode(',', (isset($rrules['bymonthday'])) ? $rrules['bymonthday'] : '');
               	
               	$i = 1;
               	$cen = 0;
               	switch ($frequency){
               		case "YEARLY"	:
               		case "MONTHLY"	:
               		case "WEEKLY"	:
               		case "DAILY"	:
               			$freqinterval = new DateInterval('P' . $interval . substr($frequency,0,1));
               			$interval3day = new DateInterval('P3D');
               			$fmdayok = true;
               			$freqstart = clone $edtstart;
               			$newstart = clone $edtstart;
               			$newend = clone $edtstart;
               			$tzoffsetprev = $timezone->getOffset ( $freqstart);
              			while ( $freqstart->getTimestamp() <= $penddate
               					&& $freqstart->getTimestamp() < $until
               					&& ($count == 0 || $i < $count  )            						)
           				{   // first FREQ loop on dtstart will only output new events
               				// created by a BY... clause
               				$test = '';
               				$fd = $freqstart->format('d');
               				$fm = $freqstart->format('m');
               				$fY = $freqstart->format('Y');
               				$fdays = $freqstart->format('t');
           				
               				foreach ($bymonth as $by) {
               					$newstart->setTimestamp($freqstart->getTimestamp()) ;
               					if (isset($rrules['bymonth'])){
               						if ($by < 0){
               							$by = 13 + $by;
               						}
               						
               						if ($frequency ='YEARLY' ){ // expand
               							
               							$test = 'Y mday:' .$by . 'fdays:' . $fdays ; //. 'ns:' . $newstart->format('Y-m-d G:i');
               							if (!$newstart->setDate($fY , $by, $fd))
               							{ continue;}
               						} else
               						{ // limit
               							$test = 'MWD mday:' .$by . 'fdays:' . $fdays ; //. 'ns:' . $newstart->format('Y-m-d G:i');
               							if ((!$fmdayok) ||
               									(intval($newstart->format('n')) != intval($by)))
               							{continue;}
               						}
               					} else { // passthrough
               						$test = 'Geen bymonth';
               					}
               					
               					foreach ($bymonthday as $by) {
           						if (isset($rrules['bymonthday'])){
           							if ($by < 0){
           								$by = $newstart->format('t')+ 1 + $by;
           							}
           							
           							if (in_array($frequency , array('MONTHLY', 'YEARLY')) ){ // expand
           								
          						//		$test = 'MY mday:' .$by . 'fdays:' . $fdays ; //. 'ns:' . $newstart->format('Y-m-d G:i');
           								if (!$newstart->setDate($fY , $fm , $by))
           							   	{ continue;}
           							} else 
           							{ // limit
           							//	$test = 'WD mday:' .$by . 'fdays:' . $fdays ; //. 'ns:' . $newstart->format('Y-m-d G:i');
           								if ((!$fmdayok) ||
           										(intval($newstart->format('j')) !== intval($by)))
           									{continue;}
           							}
           						} else { // passthrough
           							// $test = 'Geen bymonthday';
           						}
           							
           						foreach ($byday as $by) {
           							$newstart->setTimestamp($freqstart->getTimestamp()) ;
           							if (isset($rrules['byday'])){
           								$byd = substr($by,-2);
           								$byi = intval($by);
           								
           								if (in_array($frequency , array('WEEKLY','MONTHLY', 'YEARLY'))
           										&& (! isset($rrules['bymonthday']))	
           										&& (! isset($rrules['byyearday']))	
           										) { //expand
           									$test =  'WMY $byd:' .$byd . ' $byi:' . $byi; 
//           									if (!$newstart->setDate($fY , $fm , $by))
//           									{ continue;}
           								} else
           								{ //limit
           									$test =  'D $byd:' .$byd . ' $byi:' . $byi; //. 'ns:' . $newstart->format('Y-m-d G:i');
           									if ((!$fmdayok)
 //          											|| (intval($fd) !== intval($by))
           											)
           									{continue;}
           								}
           							} else { // passthrough
           								 $test =  'Geen byday';
           								 if (!$fmdayok
           								 		&& intval($edtstartmday) == intval($newstart->format('j'))
           								 		&& intval($edtstartmon) == intval($newstart->format('n'))
           								 		)
           								 {continue;}
           							}

           							if (  
           							 $newstart->getTimestamp() <= $penddate
           							&& $newstart> $edtstart) { // count events after dtstart
           							if ($newstart->getTimestamp() >= $now
           									) { // copy only events after now
           								$cen++;
           								$en =  clone $e;
           								$en->start = $newstart->getTimestamp();
           								$newend->setTimestamp($en->start) ;
           								$newend->add($eduration);
           								$en->end = $newend->getTimestamp();
           								$en->uid = $i . '_' . $e->uid;
           								$en->summary = 'nr:' . $i . ' cen:' . $cen . ' '. $e->summary;
 										if ($test > ' ') {
           									$en->summary = $en->summary . '<br>Test:' . $test;
 										}
           								$events[] = $en;
           							} // copy eevents
           							// next eventcount from $e->start	
           							$i++;
           						} // end count events
           					} // end byday	
           					} // end bymonthday
           					} // end bymonth
           					// next startdate by FREQ for loop < $until and <= $penddate
           					$freqstart->add($freqinterval);
           					if  ($fmdayok &&
           							in_array($frequency , array('MONTHLY', 'YEARLY')) &&
           							$freqstart->format('j') !== $edtstartmday){ 
           						// eg 31 jan + 1 month = 3 mar; -3 days => 28 feb 
           						$freqstart->sub($interval3day);
           						$fmdayok = false;
           					} elseif (!$fmdayok ){
           						$freqstart->add($interval3day);
           						$fmdayok = true;
           						
           					}
           					// process daylight saving time
           					$tzadd = $tzoffsetprev - $timezone->getOffset ( $freqstart);
           					$tzoffsetprev = $timezone->getOffset ( $freqstart);
           					if ($tzadd != 0) {
           						$tziv = new DateInterval('PT' . abs($tzadd) . 'S');
           						if ($tzadd < 0) {
           							$tziv->invert = 1;
           						}
           					$freqstart->add($tziv);
           					}
           				} 
                 	}
 				} // switch freq
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
 // TODO: use DateTime object with timezone, not date_default_timezone_set if possible.       
 //       $timezone = new DateTimeZone((isset($e->tzid)&& $e->tzid !== '') ? $e->tzid : get_option('timezone_string'));
 // see public static DateTime DateTime::createFromFormat 
 //    ( string $format , string $time [, DateTimeZone $timezone ] )
 //       $format = 'Y-m-d H:i:s';
 //       $date = DateTime::createFromFormat($format, '2009-02-15 15:16:17');
 //       echo "Format: $format; " . $date->format('Y-m-d H:i:s') . "\n";
 //  Output:     Format: Y-m-d H:i:s; 2009-02-15 15:16:17
 //
 // so the correct way is to do:
 //       $d = DateTime::createFromFormat("Y-m-d H:i:s T","2011-11-06 01:00:00 EST",new DateTimeZone($timezone)); // set $timezone to any valid string for DateTimeZone, it doesn't matter
 //       echo $d->format("Y-m-d H:i:s T U");
 // returns "2011-11-06 01:00:00 EST - 1320559200" as wanted
 //       e, O, P and T:	Timezone identifier,
 //                         or difference to UTC in hours, 
 //                         or difference to UTC with colon between hours and minutes, 
 //                         or timezone abbreviation	
 // Examples: UTC, GMT, Atlantic/Azores or +0200 or +02:00 or EST, MDT
        
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
