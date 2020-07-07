<?php

declare(strict_types=1);

namespace Oneup\Contao\EventGeneratorBundle\EventListener;

use Contao\Backend;
use Contao\CalendarEventsModel;
use Contao\DataContainer;
use Contao\FormModel;
use Contao\Input;
use Contao\Model\Collection;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Oneup\Contao\EventGeneratorBundle\Model\CalendarEventGeneratorModel;

class CalendarEventGeneratorListener
{
    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function buttonCallback(array $record, string $href, string $label, string $title, string $icon, string $attributes, string $table, ?array $rootIds, ?array $childIds, bool $circularReference, ?string $previousLabel, ?string $nextLabel, DataContainer $dataContainer): string
    {
        return sprintf(
            '<a href="%s" title="%s" %s><img src="%s" width="16" height="16" /></a>',
            Backend::addToUrl(sprintf('%s&amp;cpid=%s', $href, $record['id'])),
            sprintf($label, $record['id']),
            $attributes,
            $icon
        );
    }

    public function onLoad(DataContainer $dataContainer): void
    {
        /** @var CalendarEventGeneratorModel|null $record */
        $record = CalendarEventGeneratorModel::findByPk($dataContainer->id);

        if (null === $record) {
            return;
        }

        $record->pid = Input::get('cpid');
        $record->save();
    }

    public function onSubmit(DataContainer $dataContainer): void
    {
        /** @var CalendarEventGeneratorModel $data */
        $data = $dataContainer->activeRecord;

        $gid = $data->id;
        $pid = $data->pid;
        $title = $data->title;
        $author = $data->author;
        $addRegistration = $data->addRegistration;
        $registrationForm = $data->registrationForm;
        $maxParticipants = $data->maxParticipants;
        $registrationDeadline = $data->registrationDeadline;

        $this->connection
            ->prepare('DELETE FROM tl_calendar_events WHERE gid = ?')
            ->execute([$gid])
        ;

        $timeZone = new \DateTimeZone(date_default_timezone_get());

        $from = \DateTime::createFromFormat('U', (string) $data->from);

        if (!$from instanceof \DateTime) {
            throw new \RuntimeException('Could not create a DateTime object.');
        }

        $from->setTimezone($timeZone);

        $to = \DateTime::createFromFormat('U', (string) $data->to);

        if (!$to instanceof \DateTime) {
            throw new \RuntimeException('Could not create a DateTime object.');
        }

        $to->setTimezone($timeZone);

        /** @var array $slots */
        $slots = StringUtil::deserialize($data->slots);

        /** @var array $weekdays */
        $weekdays = StringUtil::deserialize($data->weekdays);
        $weekdays = array_map('intval', $weekdays);

        $to->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $range = new \DatePeriod($from, $interval, $to);

        foreach ($range as $date) {
            /** @var \DateTime $date */
            if (!\in_array((int) $date->format('N'), $weekdays, true)) {
                continue;
            }

            foreach ($slots as $slot) {
                $start = explode(':', $slot['startTime']);
                $end = explode(':', $slot['endTime']);

                $startTime = clone $date;
                $startTime->setTime((int) $start[0], (int) $start[1]);

                $endTime = clone $date;
                $endTime->setTime((int) $end[0], (int) $end[1]);

                $event = new CalendarEventsModel();
                $event->pid = $pid;
                $event->title = $title;
                $event->author = $author;
                $event->startDate = $date->format('U');
                $event->endDate = $date->format('U');
                $event->startTime = $startTime->format('U');
                $event->endTime = $endTime->format('U');
                $event->addTime = '1';
                $event->published = '1';
                $event->stop = $startTime->format('U') - $registrationDeadline;
                $event->tstamp = time();
                $event->useRegistration = $addRegistration;
                $event->regform = $registrationForm;
                $event->regperson = serialize([['mini' => '0', 'maxi' => $maxParticipants]]);
                $event->regstartdate = $startTime->format('U') - $registrationDeadline;
                $event->gid = $gid;

                $event->save();
            }
        }
    }

    public function getButtons(array $buttons, DataContainer $dataContainer): array
    {
        return [
            'save' => $buttons['save'],
            'saveNclose' => $buttons['saveNclose'],
        ];
    }

    public function getWeekdays(DataContainer $dataContainer): array
    {
        return [0, 1, 2, 3, 4, 5, 6];
    }

    public function getRegistrationForms(DataContainer $dataContainer): array
    {
        /** @var Collection $objForms */
        $objForms = FormModel::findAll();

        $return = [];

        if (null !== $objForms) {
            while ($objForms->next()) {
                $return[$objForms->id] = $objForms->title;
            }
        }

        return $return;
    }

    public function getSlots(\MultiColumnWizard $multiColumnWizard): array
    {
        return [
            'startTime' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_generator']['startTime'],
                'default' => time(),
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'rgxp' => 'time',
                    'tl_class' => 'w50',
                ],
            ],
            'endTime' => [
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_generator']['endTime'],
                'default' => time(),
                'exclude' => true,
                'inputType' => 'text',
                'eval' => [
                    'rgxp' => 'time',
                    'tl_class' => 'w50',
                ],
            ],
        ];
    }
}
