<?php

namespace App\Listeners;

use App\Events\VisitBooked;
use App\Services\TicketGenerator\TicketGeneratorInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateTicketFile implements ShouldQueue
{
    use InteractsWithQueue;

    protected $generator;

    /**
     * Create the event listener.
     */
    public function __construct(TicketGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Handle the event.
     */
    public function handle(VisitBooked $event): void
    {
        $this->generator->generate($event->registration);
    }
}
