<?php
namespace iDeliveryFood\controllers;

class BaseController {
    protected $twig;
            
    function __construct() {
        $loader = new \Twig_Loader_Filesystem('views');
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => false,
        ));
    }
}

