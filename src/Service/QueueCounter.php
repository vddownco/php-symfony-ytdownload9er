<?php

namespace App\Service;

use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class QueueCounter
{
    public function __construct(private readonly TransportInterface $messengerTransportAsync)
    {
    }

    public function getMessageCount(): int
    {
        if ($this->messengerTransportAsync instanceof ListableReceiverInterface) {
            return iterator_count($this->messengerTransportAsync->all());
        }

        return 0;
    }
}
