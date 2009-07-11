<?php

    /* $Id$ */
    
    $cur_dir = dirname(__FILE__);
    require_once $cur_dir . '/lib/Init.php';
    require_once $cur_dir . '/lib/Init/Exception.php';
        
    Init::define('DS',          DIRECTORY_SEPARATOR);
    Init::define('ROOT',        dirname(realpath(__FILE__)));
    Init::define('APP',         ROOT . DS . 'app');
    Init::define('LIB',         ROOT . DS . 'lib');
    Init::define('THIRD_PARTY', ROOT . DS . 'third_party');
    Init::define('TMP',         ROOT . DS . 'tmp');
    Init::define('LOGS',        TMP . DS . 'logs');
    Init::define('VIEWS',       APP . DS . 'View');
    Init::define('LAYOUTS',     VIEWS . DS . 'layouts');
    
    Init::setIncludePath( array(APP, LIB, THIRD_PARTY) );
    
    Init::define('CR',   "\r");
    Init::define('LF',   "\n");   
    Init::define('CRLF', CR . LF);
    
    Init::setLocale('ru_RU.UTF8');
    Init::setTimezone('Europe/Moscow');
    
    require_once 'Zend/Loader/Autoloader.php';
    Zend_Loader_Autoloader::getInstance()
        ->setFallbackAutoloader(true)
        ->suppressNotFoundWarnings(false);
    
    $config = require_once 'config.php';
    
    switch ($config['mode']) {
        case 'debug':
            Init::setErrorReporting(E_ALL | E_STRICT);
            Init::displayErrors(true);
            Init::logErrors(false);
            
            Init::setupErrorHandler();
            Init::enableAssertions();
            break;
            
        case 'production':
            Init::logErrors(true);
            Init::setErrorLog(LOGS . DS . 'php_error_log.txt');
            
            Init::setErrorReporting(E_ALL ^ E_NOTICE);
            Init::displayErrors(false);
            
            /**
            * @todo Устанавливать user-friendly перехватчик ошибок?
            */
            break;
    }
        
    Resources::create($config);
    
?>