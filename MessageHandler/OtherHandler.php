<?php

namespace App\MessageHandler;

use App\Model\Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


class OtherHandler implements MessageHandlerInterface
{
    public function __invoke(Message $message)
    {
        var_dump($message);
        //throw new \Exception('test');
    }
}
