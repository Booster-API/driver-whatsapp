<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions;

use BotMan\BotMan\Interfaces\UserInterface;
use BotMan\BotMan\Users\User as BotManUser;

class User extends BotManUser implements UserInterface
{
    /**
     * The member's status in the chat.
     * Can be “creator”, “administrator”, “member”, “restricted”, “left” or “kicked”.
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        $info = $this->getInfo();

        return $info['status'] ?? null;
    }

    /**
     * IETF language tag of the user's language.
     *
     * @return string|null
     */
    public function getLanguageCode(): ?string
    {
        $info = $this->getInfo();

        return $info['user']['language_code'] ?? null;
    }
}
