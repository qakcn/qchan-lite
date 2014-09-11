<?php
class Service {

    static private $instance = null;
    
    private function __construct() {}
    
    static function init() {
        require ABSPATH.'/includes/service/ServiceInterface.php';
        switch(SERVICE) {
        case 'tietuku':
            require_once ABSPATH.'/includes/service/TieTuKu.class.php';
            require_once ABSPATH.'/includes/service/MyTieTuKu.class.php';
            require_once ABSPATH.'/includes/service/TTKService.class.php';
            self::$instance = new TTKService(TIETUKU_ACCESSKEY, TIETUKU_SECRETKEY);
            break;
        default:
            exit('No such service! Check SERVICE in your config.php!');
        }
    }

    static function srv() {
        return self::$instance;
    }


}