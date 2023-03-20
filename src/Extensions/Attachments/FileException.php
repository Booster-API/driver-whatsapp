<?php

namespace BotMan\Drivers\WhatsappWeb\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\File;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\Traits\AttachmentException;

class FileException extends File
{
    use AttachmentException;
}
