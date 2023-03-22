<?php

namespace BoosterAPI\Whatsapp\Extensions\Attachments\Traits;

trait AttachmentException
{
    /** @var string */
    protected string $exception;

    public function __construct($exception)
    {
        parent::__construct(null);
        $this->exception = $exception;
    }

    public function getException(): string
    {
        return $this->exception;
    }
}
