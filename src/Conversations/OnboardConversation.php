<?php

namespace BotMan\Drivers\WhatsappWeb\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class OnboardConversation extends Conversation {

    protected string $name;
    protected string $age;

    public function run()
    {
        $this->say('Hello');
        $this->ask('What is your name?', function (Answer $answer) {
            $value = $answer->getText();

            if (trim($value) == '') {
                $this->repeat('This does not look like a real name, please provide your name.');
                return;
            }

            $this->name = $value;
            $this->askAge();
        });
    }

    protected function askAge()
    {
        $this->ask('What is your age?', function (Answer $answer) {
            $this->age = $answer->getText();
            $this->say('Nice to meet you, ' . $this->name);
            $this->say('Your age is, ' . $this->age);

            $this->askImage();
        });
    }

    protected function askImage() {
        $this->askForImages('Please send me a picture of you.', function($images) {
           $this->say('Thank you - I received ' . count($images) . ' images.');
        }, function() {
            $this->repeat('Ummm this does note look like a valid image to me.');
        });
    }
}
