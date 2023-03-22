<?php

namespace BoosterAPI\Whatsapp\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Audio;
use BoosterAPI\Whatsapp\Extensions\Attachments\Traits\AttachmentException;

class AudioException extends Audio
{
    use AttachmentException;
}
