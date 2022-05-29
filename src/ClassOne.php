<?php
namespace Classes;
class ClassOne
{

function print_message(){
    $text="Хеллоу ворлд! Я экземпляр класса ".get_class($this). "!";
    $date=date('H:i', time());
    $sender=get_class($this);
    echo("<p align='right'><b>$sender</b>$date<br>$text<br><br></p></div>");
}

}

?>