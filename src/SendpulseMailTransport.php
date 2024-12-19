<?php
declare(strict_types=1);

namespace LaravelSendpulseMail;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;
use LaravelSendpulseMail\Events\EmailSend;
use Symfony\Component\Mime\Email;

class SendpulseMailTransport extends AbstractTransport
{
    protected $client;

    public function __construct(string $api_user_id, string $api_secret)
    {
        parent::__construct();

        $this->client = new ApiClient($api_user_id, $api_secret, new FileStorage());
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $email = array(
            'html' => base64_encode($email->getHtmlBody()),
            'text' => $email->getTextBody(),
            'subject' => $email->getSubject(),
            'attachments_binary' => $this->getAttachments($email),
            'from' => collect($email->getFrom())->map(function ($address) {
                return [
                    "name" => $address->getName(),
                    "email" => $address->getAddress()
                ];
            })->first(),
            'to' => collect($email->getTo())->map(function ($address) {
                return [
                    "name" => $address->getName(),
                    "email" => $address->getAddress()
                ];
            })->all(),
        );

        $response = $this->client->post('smtp/emails', [
            'email' => $email,
        ]);

        if(is_null($response)) {
            throw new \Exception("Sending email trough sendpulse failed");
        } elseif(isset($response["id"]) && $response["result"] == true) {
            EmailSend::dispatch($response["result"], $email, $response["id"]);
        }
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'sendpulse';
    }

    private function getAttachments(Email $email): array
    {
        $attachments = [];

        foreach($email->getAttachments() as $attachment) {
            $attachments[$attachment->getFilename()] = base64_encode($attachment->getBody());
        }

        return $attachments;
    }
}
