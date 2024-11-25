<?php
declare(strict_types=1);

namespace LaravelSendpulseMail\Events;

use Illuminate\Foundation\Events\Dispatchable;

class EmailSend
{
    use Dispatchable;

    /**
    * EmailSend Event
    *
    * This event is dispatched after sending the email,
    * carrying the result of the operation and relevant details.
    *
    * @param bool $result Indicates whether the email was sent successfully.
    * @param array $data The data provided for sending the email.
    * @param ?string $emailId The unique identifier of the email if sent successfully. Null if the operation failed.
    */
    public function __construct(
        public bool $result,
        public array $data,
        public ?string $emailId = null,
    ) {}
}
