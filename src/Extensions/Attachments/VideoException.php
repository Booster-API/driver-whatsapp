<?php

namespace BoosterAPI\Whatsapp\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Video;
use BoosterAPI\Whatsapp\Extensions\Attachments\Traits\AttachmentException;

class VideoException extends Video
{
    use AttachmentException;
}
