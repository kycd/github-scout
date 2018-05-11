<?php

class Scout
{
    protected $targets;
    public function __construct($config)
    {
        $this->targets = $config;
    }

    public function explore()
    {
        $curl_client = curl_init();
        curl_setopt_array(
            $curl_client,
            [
                CURLOPT_USERAGENT => 'kycd-github-scout',
                CURLOPT_RETURNTRANSFER => true,
            ]
        );
        $arr = [];
        foreach ($this->targets as $target) {
            foreach ($target->repos as $repo) {
                $url = sprintf("https://api.github.com/repos/%s/%s", $target->owner, $repo);
                curl_setopt($curl_client, CURLOPT_URL, $url);
                $response = curl_exec($curl_client);
                $data = json_decode($response);
                $arr[] = [
                    'name' => sprintf("https://github.com/%s/%s", $target->owner, $repo),
                    'watch' => $data->subscribers_count,
                    'star' => $data->stargazers_count,
                    'fork' => $data->forks
                ];
            }
        }
        curl_close($curl_client);
        return $arr;
    }
}
