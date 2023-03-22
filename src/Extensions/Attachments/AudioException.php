<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Audio;
use BoosterAPI\Whatsapp\Driver\Extensions\Attachments\Traits\AttachmentException;

class AudioException extends Audio
{
    use AttachmentException;
}
