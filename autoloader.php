<?php

function autoLoader($className) {

    

    $className = trim($className, '\\');
    $classNameArray = explode('\\' , $className);
    $baseDir = __DIR__.DIRECTORY_SEPARATOR.$classNameArray[0].DIRECTORY_SEPARATOR;
    $className = $classNameArray[1];

    $filepath = $baseDir.$className.'.php';
    if(file_exists($filepath)) {
        include_once $filepath;
    }
}

spl_autoload_register('autoLoader');

