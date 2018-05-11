<?php
require __DIR__ . '/Messenger.php';
require __DIR__ . '/Scout.php';
require __DIR__ . '/Staff.php';

$config_str = file_get_contents(__DIR__ . '/../config/config.json');
$config = json_decode($config_str);

$scout = new Scout($config->targets);
$result = $scout->explore();

$staff = new Staff();
$message = $staff->createReport($result);

if (!in_array('--no-output', $argv)) {
    $messenger = new Messenger($config->channels);
    $messenger->sendMessage($message);
}

