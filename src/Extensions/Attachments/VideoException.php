<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions\Attachments;

use BotMan\BotMan\Messages\Attachments\Video;
use BoosterAPI\Whatsapp\Driver\Extensions\Attachments\Traits\AttachmentException;

class VideoException extends Video
{
    use AttachmentException;
}
