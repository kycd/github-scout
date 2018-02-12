<?php

class Messenger
{
    protected $hook;
    protected $guards;

    public function __construct($config)
    {
        $this->hook = $config->hook;
        $this->guards = $config->guards;
    }

    public function sendMessage($custom_message)
    {
        $data = [
            "text" => $this->createMessage($custom_message)
        ];

        $curl_client = curl_init();
        curl_setopt_array(
            $curl_client,
            [
                CURLOPT_URL => $this->hook,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
            ]
        );
        curl_exec($curl_client);
        curl_close($curl_client);
    }

    private function createMessage($custom_message)
    {
        $message = sprintf("%s\n", join(
            ', ', 
            array_map(
                function ($guard) {
                     return sprintf("<%s>", $guard);
                },
                $this->guards
            )
        ));

        $message .= sprintf("%s\n", $custom_message);
        return $message;
    }
}
