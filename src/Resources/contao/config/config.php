<?php

declare(strict_types=1);

$GLOBALS['BE_MOD']['content']['calendar']['tables'][] = 'tl_calendar_event_generator';

$GLOBALS['TL_MODELS']['tl_calendar_event_generator'] = Oneup\Contao\EventGeneratorBundle\Model\CalendarEventGeneratorModel::class;
