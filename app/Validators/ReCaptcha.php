<?php
namespace App\Validators;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ReCaptcha
{
    /**
     * @throws GuzzleException
     */
    public function validate($attribute, $value, $parameters, $validator)
    {
        $client = new Client;
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                    [
                        'secret' => config('fitz.recaptcha.secret'),
                        'response' => $value
                    ]
            ]
        );
        $body = json_decode((string)$response->getBody());
        return $body->success;
    }
}
