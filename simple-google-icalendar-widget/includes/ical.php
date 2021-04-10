<?php

class IcsParsingException extends Exception {}

/**
 * a simple ICS parser.
 *
 * note that this class does not implement all ICS functionality.
 *   bw 20171109 enkele verbeteringen voor start en end in ical.php
 *   bw 20190526 v1.0.2 some adjustments for longer Description or Summary or LOCATION
 *   bw 20190529 v1.0.3 trim only "\n\r\0" and first space but keep last space in Description Summary and Location lines.
 *               adjustments to correct timezone that is ignored in new datetime when the $time parameter is a UNIX timestamp (e.g. @946684800)
 *   bw 20190603 v1.1.0 parse exdate's to exclude events from repeat
 *   bw 20201122 v1.2.0 find solution for DTSTART and DTEND without time by explicit using isDate and only displaying times when isDate === false.;
 *               found a problem with UID in first line when line-ends are \n in stead of \r\n solved by better calculation of start of EventStr.
 *   bw 20201123 handle not available DTEND => !isset($e->end) in response to a comment of lillyberger (@lillyberger) on the plugin page.
 * Version: 1.3.0
 
 */
class IcsParser {
    
    const TOKEN_BEGIN_VEVENT = "\nBEGIN:VEVENT";
    const TOKEN_END_VEVENT = "\nEND:VEVENT";
    
