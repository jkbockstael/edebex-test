<?php
namespace EdebexTest;

require 'vendor/autoload.php';

if ($argc < 3) {
    echo 'Usage: ' . $argv[0] . ' approval_times_file holidays_file' . PHP_EOL;
    exit(1);
}

$workweek = [
    [['start' => '09:00', 'stop' => '12:00', 'mail' => false], ['start' => '13:30', 'stop' => '17:00', 'mail' => true]], // mon
    [['start' => '09:00', 'stop' => '12:00', 'mail' => true], ['start' => '13:30', 'stop' => '17:00', 'mail' => true]], // tue
    [['start' => '09:00', 'stop' => '12:00', 'mail' => true], ['start' => '13:30', 'stop' => '17:00', 'mail' => true]], // wed
    [['start' => '09:00', 'stop' => '12:00', 'mail' => true], ['start' => '13:30', 'stop' => '17:00', 'mail' => true]], // thu
    [['start' => '09:00', 'stop' => '12:00', 'mail' => true], ['start' => '13:30', 'stop' => '17:00', 'mail' => false]] // fri
];
$delay = 4; // hours

$holidays = \EdebexTest\HolidaysParser::fromFile($argv[2]);
$scheduler = new \EdebexTest\Scheduler($workweek, $delay, $holidays);
foreach (file($argv[1]) as $line) {
    $approval_time = new \DateTimeImmutable(trim($line));
    $mailing_time = $scheduler->getMailTime($approval_time);
    echo 'approval time: ' . $approval_time->format('Y-m-d H:i') . ', mailing time: ' . $mailing_time->format('Y-m-d H:i') . PHP_EOL;
}
