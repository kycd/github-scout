<?php
require __DIR__ . '/Messenger.php';
require __DIR__ . '/Scout.php';

$config_str = file_get_contents(__DIR__ . '/../config/config.json');
$config = json_decode($config_str);

$scout = new Scout($config->targets);
$result = $scout->explore();

if (!in_array('--no-output', $argv)) {
    $messenger = new Messenger($config->channels);
    $messenger->sendMessage($result);
}

