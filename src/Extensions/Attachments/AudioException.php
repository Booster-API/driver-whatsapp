<?php

namespace BotMan\Drivers\WhatsappWeb\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\Traits\AttachmentException;

class AudioException extends Audio
{
    use AttachmentException;
}
