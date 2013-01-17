<?php

error_reporting(E_ALL|E_STRICT);
ini_set("display_errors",1);


function __autoload($class_name){
    $file = getcwd() . "/" . $class_name . ".php";
    if(file_exists($file)){
        include($file);
    }
    else {
        throw new Exception("Class $file was not found");
    }
}

$parentFunctions = new ParentFunctions();
$output = $parentFunctions->__init();
