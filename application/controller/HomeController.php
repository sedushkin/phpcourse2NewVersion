<?php
namespace application\controller;

class HomeController extends Controller {

    /**
     * 1 этап: вызываем site.com/home/index преобразуем в ?path=home/index
     * 2 этап: App::dispatcher преобразует это в controller = HomeController и action = action_index
     */

    public function action_index() {
        $this->view->render("index");
    }

/**
     * 1 этап: вызываем site.com/home/contact преобразуем в ?path=home/contact
     * 2 этап: App::dispatcher преобразует это в controller = HomeController и action = action_contact
     */

    public function action_contact() {
        if ($this->request->isPost()) {
             /*
        $name = App::request()->post("name");
        $email = App::request()->post("email");\
        $message = App::request()->post("message");
        */
        $name = $this->request()->post("name");
        $email = $this->request()->post("email");
        $message = $this->request()->post("message");

        AppEmail::send(
            $name,
            $email,
            $message
        );
        $result = "Ваше сообщение отправлено";
        }
        $this->view->render("contact", ["result"=>$result]);
    }
    
}