<?php

declare(strict_types=1);

namespace Oneup\Contao\EventGeneratorBundle\EventListener;

use Contao\Module;
use Kmielke\CalendarExtendedBundle\CalendarLeadsModel;

class CalendarEventListener
{
    public function onGetAllEvents(array $events, array $calendars, int $start, int $end, Module $module): array
    {
        foreach ($events as $day => $eventsPerDay) {
            /** @var \DateTime $dayDateTime */
            $dayDateTime = \DateTime::createFromFormat('Ymd', (string) $day);
            $dayDateTime->setTime(0, 0);

            foreach ($eventsPerDay as $time => $eventsPerTime) {
                foreach ($eventsPerTime as $key => $event) {
                    if (!(bool) $event['useRegistration']) {
                        continue;
                    }

                    /** @var int $registrations */
                    $registrations = CalendarLeadsModel::regCountByFormEvent($event['regform'], $event['id']);

                    if ($registrations > 0) {
                        $event['class'] = sprintf('%s %s', $event['class'], 'reserved');
                    }

                    $events[$day][$time][$key] = $event;
                }
            }
        }

        return $events;
    }
}
