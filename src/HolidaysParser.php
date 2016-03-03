<?php
namespace EdebexTest;

class HolidaysParser
{
    // Get dates from a ICQ calendar file
    public static function fromFile($ics_file)
    {
        $vcalendar = \Sabre\VObject\Reader::read(fopen($ics_file, 'r'));

        $holidays = [];
        foreach ($vcalendar->VEVENT as $vevent) {
            $holidays[] = $vevent->DTSTART->getDateTime()->format('Y-m-d');
        }

        return $holidays;
    }
}
