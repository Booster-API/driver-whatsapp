<?php

namespace BoosterAPI\Whatsapp\Driver;

use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class WhatsappLocationDriver extends WhatsappDriver
{
    const DRIVER_NAME = 'TelegramLocation';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return ! is_null($this->event->get('from')) && ! is_null($this->event->get('location'));
    }

    /**
     * @return bool
     */
    public function hasMatchingEvent(): bool
    {
        return false;
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages(): array
    {
        if (empty($this->messages)) {
            $this->loadMessages();
        }

        return $this->messages;
    }

    /**
     * Load Whatsapp Web messages.
     */
    public function loadMessages()
    {
        $message = new IncomingMessage(
            Location::PATTERN,
            $this->event->get('from')['id'],
            $this->event->get('chat')['id'],
            $this->event
        );
        $message->setLocation(new Location(
            $this->event->get('location')['latitude'],
            $this->event->get('location')['longitude'],
            $this->event->get('location')
        ));

        $this->messages = [$message];
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }
}
