<?php

declare(strict_types=1);

namespace Recipe\Domain\Model\Shared;

use function count;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\ClassNameInflector;
use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDecorator;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\UnableToReconstituteAggregateRoot;
use Generator;
use Throwable;

/**
 * @template T of AggregateRoot
 *
 * @template-implements AggregateRootRepository<T>
 */
class EventSourcedAggregateRootRepository implements AggregateRootRepository
{
    /** @var class-string<T> */
    private string $aggregateRootClassName;
    private MessageRepository $messages;
    private MessageDecorator $decorator;
    private ClassNameInflector $classNameInflector;

    /**
     * @param class-string<T> $aggregateRootClassName
     */
    public function __construct(
        string $aggregateRootClassName,
        MessageRepository $messageRepository,
        MessageDecorator $decorator,
        ClassNameInflector $classNameInflector
    ) {
        $this->aggregateRootClassName = $aggregateRootClassName;
        $this->messages = $messageRepository;
        $this->decorator = $decorator;
        $this->classNameInflector = $classNameInflector;
    }

    public function retrieve(AggregateRootId $aggregateRootId): object
    {
        try {
            /** @var AggregateRoot $className */
            $className = $this->aggregateRootClassName;
            $events = $this->retrieveAllEvents($aggregateRootId);

            return $className::reconstituteFromEvents($aggregateRootId, $events);
        } catch (Throwable $throwable) {
            throw UnableToReconstituteAggregateRoot::becauseOf($throwable->getMessage(), $throwable);
        }
    }

    private function retrieveAllEvents(AggregateRootId $aggregateRootId): Generator
    {
        $messages = $this->messages->retrieveAll($aggregateRootId);

        foreach ($messages as $message) {
            yield $message->event();
        }

        return $messages->getReturn();
    }

    public function persist(object $aggregateRoot): void
    {
        assert($aggregateRoot instanceof AggregateRoot, 'Expected $aggregateRoot to be an instance of '.AggregateRoot::class);

        $this->persistEvents(
            $aggregateRoot->aggregateRootId(),
            $aggregateRoot->aggregateRootVersion(),
            ...$aggregateRoot->releaseEvents()
        );
    }

    public function persistEvents(AggregateRootId $aggregateRootId, int $aggregateRootVersion, object ...$events): void
    {
        if (0 === count($events)) {
            return;
        }

        // decrease the aggregate root version by the number of raised events
        // so the version of each message represents the version at the time
        // of recording.
        $aggregateRootVersion = $aggregateRootVersion - count($events);
        $metadata = [
            Header::AGGREGATE_ROOT_ID => $aggregateRootId,
            Header::AGGREGATE_ROOT_TYPE => $this->classNameInflector->classNameToType($this->aggregateRootClassName),
        ];
        $messages = array_map(function (object $event) use ($metadata, &$aggregateRootVersion) {
            return $this->decorator->decorate(new Message(
                $event,
                $metadata + [Header::AGGREGATE_ROOT_VERSION => ++$aggregateRootVersion]
            ));
        }, $events);

        $this->messages->persist(...$messages);
    }
}
