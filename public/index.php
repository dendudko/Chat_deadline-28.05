<?php
use Controllers\Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once dirname(__DIR__) . "/vendor/autoload.php";

$loader = new FilesystemLoader(dirname(__DIR__) . "/templates");
$log = new Logger('my_logger');
$userHandler = new StreamHandler('messenger.log', Logger::INFO);
$log->pushHandler($userHandler);
$twig = new Environment($loader);
$controller = new Controller($twig, $log);

$user = "root";
$password = "password";
$PDO = new PDO('mysql:host=localhost; dbname=chatusers', $user, $password);
$stmt = $PDO->prepare('SELECT * from users;');
$stmt->execute();
$results = $stmt->fetchAll();
$users = array();
foreach($results as $result){
    $users[$result['login']]=$result['password'];
}

$url = 'http://fefu.ml';

$uri = $_SERVER['REQUEST_URI'];
switch ($uri){
case '/logout?':
    {
    $log->info('Logout: ', ["user"=>$_COOKIE['login']]);    
    setcookie('login', '');   
    header('Location: '.$url); 
    break;
    }
case '/clear?':
    {
    $controller->clear_history();
    $log->info('History was cleared', ["user"=>$_COOKIE['login']]);
    header('Location: '.$url);
    break;
    }
case '/reset?':
    {
    $controller->reset_history();
    $log->info('History was reset', ["user"=>$_COOKIE['login']]);
    header('Location: '.$url);
    break;
    }
case '/show_log?':
    {
    $log->info('Log was shown',  ["user"=>$_COOKIE['login']]);
    $controller->show_log();
    break;
    }
}
if (isset($_GET['login'])&&isset($_GET['password'])) {
    if ($users[$_GET['login']]==$_GET['password'] AND $users[$_GET['login']]!=""){
        setcookie('login', $_GET['login'], time() + 180);
        $log->info('Login: ', ["user"=>$_COOKIE['login']]);
        header('Location: '.$url);
    } else {
        $log->info('Incorrect login or password');
        ?>
        Неверный логин или пароль!<br><br>
        <?php
    }
}

if (isset($_COOKIE['login'])){
    $controller->authorized();
}
else{
    $controller->unauthorized();
}

spl_autoload_register(function ($className) {
    include_once __DIR__ . "/../src/" . str_replace("\\", "/", $className) . ".php";
});
$cOne = new ClassOne();
$cTwo = new subsrc\ClassTwo();
$cOne->shout();
$cTwo->shout();

?>
