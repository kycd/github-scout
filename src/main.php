<?php
require __DIR__ . '/Messenger.php';
require __DIR__ . '/Scout.php';
require __DIR__ . '/Staff.php';

$config_str = file_get_contents(__DIR__ . '/../config/config.json');
$config = json_decode($config_str);

$scout = new Scout([
    'targets' => $config->targets,
    'token' => $config->token
]);
$result = $scout->explore();

$record_path = __DIR__ . '/../tmp/data.json';
$staff = new Staff();
$message = $staff->createReport($result, $record_path);
$staff->record($result, $record_path);

if (!in_array('--no-output', $argv)) {
    $messenger = new Messenger($config->channels);
    $messenger->sendMessage($message);
}

