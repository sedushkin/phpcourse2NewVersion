<?php
namespace action\controller\base;

use \application\service\Application as App;


abstract class Controller {
    protected
        $view,
        $config,
        $request,
        $logger;

    public function __construct() {
        $this->view = App::view();
        $this->config = App::config();
        $this->request = App::request();
        $this->logger = App::logger();
    }
}