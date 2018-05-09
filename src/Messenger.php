<?php

class Messenger
{
    protected $channels;

    public function __construct($config)
    {
        $this->channels = $config;
    }

    public function sendMessage($custom_message)
    {
        foreach ($this->channels as $channel) {
            if (isset($channel->disable) && $channel->disable == true) {
                continue;
            }

            $data = [
                "text" => $this->createMessage($custom_message, $channel->guards)
            ];

            $curl_client = curl_init();
            curl_setopt_array(
                $curl_client,
                [
                    CURLOPT_URL => $channel->hook,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($data),
                ]
            );
            curl_exec($curl_client);
            curl_close($curl_client);
        }
    }

    private function createMessage($custom_message, $guards)
    {
        $message = sprintf("%s\n", join(
            ', ', 
            array_map(
                function ($guard) {
                     return sprintf("<%s>", $guard);
                },
                $guards
            )
        ));

        $message .= sprintf("%s\n", $custom_message);
        return $message;
    }
}
