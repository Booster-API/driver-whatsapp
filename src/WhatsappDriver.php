<?php

namespace BoosterAPI\Whatsapp\Driver;

use Illuminate\Support\Collection;
use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\File;
use BoosterAPI\Whatsapp\Driver\Extensions\User;
use BotMan\BotMan\Messages\Attachments\Audio;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Attachments\Contact;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use BotMan\BotMan\Drivers\Events\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use BotMan\BotMan\Messages\Attachments\Location;
use Symfony\Component\HttpFoundation\ParameterBag;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BoosterAPI\Whatsapp\Driver\Exceptions\WhatsappWebException;
use Symfony\Component\Mime\MimeTypes;

class WhatsappDriver extends HttpDriver
{
    const DRIVER_NAME = 'WhatsappWeb';
    const GENERIC_EVENTS = [
        'qr',
        'authenticated',
        'disconnected',
        'auth_failure',
        'ready',
        'loading_screen',
        'message_create',
        'message_ack',
        'message_revoke_everyone',
        'message_revoke_me',
        'group_update',
        'group_join',
        'group_leave',
    ];

    protected string $endpoint = 'send-message';

    protected array $messages = [];

    protected string $instance_id = '';

    /** @var Collection */
    protected Collection $queryParameters;

    /** @var Collection */
    protected Collection $header;
    /** @var Collection */
    protected Collection $message;

    /**
     * @param Request $request
     */
    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag((array)json_decode($request->getContent(), true));

        $message = $this->payload->get('data');
        $this->instance_id = $this->payload->get('instance_id');

