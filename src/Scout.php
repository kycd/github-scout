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
        return $this->createReport($arr);
    }

    protected function createReport($arr)
    {
        $len = [
            'name' => 4,
            'watch' => 5,
            'star' => 4,
            'fork' => 4,
        ];
        foreach ($arr as $repo_data) {
            if ($len['name'] < strlen($repo_data['name'])) {
                $len['name'] = strlen($repo_data['name']);
            }
            $keys = ['watch', 'star', 'fork'];
            foreach ($keys as $key) {
                $column_len = strlen(sprintf("%d", $repo_data[$key]));
                $len[$key] = max($len[$key], $column_len);
            }
        }
        $border_format = sprintf(
            "+%%%ds+%%%ds+%%%ds+%%%ds+\n",
            $len['name'] + 2,
            $len['watch'] + 2,
            $len['star'] + 2,
            $len['fork'] + 2
        );
        $field_format = sprintf(
            "| %%-%ds | %%%ds | %%%ds | %%%ds |\n",
            $len['name'],
            $len['watch'],
            $len['star'],
            $len['fork']
        );
        
        $border = sprintf(
            $border_format,
            str_repeat("-", $len['name'] + 2),
            str_repeat("-", $len['watch'] + 2),
            str_repeat("-", $len['star'] + 2),
            str_repeat("-", $len['fork'] + 2)
        );

        $table = "";

        $table .= "```";
        $table .= $border;
        $table .= sprintf($field_format, "repo", "watch", "star", "fork");
        $table .= $border;

        foreach ($arr as $repo_data) {
            $table .= sprintf($field_format, $repo_data['name'], $repo_data['watch'], $repo_data['star'], $repo_data['fork']);
        }

        $table .= $border;
        $table .= "```";

        return $table;
    }
}
