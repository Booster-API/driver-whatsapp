<?php

namespace BotMan\Drivers\WhatsappWeb;

use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\AudioException;

class WhatsappAudioDriver extends WhatsappDriver
{
    const DRIVER_NAME = 'TelegramAudio';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return in_array($this->event->get('type'), ['ptt', 'audio']) && !$this->fromMe();
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
            Audio::PATTERN,
            $this->event->get('from'),
            $this->event->get('to'),
            $this->event
        );
        $message->setAudio($this->getAudio());

        $this->messages = [$message];
    }

    /**
     * Retrieve a image from an incoming message.
     * @return array A download for the audio file.
     */
    private function getAudio(): array
    {
        $audio = $this->message->get('attachmentData');

        return [new Audio($this->buildFileApiUrl(), $audio['data'])];
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }
}
