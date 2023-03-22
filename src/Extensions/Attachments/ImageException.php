<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Image;
use BoosterAPI\Whatsapp\Driver\Extensions\Attachments\Traits\AttachmentException;

class ImageException extends Image
{
    use AttachmentException;
}
