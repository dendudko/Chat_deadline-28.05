<?php

namespace Controllers;
use Twig\Environment;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$url = 'http://fefu.ml';

class Controller{

private $twig;
private $logger;
private $messengerHandler;

public function __construct(Environment $twig, Logger $logger)
{
    $this->twig = $twig;
    $this->logger = new Logger('my_logger');
    $this->messengerHandler = new StreamHandler('messenger.log', Logger::INFO);
}
    
function print_messages(){
    $content = json_decode(file_get_contents('history.json'));
    foreach($content->messages as $message){
    echo("<b>$message->sender</b> $message->date<br>$message->text<br><br>");
    }
}

function add_message(){
    $message = json_decode(file_get_contents('history.json'), true);
    $message['messages'][] = [
    'text'=>$_GET['message'],
    'date'=>date('H:i', time()),
    'sender'=>$_COOKIE['login']
    ];
    file_put_contents('history.json', json_encode($message));
    $this->logger->pushHandler($this->messengerHandler);
    $this->logger->info('New message', [
    'text'=>$_GET['message'],
    'date'=>date('H:i', time()),
    'sender'=>$_COOKIE['login']
    ]);
}

function clear_history(){
    file_put_contents('history.json', '');
    $this->print_messages();
} 

function reset_history(){
    $this->clear_history();
    $content = json_decode(file_get_contents('reset.json'));
    file_put_contents('history.json', json_encode($content));
    $this->print_messages();
}

public function unauthorized()
{
    echo $this->twig->render('main.html.twig');
    $this->print_messages();
}

public function authorized()
{
?>
<form action="/send" method="GET">
    <input placeholder="Напишите сообщение, <?php echo($_COOKIE['login']);?>..." name="message" style="width: 250px; height: 40px">
    <input type="submit" value="Отправить" style="width: 250px; height: 40px; 
    margin-left:-5px;">
</form>
<?php
    echo $this->twig->render('authorized.html.twig');
    if(isset($_GET['message']) AND $_GET['message'] != '' AND isset($_COOKIE['login'])){
        $this->add_message();
        header('Location: '.$url.'/send?message=');
    }
    $this->print_messages();
}

function show_log()
{
    echo $this->twig->render('log.html.twig');
    $file = file_get_contents('messenger.log');
    echo $file;
}

}
?>
