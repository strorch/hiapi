<?php
declare(strict_types=1);

namespace hiapi\event;

/**
 * Trait EventTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait EventAwareTrait
{
    /**
     * @var EventInterface[]
     */
    private $events = [];

    /**
     * Registers that $event occurred
     * @param EventInterface $event
     */
    public function recordThat(EventInterface $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @return EventInterface[] of occurred events
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
