<?php

class Controller
{
    private $model;//объект модель
    private $view;//объект вид
    private $pageNumber;//номер запрошенной страницы
    private $action;//чтение или запись

    public function __construct() {//конструктор определяет номер запрошенной страницы
        $this->pageNumber = isset($_GET['number'])?intval($_GET['number']):1;
        if (!empty($_POST)) $this->action = 'write'; else $this->action = 'read';//определяет действие с данными
    }
    public function run() {
        try {
            $this->model = new Model($this->action);//создаем новую модель
            $this->model->setMessagesPerPage(MESSAGES_PER_PAGE);//передаем ей количество сообщений на странице
            $this->model->run($this->pageNumber);//запускаем, она записывает или извлекает нужные данные

            $this->view = new View($this->pageNumber, $this->model->getPagesAmount(), PAGINATION_INDENT);//создаем новый объект View
            $this->view->render($this->model->getPageMessages(), $this->model->getFormData(), $this->model->getError());//выводим страницу
        }
        catch(Exception $e) {
            echo '<div class="alert alert-danger" role="alert">ОШИБКА<br/>' . $e->getMessage() . '</div>';//если возникла ошибка, выводим сообщение
        }
    }

}

?>