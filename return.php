<?php

session_start();

require_once('auth_check.php'); // проверка авторизации пользователя

require_once('db_connect.php');

require_once('userinfo.php');

?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <title>Вернуть ведомость</title>

    <link href="navbar.css" rel="stylesheet">

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
			  <li class="active"><a href="return.php">Вернуть ведомость</a></li>
              <li><a href="adduser.php">Доб. нов. пользователя *</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

      <!-- Main component for a primary marketing message or call to action -->
	  
	<div role="tabpanel">
	<h3>Вернуть ведомость<?php if($userinfo['type'] == 1) echo "<br><small>Выберите способ возврата заполненной ведомости</small>" ?></h3>
	
	<?php if($userinfo['type'] == 2): ?>
	<h4 class="alert alert-danger">У студентов нет доступа к этой странице.</h4>
	<?php endif; ?>
	
	<?php if($userinfo['type'] == 1): ?>
	
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
		<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Вернуть Excel-файл</a></li>
		<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Вернуть скан распечатки ведомости</a></li>
	</ul>

	
	<div class="tab-content">
		<!-- первая вкладка. Вернуть Excel-файл -->
		<div role="tabpanel" class="tab-pane active" id="home" style="margin-top: 20px;">
			<form class="form-horizontal" enctype="multipart/form-data" id="returnexcel" role="form" data-toggle="validator" action="return_excel.php" method="post" style="margin-top: 20px;">
				<div class="form-group">
					<label for="lesson" class="col-sm-2 control-label">Укажите файл</label>
					<div class="col-sm-10" style="padding-top: 5px">
						<input type="file" id="excelfile" name="excelfile" required>
						<p class="help-block">Вы также можете перетащить файл в это поле.</p>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Проверить и сохранить</button>
					</div>
				</div>
			</form>
		</div>
		<!-- вторая вкладка. Вернуть скан распечатки ведомости -->
		<div role="tabpanel" class="tab-pane" id="profile" style="margin-top: 20px;">
			<a href="return_scan.php">Версия в разработке</a>.
		</div>
	</div>
	<?php endif; ?>
	
	<hr>
	<?php
	if($userinfo['type'] == 2) {
		echo "<p class=\"text-muted\">".$student['last_name']." ".$student['first_name']." ".$student['middle_name']."<br>";
		echo "<small>Студент группы <b>".$student_group['name']."</b>, факультета <b>".$student_faculty['fullname']."</b>, кафедры <b>".$student_chair['fullname']."</b></small></p>";
	} elseif($userinfo['type'] == 1) {
		echo "<p class=\"text-muted\">".$teacher['last_name']." ".$teacher['first_name']." ".$teacher['middle_name']."<br>";
		echo "<small>Преподаватель кафедры <b>".$teacher_chair['fullname']."</b>, факультета <b>".$teacher_faculty['fullname']."</b></small></p>";
	}
	?>
	<p class="text-muted"><small>* только для администратора системы.</small></p>
	<p><a href="logout.php">Выход из системы</a></p>
    </div> <!-- /container -->
	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="validator.js"></script>
  </body>
</html>