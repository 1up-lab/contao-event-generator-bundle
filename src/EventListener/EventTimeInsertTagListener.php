<?php

declare(strict_types=1);

namespace Oneup\Contao\EventGeneratorBundle\EventListener;

use Contao\CalendarEventsModel;
use Contao\Date;

class EventTimeInsertTagListener
{
    public const TAG = 'event_time';

    /**
     * @return string|false
     */
    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if (self::TAG !== $chunks[0]) {
            return false;
        }

        $event = CalendarEventsModel::findByPk($chunks[1]);

        if (!$event instanceof CalendarEventsModel) {
            return 'n/a';
        }

        return sprintf('%s-%s', (new Date($event->startTime))->time, (new Date($event->endTime))->time);
    }
}
