<?php

namespace BoosterAPI\Whatsapp\Driver\Extensions;

use Illuminate\Support\Collection;

/**
 * Class Keyboard.
 */
class Keyboard
{
    const TYPE_KEYBOARD = 'keyboard';
    const TYPE_INLINE = 'inline_keyboard';

    protected bool $oneTimeKeyboard = false;
    protected bool $resizeKeyboard = false;

    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @param string $type
     * @return Keyboard
     */
    public static function create($type = self::TYPE_INLINE): Keyboard
    {
        return new self($type);
    }

    /**
     * Keyboard constructor.
     * @param string $type
     */
    public function __construct($type = self::TYPE_INLINE)
    {
        $this->type = $type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function type($type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function oneTimeKeyboard($active = true): static
    {
        $this->oneTimeKeyboard = $active;

        return $this;
    }

    /**
     * @param bool $active
     * @return $this
     */
    public function resizeKeyboard($active = true): static
    {
        $this->resizeKeyboard = $active;

        return $this;
    }

    /**
     * Add a new row to the Keyboard.
     * @param KeyboardButton[] $buttons
     * @return Keyboard
     */
    public function addRow(KeyboardButton ...$buttons): static
    {
        $this->rows[] = $buttons;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'reply_markup' => json_encode(Collection::make([
                $this->type => $this->rows,
                'one_time_keyboard' => $this->oneTimeKeyboard,
                'resize_keyboard' => $this->resizeKeyboard,
            ])->filter()),
        ];
    }
}
