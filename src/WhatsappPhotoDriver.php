<?php

namespace BotMan\Drivers\WhatsappWeb;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\ImageException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Mime\MimeTypes;

class WhatsappPhotoDriver extends WhatsappDriver
{
    const DRIVER_NAME = 'WhatsAppPhoto';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return $this->event->get('type') === 'image' && !$this->fromMe();
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
            Image::PATTERN,
            $this->event->get('from'),
            $this->event->get('to'),
            $this->event
        );

        $message->setImages($this->getImages());

        $this->messages = [$message];
    }

    /**
     * Retrieve a image from an incoming message.
     * @return array A download for the image file.
     */
    private function getImages(): array
    {
        $photo = $this->message->get('attachmentData');
        $caption = $this->event->get('caption');

        return [(new Image($this->buildFileApiUrl(), $photo['data']))->title($caption)];
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return false;
    }
}