        $this->event = Collection::make($message['message']['_data'] ?? []);
        $this->message = Collection::make($message['message'] ?? []);
        $this->config = Collection::make($this->config->get('whatsapp-web'));
        $this->queryParameters = Collection::make($request->query);
        $this->header = Collection::make([
            'x-instance-id: ' . $this->payload->get('instance_id'),
            'x-instance-secret: ' . $this->config->get('secret'),
            'x-api-key: ' . $this->config->get('api_key'),
        ]);
    }

    /**
     * @param IncomingMessage $matchingMessage
     * @return User
     * @throws WhatsappWebException
     */
    public function getUser(IncomingMessage $matchingMessage): User
    {
        $response = $this->http->post($this->buildApiUrl('contacts/' . $matchingMessage->getSender()), [], [
            'x-instance-id' => $this->config->get('id'),
            'x-instance-secret' => $this->config->get('secret'),
            'x-api-key' => $this->config->get('api_key'),
        ]);

        $responseData = json_decode($response->getContent(), true);

        if ($response->getStatusCode() !== 200) {
            throw new WhatsappWebException('Error retrieving user info: ' . $responseData['description']);
        }

        $userData = Collection::make($responseData['result']['user']);

        return new User(
            $userData->get('id'),
            $userData->get('first_name'),
            $userData->get('last_name'),
            $userData->get('username'),
            $responseData['result']
        );
    }

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest(): bool
    {
        return $this->payload->get('type') === 'message' &&
            $this->event->get('type') === 'chat' &&
            !$this->fromMe();
    }

    /**
     * @return GenericEvent|bool
     */
    public function hasMatchingEvent(): GenericEvent|bool
    {
        $event = false;

        foreach (self::GENERIC_EVENTS as $genericEvent) {
            if ($this->payload->has($genericEvent)) {
                $event = new GenericEvent($this->payload->get($genericEvent));
                $event->setName($genericEvent);

                return $event;
            }
        }

        return $event;
    }

    /**
     * @param IncomingMessage $message
     * @return Answer
     */
    public function getConversationAnswer(IncomingMessage $message): Answer
    {
        if ($this->event->get('type') === 'list_response') {
            $callback = Collection::make($this->payload->get('listResponse'));

            return Answer::create($callback->get('title'))
                ->setInteractiveReply(true)
                ->setMessage($message)
                ->setValue($callback->get('singleSelectReply')['selectedRowId']);
        }

        return Answer::create($message->getText())->setMessage($message);
    }

    /**
     * Retrieve the chat message.
     *
     * @return array
     */
    public function getMessages(): array
    {
        if (empty($this->messages)) {
            $this->loadMessages();
        }

        return $this->messages;
    }

    /**
     * Load Whatsapp Web messages.
     */
    public function loadMessages()
    {
        $messages = [
            new IncomingMessage(
                $this->event->get('body'),
                $this->event->get('from'),
                $this->event->get('to'),
                $this->event
            ),
        ];

        $this->messages = $messages;
    }

    /**
     * @param IncomingMessage $matchingMessage
     * @return Response
     */
    public function types(IncomingMessage $matchingMessage): Response
    {
        $parameters = [
            'typing' => true,
        ];

        return $this->http->post(
            "chat/{$matchingMessage->getRecipient()}/typing", [], $parameters, $this->header->all());
    }

    /**
     * @return void
     */
    public function replay(): void
    {
        $this->event->put('replay', $this->event->get('id')['_serialized']);
    }

    /**
     * Convert a Question object into a valid
     * quick reply response object.
     *
     * @param Question $question
     * @return array
     */
    private function convertQuestion(Question $question): array
    {
        $replies = Collection::make($question->getButtons())->map(function ($button) {
            return [
                array_merge([
                    'text' => (string)$button['text'],
                    'callback_data' => (string)$button['value'],
                ], $button['additional']),
            ];
        });

        return $replies->toArray();
    }

    /**
     * @param string|Question|IncomingMessage $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return array
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = []): array
    {
        $this->endpoint = 'chat/send-message';

        $defaultAdditionalParameters = $this->config->get('default_additional_parameters', []);
        $parameters = array_merge_recursive([
            'quote_message_id' => $this->event->get('replay')
        ], $additionalParameters + $defaultAdditionalParameters);

        /*
         * If we send a Question with buttons, ignore
         * the text and append the question.
         */
        if ($message instanceof Question) {
            $parameters['text'] = $message->getText();
            $parameters['reply_markup'] = json_encode([
                'inline_keyboard' => $this->convertQuestion($message),
            ], true);
        } elseif ($message instanceof OutgoingMessage) {
            if ($message->getAttachment() !== null) {
                $attachment = $message->getAttachment();
                $parameters['caption'] = $message->getText();
                if ($attachment instanceof Image) {
                    $this->endpoint = 'chat/send-file';
                    $parameters['file'] = $attachment->getPayload();
                    $parameters['mime'] = $this->event->get('mimetype', 'image/png');
                    $parameters['is_audio'] = false;

                    // If has a title, overwrite the caption
                    if ($attachment->getTitle() !== null) {
                        $parameters['caption'] = $attachment->getTitle();
                    }
                } elseif ($attachment instanceof Video) {
                    $this->endpoint = 'chat/send-file';
                    $parameters['file'] = $attachment->getPayload();
                    $parameters['mime'] = $this->event->get('mimetype', 'video/mp4');
                    $parameters['is_audio'] = false;
                } elseif ($attachment instanceof Audio) {
                    $this->endpoint = 'chat/send-file';
                    $parameters['file'] = $attachment->getPayload();
                    $parameters['mime'] = $this->event->get('mimetype', 'audio/mpeg-3');
                    $parameters['is_audio'] = false;
                } elseif ($attachment instanceof File) {
                    $this->endpoint = 'chat/send-file';
                    $parameters['file'] = $attachment->getPayload();
                    $parameters['mime'] = $this->event->get('mimetype', '*/*');
                    $parameters['is_audio'] = false;
                } elseif ($attachment instanceof Location) {
                    $this->endpoint = 'chat/send-location';
                    $parameters['latitude'] = $attachment->getLatitude();
                    $parameters['longitude'] = $attachment->getLongitude();
                    if (isset($parameters['title'], $parameters['address'])) {
                        $this->endpoint = 'sendVenue';
                    }
                } elseif ($attachment instanceof Contact) {
                    $this->endpoint = 'chat/send-contact';
                    $parameters['phone_number'] = $attachment->getPhoneNumber();
                    $parameters['first_name'] = $attachment->getFirstName();
                    $parameters['last_name'] = $attachment->getLastName();
                    $parameters['user_id'] = $attachment->getUserId();
                    if (null !== $attachment->getVcard()) {
                        $parameters['vcard'] = $attachment->getVcard();
                    }
                }
            } else {
                $parameters['message'] = $message->getText();
            }
        } else {
            $parameters['message'] = $message;
        }

        return $parameters;
    }

    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload): Response
    {
        return $this->http->post(
            $this->buildApiUrl($this->endpoint . '/' . $this->event->get('from')),
            [],
            $payload,
            $this->header->all());
    }

    /**
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->config->get('secret'));
    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string $endpoint
     * @param array $parameters
     * @param IncomingMessage $matchingMessage
     * @return Response
     */
    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage): Response
    {
        $parameters = array_replace_recursive([
            'chat_id' => $matchingMessage->getRecipient(),
        ], $parameters);

        return $this->http->post($this->buildApiUrl($endpoint), [], $parameters, $this->header->all());
    }

    /**
     * Generate the Whatsapp Web API url for the given endpoint.
     *
     * @param $endpoint
     * @return string
     */
    protected function buildApiUrl($endpoint): string
    {
        return $this->config->get('url') . '/' . $endpoint;
    }


    /**
     * Generate the Telegram File-API url for the given endpoint.
     *
     * @return string
     */
    protected function buildFileApiUrl(): string
    {
        $attachmentData = $this->message->get('attachmentData');
        if (!$attachmentData) {
            return '';
        }
        $uuid = Str::uuid();
        $mimeTypes = new MimeTypes();

        $safeName = $uuid . '.' . $mimeTypes->getExtensions(explode(';', $attachmentData['mimetype'])[0])[0];
        $path = 'upload' . DIRECTORY_SEPARATOR . 'chat' . DIRECTORY_SEPARATOR . $this->instance_id . DIRECTORY_SEPARATOR . $this->event->get('type') . DIRECTORY_SEPARATOR . $safeName;

        $storage = Storage::disk('public');

        $storage->put($path, base64_decode($attachmentData['data']));
        return Str::of($storage->url($path))->explode(DIRECTORY_SEPARATOR)->join('/');
    }

    public function fromMe(): bool
    {
        return $this->event->get('id', ['fromMe' => true])['fromMe'];
    }
}
