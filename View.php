<?php

class View
{
    private $pageMessages;//массив сообщений для вывода
    private $pageNumber;//номер запрошенной страницы
    private $pagesAmount;//количество страниц
    private $paginationIndent;//количество номеров слева и справа от текущей страницы в пагинаторе
    private $formData;//данные для формы, чтобы отобразить повторно при некорректном вводе
    private $error;//сообщение обшибке при некорректных данных в форме

    public function __construct($pageNumber, $pagesAmount, $paginationIndent) {//в аргументах конструктора передаются настройки пагинатора
        $this->pageMessages = array();//инициализация пустым массивом
        $this->pageNumber = $pageNumber;
        $this->pagesAmount = $pagesAmount;
        $this->paginationIndent = $paginationIndent;
    }

    public function render($pageMessages, $formData, $error) {//выводит страницу, массив данных передается аргументом
        $this->pageMessages = $pageMessages;
        $this->formData = $formData;
        $this->error = $error;
        $this->renderHeader();//вызываем функции, выводящие блоки страниц
        $this->renderMessages();
        $this->renderPagination();
        $this->renderForm();
        $this->renderFooter();
    }

    private function renderHeader() {//private, так как вызывается только из этого класса функцией render
        echo ' 
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Страница ' . $this->pageNumber .'</title>

	<!-- Bootstrap -->
	<link href="public/css/bootstrap.min.css" rel="stylesheet">
	<link href="public/css/user_comment.css" rel="stylesheet">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesnt work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<div class="navbar navbar-inverse navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="page_1">Гостевая книга</a>
			</div>
		</div>
	</div> 
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h3 class="page-header">Сообщения пользователей</h3>
			</div>
		</div>
	</div>';
    }

    private function renderMessages() {//вывод сообщений
        foreach ($this->pageMessages as $message) {
			echo '
<div class="container">
	<div class="row">
		<div class="col-md-1">
			<div class="thumbnail">
				<img class="img-responsive user-photo" src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png">
			</div>
		</div>
		<div class="col-md-11">
			<div class="panel panel-default">
				<div class="panel-heading">
					<strong>Пользователь ' . $message['author'] . '&nbsp;</strong> <span class="text-muted">написал ' . $message['message_time'] . '</span>
				</div>
				<div class="panel-body">' . $message['message'] . '</div> 
			<div class="panel panel-success">
				<div class="panel-heading">
					<h4 class="panel-title">
					<a href="#collapse-'.$message['id_message'].'" data-toggle="collapse">Открыть(скрыть) изображеие</a>
					</h4>
				</div>
				<div id="collapse-'.$message['id_message'].'" class="panel-collapse collapse off">
					<div class="panel-body">
						<p><img src="image/'.$message['filename'].'" alt="Добавьте пожалуйста весёлую картинку :)" width="200" height="200"></p>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>';
        }
    }

    private function renderPagination() {//вывод пагинации
        if ($this->pagesAmount<2) return;
        $start = $this->pageNumber - $this->paginationIndent;
        if ($start<1) $start = 1;
        $finish = $this->pageNumber + $this->paginationIndent;
        if ($finish>$this->pagesAmount) $finish = $this->pagesAmount;
        for ($i = $start; $i<=$finish; $i++) {
            if ($i==$this->pageNumber)
                echo '	
		<ul class="pagination">
			<li class="active"><a>' . $i . '<span class="sr-only">(current)</span></a></li>
		</ul>&nbsp;';
            else
                echo '
		<ul class="pagination">
			<li><a href="page_' . $i . '">' . $i . '<span class="sr-only">(current)</span></a></li>
		</ul>&nbsp;';
        }
    }

    private function renderFooter() {
        echo '		
		<!-- jQuery (necessary for Bootstraps JavaScript plugins) -->		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>		
		<!-- Include all compiled plugins (below), or include individual files as needed -->	
		<script src="public/js/bootstrap.min.js"></script>
		<script src="public/js/message_box_with_counter.js"></script>
	</body>
	</html>	';
    }

    private function renderForm() {
        if ($this->error)//если были ошибки, выводим сообщение
            echo '<div class="alert alert-danger" role="alert">ПРИ ВВОДЕ ДОПУЩЕНЫ ОШИБКИ:<br/>' . $this->error . '</div>';
        echo '
<div class="container">
    <div class="row">
		<div class="col-sm-4 col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
					<form enctype="multipart/form-data" accept-charset="UTF-8" action="index.php" method="POST">
						Ваше имя<br/><input class="form-control" type="text" name="имя" size="20" maxlength="25" placeholder="Введите имя" pattern="^^[а-яА-ЯёЁa-zA-Z0-9]+$" required value="' . $this->formData["имя"] . '"><br/>
						Ваше сообщение<br/><textarea class="form-control counted" name="сообщение" placeholder="Введите сообщение" cols="50" rows="10" style="margin-bottom:10px;">' . $this->formData["сообщение"] . '</textarea><br/>
						<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
						Добавить файл<br/><input type="file" name="loadfile" size="50"><br/>
						<img id="captcha_img" src="captcha/captcha.php?t='.time().'" style="border: 1px solid black"/><br/>
						<p><a href="javascript:void(0)" onclick="getCaptcha()">Не вижу символы</a></p>
						<p><input type="text" name="captcha"/>
                        <h6 class="pull-right" id="counter">Осталось 200 символов</h6>
                        <button class="btn btn-info" type="submit" name="upload">Новое сообщение</button>
                    </form>
                </div>
            </div>
        </div>
	</div>
</div>';
    }
}

?>