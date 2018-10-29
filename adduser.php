<?php

/*** begin our session ***/
session_start();

require_once('db_connect.php');

/*** set a form token ***/
$form_token = md5( uniqid('auth', true) );

/*** set the session form token ***/
$_SESSION['form_token'] = $form_token;
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <title>Добавление нового пользователя</title>

    <link href="navbar.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <!-- Static navbar -->
      <div class="navbar navbar-inverse" role="navigation">
        <div class="container-fluid">
          <div class="navbar-header">
            <span class="navbar-brand">Система контроля текущей успеваемости</span>
          </div>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="index.php">Запрос информации</a></li>
              <li><a href="get.php">Получить ведомость</a></li>
			  <li><a href="return.php">Вернуть ведомость</a></li>
              <li class="active"><a href="#">Доб. нов. пользователя *</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

      <!-- Main component for a primary marketing message or call to action -->
	  
	<div role="tabpanel">
	<h3>Добавление нового пользователя<br><small>Студент или преподаватель должен присутствовать в базе данных</small></h3>
	
	<!-- панель уведомлений -->
	<?php if(isset($_GET['error'])): ?>
	<div class="alert alert-danger">
        <strong><?php echo $_GET['error'] ?></strong>
    </div>
	<?php endif; ?>
	
	<?php if(isset($_GET['success'])): ?>
	<div class="alert alert-success">
        <strong><?php echo $_GET['success'] ?></strong>
    </div>
	<?php endif; ?>
	
	<!-- вкладки -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Студент</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Преподаватель</a></li>
	</ul>

	
	<div class="tab-content">
		<!-- первая вкладка. студент -->
		<div role="tabpanel" class="tab-pane active" id="home" style="margin-top: 20px;">
			<form class="form-horizontal" id="student-form" role="form" data-toggle="validator" action="adduser_submit.php" method="post">
			  <input type="hidden" name="usertype" value="2"><!-- 1(teacher), 2(student) -->
			  <div class="form-group">
				<label for="student-username" class="col-sm-2 control-label">Логин</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="student-username" data-minlength="4" maxlength="20" name="student-username" placeholder="Придумайте логин (минимум 4 символа, латинские буквы, цифры, черточка, подчеркивание)" pattern="^([-_A-z0-9]){4,20}$" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="student-password" class="col-sm-2 control-label">Пароль</label>
				<div class="col-sm-10">
				  <input type="password" class="form-control" id="student-password" data-minlength="6" maxlength="40" name="student-password" placeholder="Придумайте пароль (минимум 6 символов, латинские буквы, цифры, черточка, подчеркивание)" pattern="^([-_A-z0-9]){6,40}$" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="student-password-2" class="col-sm-2 control-label"></label>
				<div class="col-sm-10">
				  <input type="password" class="form-control" id="student-password-2" data-minlength="6" maxlength="40" placeholder="Введите пароль ещё раз" required data-match="#student-password" pattern="^([-_A-z0-9]){6,40}$">
				</div>
			  </div>
			  
			  <div class="form-group">
				<label for="student-lastname" class="col-sm-2 control-label">Фамилия</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="student-lastname" name="student-lastname" placeholder="Обязательно, с заглавной буквы" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="student-firstname" class="col-sm-2 control-label">Имя</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="student-firstname" name="student-firstname" placeholder="Обязательно, с заглавной буквы" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="student-group" class="col-sm-2 control-label">Группа</label>
				<div class="col-sm-10">
					<select class="form-control" id="student-group" name="student-group" required>
						<?php
						
							$stmt = $conn->query('SELECT name FROM groups');
							while ($row = $stmt->fetch()) {
								echo "<option>".$row['name']."</option>";
							}
						
							$conn = null;
						?>
					</select>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
				  <button type="submit" class="btn btn-default">Проверить и сохранить</button>
				</div>
			  </div>
			</form>
			
		</div>
		<!-- вторая вкладка. преподаватель -->
		<div role="tabpanel" class="tab-pane" id="profile" style="margin-top: 20px;">
			<form class="form-horizontal" id="teacher-form" role="form" data-toggle="validator" action="adduser_submit2.php" method="post">
			  <input type="hidden" name="usertype" value="1"><!-- 1(teacher), 2(student) -->
			  <div class="form-group">
				<label for="teacher-username" class="col-sm-2 control-label">Логин</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="teacher-username" data-minlength="4" maxlength="20" name="teacher-username" placeholder="Придумайте логин (минимум 4 символа, латинские буквы, цифры, черточка, подчеркивание)" pattern="^([-_A-z0-9]){4,20}$" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="teacher-password" class="col-sm-2 control-label">Пароль</label>
				<div class="col-sm-10">
				  <input type="password" class="form-control" id="teacher-password" data-minlength="6" maxlength="40" name="teacher-password" placeholder="Придумайте пароль (минимум 6 символов, латинские буквы, цифры, черточка, подчеркивание)" pattern="^([-_A-z0-9]){6,40}$" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="teacher-password-2" class="col-sm-2 control-label"></label>
				<div class="col-sm-10">
				  <input type="password" class="form-control" id="teacher-password-2" data-minlength="6" maxlength="40" placeholder="Введите пароль ещё раз" required data-match="#teacher-password" pattern="^([-_A-z0-9]){6,40}$">
				</div>
			  </div>
			  
			  <div class="form-group">
				<label for="teacher-lastname" class="col-sm-2 control-label">Фамилия</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="teacher-lastname" name="teacher-lastname" placeholder="Обязательно, с заглавной буквы" required>
				</div>
			  </div>
			  <div class="form-group">
				<label for="teacher-firstname" class="col-sm-2 control-label">Имя</label>
				<div class="col-sm-10">
				  <input type="text" class="form-control" id="teacher-firstname" name="teacher-firstname" placeholder="Обязательно, с заглавной буквы" required>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
				  <button type="submit" class="btn btn-default">Проверить и сохранить</button>
				</div>
			  </div>
			</form>
		</div>
	</div>

	</div>
	<hr>
	<small>* только для администратора системы.</small>
    </div> <!-- /container -->
	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="validator.js"></script>
  </body>
</html>