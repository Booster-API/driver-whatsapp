<?php

namespace BoosterAPI\Whatsapp\Driver;

use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BoosterAPI\Whatsapp\Driver\Extensions\Attachments\VideoException;

class WhatsappVideoDriver extends WhatsappDriver
{
    const DRIVER_NAME = 'TelegramVideo';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return $this->event->get('type') === 'video' && !$this->fromMe();
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
            Video::PATTERN,
            $this->event->get('from'),
            $this->event->get('to'),
            $this->event
        );
        $message->setVideos($this->getVideos());

        $this->messages = [$message];
    }

    /**
     * Retrieve a image from an incoming message.
     * @return array A download for the image file.
     */
    private function getVideos(): array
    {
        $video = $this->message->get('attachmentData');
//        $caption = $this->event->get('caption');

        return [new Video($this->buildFileApiUrl(), $video['data'])];
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }
}
