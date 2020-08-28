<?php

declare(strict_types=1);

namespace Oneup\Contao\EventGeneratorBundle\Dca\Alias;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\Slug\Slug;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class EventAliasGenerator
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Slug
     */
    protected $slugGenerator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(Connection $connection, Slug $slugGenerator, TranslatorInterface $translator)
    {
        $this->connection = $connection;
        $this->slugGenerator = $slugGenerator;
        $this->translator = $translator;
    }

    public function generate(CalendarEventsModel $eventsModel): string
    {
        $value = $eventsModel->alias;

        $aliasExists = function (string $alias) use ($eventsModel) {
            $statement = $this->connection->prepare('SELECT id FROM tl_calendar_events WHERE alias = ? AND id <> ?');
            $statement->execute([$alias, $eventsModel->id]);

            return \count($statement->fetchAll()) > 0;
        };

        if ('' === $value) {
            /** @var \DateTime $date */
            $date = \DateTime::createFromFormat('U', $eventsModel->startDate);

            $value = $this->slugGenerator->generate(sprintf(
                '%s-%s-%s',
                $eventsModel->title,
                $date->format('d-m-Y'),
                $eventsModel->id
            ), $eventsModel->pid, $aliasExists);
        }

        if ($aliasExists($value)) {
            throw new \DomainException(sprintf($this->translator->trans('ERR.aliasExists'), $value));
        }

        return $value;
    }
}
