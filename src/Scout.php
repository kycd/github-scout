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
        $max_repo_length = 0;
        foreach ($arr as $repo_data) {
            if ($max_repo_length < strlen($repo_data['name'])) {
                $max_repo_length = strlen($repo_data['name']);
            }
        }
        $border_format = sprintf("+%%%ds+%%%ds+%%%ds+%%%ds+\n", $max_repo_length + 2, 7, 6, 6);
        $field_format = sprintf("| %%-%ds | %%%ds | %%%ds | %%%ds |\n", $max_repo_length, 5, 4, 4);
        
        $border = sprintf($border_format, str_repeat("-", $max_repo_length + 2), str_repeat("-", 7), str_repeat("-", 6), str_repeat("-", 6));

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
