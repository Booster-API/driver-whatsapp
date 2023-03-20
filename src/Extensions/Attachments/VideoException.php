<?php

namespace BotMan\Drivers\WhatsappWeb\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\Drivers\WhatsappWeb\Extensions\Attachments\Traits\AttachmentException;

class VideoException extends Video
{
    use AttachmentException;
}
