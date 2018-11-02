<?php
use Telegram\Bot\Api;

require 'vendor/autoload.php';

if(file_exists('.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}

if(!isset($_POST['stripped-text'])) {
    return;
}
preg_match('/Olá (?<nome>.*)\r\n/', $_POST['stripped-text'], $matches);

if(!isset($_POST['nome'])) {
    return;
}

$redis   = new Predis\Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT')
]);
$redis->auth(getenv('REDIS_AUTH'));
$redis->incr('counter');

$telegram = new Api();
$telegram->sendMessage([
    'chat_id' => getenv('CHAT_ID'),
    'text' => "Mais um: ".$matches['nome']."\nTotal: ".$redis->get('counter')
]);