<?php

namespace App\Service;

use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class QueueCounter
{
    private TransportInterface $transport;

    public function __construct(TransportInterface $messengerTransportDefault)
    {
        $this->transport = $messengerTransportDefault;
    }

    public function getMessageCount(): int
    {
        if ($this->transport instanceof ListableReceiverInterface) {
            return iterator_count($this->transport->all());
        }

        return 0;
    }
}
