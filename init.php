<?php
session_start();
// ini_set("display_error",true);
define('_BASE_DIR_','');
define('_TITLE_','iTrader');
define('_API_','http://localhost:3000');
define('_SALT_','_SALT_');

require_once 'vendor/autoload.php';

require_once 'BladeFactory.php';

$views = __DIR__ ."/templates";
$cache = __DIR__ ."/cache";
$components = __DIR__ ."/components";
$language = __DIR__ ."/language";



$blade = new BladeFactory($views, $cache, $components, $language);  

// $lang = $_SESSION['user']['lang'] ?? 'en';
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

$blade->setLocale($lang);


foreach (glob("lib/*.php") as $filename){include_once $filename;}

spl_autoload_register(function($class) {
    if (!file_exists('modules/'.$class.'.php')) {
        //igone it
        return;
    }
    require_once 'modules/'.$class.'.php';
});

foreach (glob("functions/*.php") as $filename){include_once $filename;}

