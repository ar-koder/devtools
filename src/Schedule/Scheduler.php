<?php

namespace App\Schedule;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

class Scheduler implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        $schedule
            ->timezone('UTC')
            ->environments('dev')
        ;

        $schedule->addCommand('doctrine:fixtures:load --no-interaction')
            //->cron('0 */2 * * *')
            ->cron('* * * * *')
        ;
    }
}
