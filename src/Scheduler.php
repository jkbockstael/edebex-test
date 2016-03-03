<?php
namespace EdebexTest;

class Scheduler
{
    private $workweek;
    private $delay;
    private $holidays;

    public function __construct($workweek, $delay, $holidays)
    {
        $this->workweek = $workweek;
        $this->delay = $delay;
        $this->holidays = $holidays;
    }

    // Return the mail sending time for a given approval time
    public function getMailTime($approval_time)
    {
        $block = $this->getBlock($approval_time);
        if ($block === null) {
            $block = $this->getNextBlock($approval_time);
        }
        $delay_start = $approval_time;
        $delay_remaining = $this->delay * 60; // minutes
        while ($delay_remaining > 0) {
            while ($block['mail'] === false) {
                $block = $this->getNextBlock($block['stop']);
                $delay_start = $block['start'];
            }
            if ($block['stop'] < $delay_start->add(new \DateInterval('PT' . $delay_remaining . 'M'))) {
                $consumed_delay = $block['stop']->diff($delay_start);
                $delay_remaining -= $consumed_delay->h * 60 + $consumed_delay->i;
                $block = $this->getNextBlock($block['stop']);
                $delay_start = $block['start'];
            }
            else {
                $send_time = $delay_start->add(new \DateInterval('PT' . $delay_remaining . 'M'));
                return $send_time;
            }
        }
    }

    // Return the block a given time is in, or null if this time isn't in a block
    private function getBlock($approval_time)
    {
        $day_of_week = (int)$approval_time->format('N') - 1;
        if (!isset($this->workweek[$day_of_week]) or empty($this->workweek[$day_of_week])) {
            return null;
        }
        if (in_array($approval_time->format('Y-m-d'), $this->holidays)) {
            return null;
        }
        $blocks = $this->workweek[$day_of_week];
        foreach ($blocks as $block) {
            $block_start = new \DateTimeImmutable($approval_time->format('Y-m-d') . ' ' . $block['start']);
            $block_stop = new \DateTimeImmutable($approval_time->format('Y-m-d') . ' ' . $block['stop']);
            if ($block_start <= $approval_time and $block_stop > $approval_time) {
                return [
                    'start' => $block_start,
                    'stop' => $block_stop,
                    'mail' => $block['mail']
                ];
            }
        }
        return null;
    }

    // Return the earliest block following a given time
    private function getNextBlock($from_time)
    {
        $next_block = null;
        while ($next_block === null) {
            $day_of_week = (int)$from_time->format('N') - 1;
            while (!isset($this->workweek[$day_of_week]) or empty($this->workweek[$day_of_week])) {
                $from_time = $from_time->add(new \DateInterval('P1D'))->setTime(0, 0);
                $day_of_week = (int)$from_time->format('N') - 1;
            }
            while (in_array($from_time->format('Y-m-d'), $this->holidays)) {
                $from_time = $from_time->add(new \DateInterval('P1D'))->setTime(0, 0);
                $day_of_week = (int)$from_time->format('N') - 1;
            }
            $blocks = $this->workweek[$day_of_week];
            foreach ($blocks as $block) {
                $block_start = new \DateTimeImmutable($from_time->format('Y-m-d') . ' ' . $block['start']);
                $block_stop = new \DateTimeImmutable($from_time->format('Y-m-d') . ' ' . $block['stop']);
                if ($block_start > $from_time) {
                    $next_block = [
                        'start' => $block_start,
                        'stop' => $block_stop,
                        'mail' => $block['mail']
                    ];
                    break;
                }
            }
            $from_time = $from_time->add(new \DateInterval('P1D'))->setTime(0, 0);
        }
        return $next_block;
    }
}
