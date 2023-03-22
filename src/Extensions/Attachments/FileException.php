<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\File;
use BoosterAPI\Whatsapp\Driver\Extensions\Attachments\Traits\AttachmentException;

class FileException extends File
{
    use AttachmentException;
}
