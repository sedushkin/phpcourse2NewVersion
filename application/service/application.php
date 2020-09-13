<?php
namespace application\service;

use \application\service\View;
use \application\service\Config;
use \application\service\Request;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

class Application {
    
    public static $app = null;

    private
        $view,
        $config,
        $request,
        $logger;

    /* Композиция */
    
    private function __construct() {
        $this->view = new View();
        $this->config = new Config();
        $this->request = new Request();
        $this->logger = new Logger('common');
        $this->logger->pushHandler(new StreamHandler(BASE_PATH.'logs/common.log', Logger::WARNING));
    }

    private function __clone(){

    }
/* 
Проверяем на наличие запущенного приложения $app, если $app=null, то создаем новый 
$app типа Application (проверка на единственность запущенного приложения)
Singletone
*/
    public static function get() {
        if (is_null(self::$app)) {
            self::$app = new Application();
        }
        return self::$app;

    }

    /**
     * Для рендера наших темплейтов
     * $this->view->render("home/index", []);
     */
    public function view(){
        return $this->view;
    }

    /**
     * Для получения данных из конфигурации,
     * данные для подключения к БД
     * $this->config->get("db");
     */
    public function config(){
        return $this->config();
    }

    /*
     * Для получения $_GET и $_POST данных
     * $this->request->get("path");  то же самое, что -> $_GET["path"]
     * Application::get()->request->get("path");  то же самое, что -> $_GET["path"]
     * $this->request->post("login"); то же самое, что -> $_POST["login"]
    */
    public function request(){
        return $this->request;
    }

    public function logger(){
        return $this->logger;
    }

    /**
     * Необходим для поиска нужного контроллера и экшена
     * /?path=home/index
	 * controller = home
	 * action = index
     */
    public function dispatch() {
        try {
            if ($_SERVER['REQUEST_URI'] == "/") {
				$this->request->set("path", "home/index");
			}
            if (!$this->request->data("path")) {
                throw new Exception("Неверный формат url".$_SERVER['REQUEST_URI']);
            }
            
            list(
                $controller,
                $action
            ) = explode("/", $this->request->get("path"));
            
            $class = '\\application\controller\\'.ucfirst($controller)."Controller";
            if (!class_exists($class)) {
                throw new Exception("Класс ".$class." не существует");
            }
            $controller = new $class;
            if (!method_exists($controller, "action_".$action)) {
                throw new Exception("Экшена ".$action." не существует");
            }

            $result = $controller->{"action_".$action}();

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->logger->render("error500");
        }
    }
    
}