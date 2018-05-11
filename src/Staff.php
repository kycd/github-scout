<?php

class Staff
{
    public function __construct()
    {
    }

    public function record($data, $save_path)
    {
        $dir_path = dirname($save_path);
        if (!file_exists($dir_path)) {
            mkdir($dir_path);
        }

        $fp = fopen($save_path, 'w');
        fwrite($fp, json_encode($data));
    }

    public function createReport($data, $record_path = '')
    {
        if (file_exists($record_path)) {
            $fp = fopen($record_path, 'r');
            $contents = fread($fp, filesize($record_path));
            $base_data = json_decode($contents);
        }
        $len = [
            'name' => 4,
            'watch' => 5,
            'star' => 4,
            'fork' => 4,
        ];
        foreach ($data as $repo_name => $repo_data) {
            if ($len['name'] < strlen($repo_data['name'])) {
                $len['name'] = strlen($repo_data['name']);
            }
            $keys = ['watch', 'star', 'fork'];
            foreach ($keys as $key) {
                $column_val = $repo_data[$key];
                $column_str = sprintf("%d", $column_val);
                $column_len = strlen($column_str);
                $data[$repo_name][$key . '_str'] = $column_str;

                if (isset($base_data->$repo_name)) {
                    $base_column_val = $base_data->$repo_name->$key;

                    if ($base_column_val > $column_val) {
                        $column_str = sprintf("%d%d", $base_column_val, $column_val - $base_column_val);
                        $column_len = strlen($column_str);
                $data[$repo_name][$key . '_str'] = $column_str;
                    } else if ($base_column_val < $column_val) {
                        $column_str = sprintf("%d+%d", $base_column_val, $column_val - $base_column_val);
                        $column_len = strlen($column_str);
                $data[$repo_name][$key . '_str'] = $column_str;
                    }
                }

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

        foreach ($data as $repo_data) {
            $table .= sprintf($field_format, $repo_data['name'], $repo_data['watch_str'], $repo_data['star_str'], $repo_data['fork_str']);
        }

        $table .= $border;
        $table .= "```";

        return $table;
    }
}
