# Laravel Sendpulse mail transport

Laravel mail transport based on https://github.com/sendpulse/sendpulse-rest-api-php library

**Installation:**
```
    composer require olenchenko-a/laravel-sendpulse-mail
```
**Configuration:**


In the config **config/mail.php** add a new transport:

```php
return [
    //...
    'mailers' => [
        //...
        'sendpulse' => [
            'transport' => 'sendpulse',
            'api_user_id' => env('SPAPI_USER_ID'),
            'api_secret' => env('SPAPI_SECRET'),
        ],
    ]
]
```

**Register the Service Provider**

In ```config/app.php``` to providers section add

```
LaravelSendpulseMail\SendpulseMailServiceProvider::class,
```

In ```config/services.php``` add

```php
'send_pulse' => [
        'api_user_id' => env('SPAPI_USER_ID'),
        'api_secret' => env('SPAPI_SECRET'),
    ],

```

Add to your .env

```
MAIL_MAILER=sendpulse
```

And provide your sendpulse credentials

```
SPAPI_USER_ID="YOUR_USER_ID"
SPAPI_SECRET="YOUR_SECRET"
```

Package also provide **EmailSend** event which contain the result of the operation sending email.
You can use it for additional validating email sending result

```php
    public function handle(EmailSend $event): void
    {
        if($event->result) {
            dispatch(function () use ($event) {
                $data = $this->client->get('smtp/emails/' . $event->emailId);

                if($data['smtp_answer_code'] != 250) {
                    // Despite email was successfuly sent to Sendpulse,
                    // it was not delivered to recepient
                }
            })->delay(now()->addMinutes(3));
        }
    }
```