    public function parse($str ,  $penddate,  $pcount) {
        
        $curstr = $str;
        $haveVevent = true;
        $events = array();
        $now = time();
        $penddate = (isset($penddate) && $penddate > $now) ? $penddate : $now;
        $weekdays = array (
            'MO' => 'monday',
            'TU' => 'tuesday',
            'WE' => 'wednesday',
            'TH' => 'thursday',
            'FR' => 'friday',
            'SA' => 'saturday',
            'SU' => 'sunday',
        );
        
        do {
            $startpos = strpos($curstr, self::TOKEN_BEGIN_VEVENT);
            if ($startpos !== false) {
                // remove BEGIN_VEVENT and END:VEVENT
                // +1: because \r\n or \n at the end. if \r\n remove remaining \n
                $eventStrStart = $startpos + strlen(self::TOKEN_BEGIN_VEVENT) + 1;
                $eventStr = substr($curstr, $eventStrStart);
                if (substr($eventStr,0,1) == "\n") {$eventStr = substr($eventStr,1); }
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
                    $edtstart = new DateTime('@' . $e->start);
                    $edtstart->setTimezone($timezone);
                    $edtstartmday = $edtstart->format('j');
                    $edtstartmon = $edtstart->format('n');
                    $egdstart = getdate($e->start);
                    //      example 2017-11-16
                    // 		$egdstart['weekday'] 'Monday' - 'Sunday' example 'Thursday'
                    //		$egdstart['mon']  monthnr in year 1 - 12 example 11  (november)
                    //		$egdstart['mday'] day in the month 1 - 31 example 16
                    $edtendd   = new DateTime('@' . $e->end);
                    $edtendd->setTimezone($timezone);
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
                    $freqinterval = new DateInterval('P' . $interval . substr($frequency,0,1));
                    $interval3day = new DateInterval('P3D');
                    $until = (isset($rrules['until'])) ? $this->parseIcsDateTime($rrules['until']) : $penddate;
                    $until = ($until < $penddate) ? $until : ($penddate - 1);
                    $freqendloop = ($until > $penddate) ? $until : $penddate;
                    switch ($frequency){
                        case "YEARLY"	:
                            $freqendloop = $freqendloop + (31622400 * $interval); // 366 days in sec
                            break;
                        case "MONTHLY"	:
                            $freqendloop = $freqendloop + (2678400 * $interval); // 31 days in sec
                            break;
                            
                        case "WEEKLY"	:
                            $freqendloop = $freqendloop + (604800 * $interval); // 7 days in sec
                            break;
                            
                        case "DAILY"	:
                            $freqendloop = $freqendloop + (86400 * $interval); // 1 days in sec
                            break;
                            
                    }
                    $count = (isset($rrules['count'])) ? $rrules['count'] : 0;
                    $bymonth = explode(',', (isset($rrules['bymonth'])) ? $rrules['bymonth'] : '');
                    $bymonthday = explode(',', (isset($rrules['bymonthday'])) ? $rrules['bymonthday'] : '');
                    $byday = explode(',', (isset($rrules['byday'])) ? $rrules['byday'] : '');
                    $i = 1;
                    $cen = 0;
                    switch ($frequency){
                        case "YEARLY"	:
                        case "MONTHLY"	:
                        case "WEEKLY"	:
                        case "DAILY"	:
                            $fmdayok = true;
                            $freqstart = clone $edtstart;
                            $newstart = clone $edtstart;
                            $newend = clone $edtstart;
                            $tzoffsetedt = $timezone->getOffset ( $edtstart);
                            while ( $freqstart->getTimestamp() <= $freqendloop
                                && ($count == 0 || $i < $count  )            						)
                            {   // first FREQ loop on dtstart will only output new events
                                // created by a BY... clause
                                $test = '';
                                //							$test = print_r($e->exdate, true);
                                $fd = $freqstart->format('d'); // Day of the month, 2 digits with leading zeros
                                $fn = $freqstart->format('n'); // Month, without leading zeros
                                $fY = $freqstart->format('Y'); // Year, 4 digits
                                $fH = $freqstart->format('H'); // 24-hour format of an hour with leading zeros
                                $fi = $freqstart->format('i'); // Minutes with leading zeros
                                $fdays = $freqstart->format('t'); // Number of days in the given month
                                $expand = false;
                                // bymonth
                                if (isset($rrules['bymonth'])) {
                                    $bym = array();
                                    foreach ($bymonth as $by){
                                        // convert bymonth ordinals to month-numbers
                                        if ($by < 0){
                                            $by = 13 + $by;
                                        }
                                        $bym[] = $by;
                                    }
                                    $bym= array_unique($bym); // make unique
                                    sort($bym);	// order array so that oldest items first are counted
                                } else {$bym= array('');}
                                foreach ($bym as $by) {
                                    $newstart->setTimestamp($freqstart->getTimestamp()) ;
                                    if (isset($rrules['bymonth'])){
                                        
                                        if ($frequency == 'YEARLY' ){ // expand
                                            $newstart->setDate($fY , $by, 1);
                                            $ndays = intval($newstart->format('t'));
                                            $expand = true;
                                            if (intval($fd) <= $ndays) {
                                                $newstart->setDate($fY , $by, $fd);
                                            } elseif (isset($rrules['bymonthday'])
                                                || isset($rrules['byday'])){
                                                    // no action day-of the-month is set later
                                            }  else {
                                                continue;
                                            }
                                        } else
                                        { // limit
                                            if ((!$fmdayok) ||
                                                (intval($newstart->format('n')) != intval($by)))
                                            {continue;}
                                        }
                                    } else { // passthrough
                                    }
                                    // bymonthday
                                    if (isset($rrules['bymonthday'])) {
                                        $byn = array();
                                        $ndays = intval($newstart->format('t'));
                                        foreach ($bymonthday as $by){
                                            // convert bymonthday ordinals to day-of-month-numbers
                                            if ($by < 0){
                                                $by = 1 + $ndays + intval($by);
                                            }
                                            if ($by > 0 && $by <= $ndays) {
                                                $byn[] = $by;
                                            }
                                        }
                                        $byn= array_unique($byn); // make unique
                                        sort($byn);	// order array so that oldest items first are counted
                                    } else {$byn = array('');}
                                    
                                    foreach ($byn as $by) {
                                        if (isset($rrules['bymonthday'])){
                                            if (in_array($frequency , array('MONTHLY', 'YEARLY')) ){ // expand
                                                $expand = true;
                                                $newstart->setDate($newstart->format('Y'), $newstart->format('m'), $by);
                                            } else
                                            { // limit
                                                if ((!$fmdayok) ||
                                                    (intval($newstart->format('j')) !== intval($by)))
                                                {continue;}
                                            }
                                        } else { // passthrough
                                        }
                                        // byday
                                        $bydays = array();
                                        if (isset($rrules['byday'])){
                                            if (in_array($frequency , array('WEEKLY','MONTHLY', 'YEARLY'))
                                                && (! isset($rrules['bymonthday']))
                                                && (! isset($rrules['byyearday']))) { // expand
                                                    $expand =true;
                                                    foreach ($byday as $by) {
                                                        // expand byday codes to bydays datetimes
                                                        $byd = $weekdays[substr($by,-2)];
                                                        if (!($byd > 'a')) continue; // if $by contains only number (not good ical)
                                                        $byi = intval($by);
                                                        $wdf = clone $newstart;
                                                        if ($frequency == 'MONTHLY'	|| $frequency == 'YEARLY' ){
                                                            $wdl = clone $newstart;
                                                            if ($frequency == 'YEARLY' && (!isset($rrules['bymonth']))){
                                                                $wdf->setDate($fY , 1, $fd);
                                                                $wdl->setDate($fY , 12, $fd);
                                                            }
                                                            $wdf->modify('first ' . $byd . ' of');
                                                            $wdl->modify('last ' . $byd . ' of');
                                                            $wdf->setTime($fH, $fi);
                                                            $wdl->setTime($fH, $fi);
                                                            if ($byi > 0) {
                                                                $wdf->add(new DateInterval('P' . ($byi - 1) . 'W'));
                                                                $bydays[] = $wdf->getTimestamp();
                                                            } elseif ($byi < 0) {
                                                                $wdl->sub(new DateInterval('P' . (- $byi - 1) . 'W'));
                                                                $bydays[] = $wdl->getTimestamp();
                                                                
                                                            }
                                                            else {
                                                                while ($wdf <= $wdl) {
                                                                    $bydays[] = $wdf->getTimestamp();
                                                                    $wdf->add(new DateInterval('P1W'));
                                                                }
                                                            }
                                                        } // Yearly or Monthly
                                                        else  { // $frequency == 'WEEKLY' byi is not allowed so we dont parse it
                                                            $wdnrn = $newstart->format('N'); // Mo 1; Su 7
                                                            $wdnrb = array_search($byd,array_values($weekdays)) + 1;  // numeric index in weekdays
                                                            if ($wdnrb > $wdnrn) {
                                                                $wdf->add (new DateInterval('P' . ($wdnrb - $wdnrn ) . 'D'));
                                                            }
                                                            if ($wdnrb < $wdnrn) {
                                                                $wdf->sub (new DateInterval('P' . ($wdnrn - $wdnrb) . 'D'));
                                                                
                                                            }
                                                            $bydays[] = $wdf->getTimestamp();
                                                            
                                                        } // Weekly
                                                        
                                                    } // foreach
                                            } // expand
                                            else { // limit frequency period smaller than Week//
                                                // intval (byi) is not allowed with a frquency other than YEARLY or MONTHLY so
                                                // RRULE:FREQ=DAILY;BYDAY=-1SU; won't give any reptition.
                                                if ($byday == array('')
                                                    || in_array(strtoupper(substr($newstart->format('D'),0,2 )), $byday)
                                                    ){ // only one time in this loop no change of $newstart
                                                        $bydays =  array('');
                                                } else {
                                                    continue;
                                                }
                                            } // limit
                                        } // isset byday
                                        else {$bydays = array('');
                                        }
                                        $bydays= array_unique($bydays); // make unique
                                        sort($bydays);	// order array so that oldest items first are counted
                                        foreach ($bydays as $by) {
                                            if (intval($by) > 0 ) {
                                                $newstart->setTimestamp($by) ;
                                            }
                                            if (
                                                ($fmdayok  || $expand
                                                    || $newstart->format('Ymd') != $edtstart->format('Ymd'))
                                                && ($count == 0 || $i < $count)
                                                && $newstart->getTimestamp() < $until
                                                && !(!empty($e->exdate) && in_array($newstart->getTimestamp(), $e->exdate))
                                                && $newstart> $edtstart) { // count events after dtstart
                                                    if ($newstart->getTimestamp() >= $now
                                                        ) { // copy only events after now
                                                            $cen++;
                                                            // process daylight saving time
                                                            $tzadd = $tzoffsetedt - $timezone->getOffset ( $newstart);
                                                            if ($tzadd != 0) {
                                                                $tziv = new DateInterval('PT' . abs($tzadd) . 'S');
                                                                if ($tzadd < 0) {
                                                                    $tziv->invert = 1;
                                                                }
                                                                $newstart->add($tziv);
                                                            }
                                                            
                                                            $en =  clone $e;
                                                            $en->start = $newstart->getTimestamp();
                                                            $newend->setTimestamp($en->start) ;
                                                            $newend->add($eduration);
                                                            $en->end = $newend->getTimestamp();
                                                            $en->uid = $i . '_' . $e->uid;
                                                            if ($test > ' ') { 	$en->summary = $en->summary . '<br>Test:' . $test; 	}
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
            if ((($e->start >= $now) || (!empty($e->end) && $e->end >= $now))
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
        
        if (strlen($datetime) >= 13)  {
            $hms = substr($datetime, 9, 4) . '00';
        } else {
            $hms = '000000';
        }
        
        // check if it is GMT
        $lastChar = $datetime[strlen($datetime) - 1];
        if ($lastChar == 'Z') {
            $tzid = 'UTC';
        } else  {
            $tzid = ($tzid > ' ') ? $tzid : get_option('timezone_string');
        }
        $date = date_create_from_format('Ymd His e', substr($datetime,0,8) . ' ' . $hms. ' ' . $tzid);
        $time = $date->getTimestamp();
        
        
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
        $tokenprev = "";
        
        foreach($lines as $l) {
            
            $list = explode(":", $l);
            $token = "";
            $value = "";
            $tzid = '';
            $isdate = false;
            //bw 20171108 added, because sometimes there is timezone or other info after DTSTART, or DTEND
            //     eg. DTSTART;TZID=Europe/Amsterdam, or  DTSTART;VALUE=DATE:20171203
            $tl = explode(";", $list[0]);
            $token = $tl[0];
            if (count($tl) > 1 ){
                $dtl = explode("=", $tl[1]);
                if (count($dtl) > 1 ){
                    switch($dtl[0]) {
                    case 'TZID':
                        $tzid = $dtl[1];
                        break;
                    case 'VALUE':
                        $isdate = (substr( $dtl[1],0,4) == 'DATE');
                        break;
                    }
                }
            }
            if (count($list) > 1) {
                // trim() to remove \n\r\0
                $value = trim($list[1]);
                $desc = str_replace(array('\;', '\,', '\r\n', '\n', '\r'), array(';', ',', '<br>', '<br>', '<br>'), htmlspecialchars(trim($list[1], "\n\r\0")));
                $tokenprev = $token;
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
                        $eventObj->startisdate = $isdate;
                        $eventObj->start = $this->parseIcsDateTime($value, $tzid);
                        if ($tzid > ' ') {
                            $eventObj->tzid = $tzid;
                        }
                        if (!isset($eventObj->end)) { // because I am not sure the order is alway DTSTART before DTEND
                            $eventObj->endisdate = $isdate;
                            $eventObj->end = $eventObj->start;
                        }                        
                        break;
                    case "DTEND":
                        $eventObj->endisdate = $isdate;
                        $eventObj->end = $this->parseIcsDateTime($value, $tzid);
                        break;
                    case "UID":
                        $eventObj->uid = $value;
                        break;
                    case "RRULE":
                        $eventObj->rrule = $value;
                        break;
                    case "EXDATE":
                        $dtl = explode(",", $value);
                        foreach ($dtl as $value) {
                            $eventObj->exdate[] = $this->parseIcsDateTime($value, $tzid);
                        }
                        break;
                }
            }else { // count($list) <= 1
                if (strlen($token) > 1) {
                    $desc = str_replace(array('\;', '\,', '\r\n', '\n', '\r'), array(';', ',', '<br>', '<br>', '<br>'), htmlspecialchars(trim(substr($token,1), "\n\r\0")));
                    switch($tokenprev) {
                        case "SUMMARY":
                            $eventObj->summary .= $desc;
                            break;
                        case "DESCRIPTION":
                            $eventObj->description .= $desc;
                            break;
                        case "LOCATION":
                            $eventObj->location .= $desc;
                            break;
                    }
                }
            }
            
            
        }
        
        return $eventObj;
    }
}

$p = new IcsParser();
