<?php
require 'vendor/autoload.php';

if(file_exists('.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}

if(!isset($_POST['stripped-text'])) {
    error_log('Sem stripped-text');
    return;
}
preg_match('/Olá (?<nome>.*)\r\n/', $_POST['stripped-text'], $matches);

if(!isset($matches['nome'])) {
    error_log('Nome não encontrado: '.$_POST['stripped-text']);
    return;
}

$redis   = new Predis\Client([
    'host' => getenv('REDIS_HOST'),
    'port' => getenv('REDIS_PORT')
]);
$redis->auth(getenv('REDIS_AUTH'));
$redis->incr('counter');
error_log('Contador incrementado');

$telegram = new Telegram\Bot\Api();
$telegram->sendMessage([
    'chat_id' => getenv('CHAT_ID'),
    'text' => "Mais um: ".$matches['nome']."\nTotal: ".$redis->get('counter')
]);
error_log('Enviado!');