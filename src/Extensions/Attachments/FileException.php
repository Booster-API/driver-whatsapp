<?php

namespace BoosterAPI\Whatsapp\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\File;
use BoosterAPI\Whatsapp\Extensions\Attachments\Traits\AttachmentException;

class FileException extends File
{
    use AttachmentException;
}
