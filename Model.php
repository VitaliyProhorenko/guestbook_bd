<?php

class Model
{
    private $messagesArray;//массив сообщений
    private $messagesAmount;//общее количество сообщений
    private $messagesPerPage;//количество сообщений на одной странице
    private $pagesAmount;//количество страниц
    private $pageMessages;//массив сообщений для запрошенной страницы
    private $pageNumber;//номер запрошенной страницы
    private $action;//действие read или write
    private $formData;//данные для формы
    private $error;//сообщение об ошибке при некорректных данных в форме

    public function __construct($action) {//устанавливаем для всех свойств значения по умолчанию, чтобы объект был полностью инициализирован
        $this->messagesArray = array();
        $this->messagesAmount = 0;
        $this->action = $action;
        $this->messagesPerPage = 1;
        $this->pagesAmount = 1;
        $this->pageMessages = array();
        $this->pageNumber = 1;
        $this->formData['имя'] = '';
        $this->formData['сообщение'] = '';
        $this->error = '';
    }

    public function setMessagesPerPage($messagesPerPage) {//установка количества сообщений на странице
        $this->messagesPerPage = $messagesPerPage;
    }

    public function run($pageNumber = 1) {//выполняет все действия, в аргументе передается номер запрошенной страницы
        $this->pageNumber = $pageNumber;//записываем полученный в параметре номер страницы в свойство
        $this->{$this->action}();//вызываем метод по имени, хранящемся в $this->action,т.е. read или write
    }

    public function getPageMessages() {//функция для получения сообщений страницы для View
        return $this->pageMessages;
    }

    public function getPagesAmount() {//функция для получения количества страниц тоже для передачи View
        return $this->pagesAmount;
    }

    public function getFormData() {//получение данных для формы, если они были введены с ошибкой, для повторного вывода
        return $this->formData;
    }

    public function getError() {//получим сообщение при ошибке во введенных в форму данных
        return $this->error;
    }

    private function read() {
        try {
            $pdo = new PDO(DB_DSN_MYSQL, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $query = "SELECT count(*) FROM messages";
            $result = $pdo->query($query, PDO::FETCH_NUM);//выполняет запрос
            $row = $result->fetch();//извлекаем строку
            $this->messagesAmount = $row[0];//в $row[0] полученное количество строк
            $result->closeCursor();//закрываем набор данных
            //var_dump($this->messagesAmount);
            $this->pagesAmount = ceil($this->messagesAmount / $this->messagesPerPage);//определяем количество страниц
            if ($this->pageNumber < 1 || $this->pageNumber > $this->pagesAmount) {//если номер страницы вне диапазона существующих страниц
                throw new Exception('<div class="alert alert-danger" role="alert">Запрошенная страница не существует<br/></div>');//генерируем исключение, оно перехватится в контроллере
            }
            $messageOffset = ($this->pageNumber - 1) * $this->messagesPerPage;//номер первого сообщения на странице
            $query = "SELECT * FROM messages ORDER BY id_message DESC LIMIT " . $messageOffset . ", " . $this->messagesPerPage;
            //var_dump($query);
            $result = $pdo->query($query, PDO::FETCH_ASSOC);
            $this->pageMessages = $result->fetchAll();
            //var_dump($this->pageMessages);
            $pdo = NULL;
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger" role="alert">ОШИБКА<br/>' . $e->getMessage() . '</div>';
        };
    }

    private function write() {
        $this->validate('имя', array(2, 20));
        $this->validate('сообщение', array(1, 200));
        if (isset($_POST["captcha"])) {
            if ($_POST["captcha"] != $_SESSION["captcha"]) {
                $this->error .= '<div class="alert alert-danger" role="alert">Капча введена неправильно, попробуйте еще раз<br/></div>';
            }
        }
        if ($this->error) return;//если были ошибки при проверке выходим без записи в бд
        $user = $this->formData['имя'];
        $message = $this->formData['сообщение'];
        try {
            $pdo = new PDO(DB_DSN_MYSQL, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $exec = "INSERT into messages set author='$user', message='$message'";
            $result = $pdo->exec($exec);
			if (isset($_FILES['loadfile'])) {
				if ($_FILES['loadfile']['error'] == 0) {
					$extension = strrchr($_FILES['loadfile']['name'], '.');//расширение файла
					$id = $pdo->lastInsertId();
					$filename = $id . $extension;//имя файла формируется по id с добавлением расширения
					if (move_uploaded_file($_FILES['loadfile']['tmp_name'], 'image/' . $filename)) {//файл хранится в папке image
						$exec = "UPDATE messages set filename='$filename' WHERE id_message=$id";
						$result = $pdo->exec($exec);
                    }
                }
            }
            $pdo = NULL;
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger" role="alert">ОШИБКА<br/>' . $e->getMessage() . '</div>';
        };
        header('Location: http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);//перезапрос страницы после успешной записи
        exit();
    }

    private function validate($key, array $length) {//$key - ключ массива $_GET, $length - допустимая длина [min, max]
        if (!empty($_POST[$key])) $this->formData[$key] = $_POST[$key];
        $this->formData[$key] = trim($this->formData[$key]);
        $len = mb_strlen($this->formData[$key]);
        if ($len < $length[0] | $len > $length[1])
			$this->error .= '<div class="alert alert-danger" role="alert">Длина текста в поле ' . $key . ' должна быть от ' . $length[0] . ' до ' . $length[1] . ' символов<br/></div>';
        $this->formData[$key] = str_replace("\t", ' ', $this->formData[$key]);
        $this->formData[$key] = str_replace(array("\r\n", "\n\r", "\r", "\n"), '<br/>', $this->formData[$key]);
        $this->formData[$key] = htmlspecialchars($this->formData[$key], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
?>