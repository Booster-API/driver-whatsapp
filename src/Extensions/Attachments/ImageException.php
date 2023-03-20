<?php

namespace BotMan\Drivers\WhatsappWeb\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\Traits\AttachmentException;

class ImageException extends Image
{
    use AttachmentException;
}
