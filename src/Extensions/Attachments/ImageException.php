<?php

namespace BoosterAPI\Whatsapp\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Image;
use BoosterAPI\Whatsapp\Extensions\Attachments\Traits\AttachmentException;

class ImageException extends Image
{
    use AttachmentException;
}
