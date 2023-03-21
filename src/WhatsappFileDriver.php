<?php

namespace BotMan\Drivers\WhatsappWeb;

use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\FileException;

class WhatsappFileDriver extends WhatsappDriver
{
    const DRIVER_NAME = 'TelegramFile';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return $this->event->get('type') === 'document' && !$this->fromMe();
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
            File::PATTERN,
            $this->event->get('from'),
            $this->event->get('to'),
            $this->event
        );
        $message->setFiles($this->getFiles());

        $this->messages = [$message];
    }

    /**
     * Retrieve a file from an incoming message.
     * @return array A download for the files.
     */
    private function getFiles(): array
    {
        $file = $this->message->get('attachmentData');

        return [new File($this->buildFileApiUrl(), $file['data'])];
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }
}
