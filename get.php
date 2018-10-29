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

    <title>Получить ведомость</title>

    <link href="navbar.css" rel="stylesheet">
	<link id="bsdp-css" href="datepicker/css/bootstrap-datepicker3.css" rel="stylesheet">

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
              <li class="active"><a href="get.php">Получить ведомость</a></li>
			  <li><a href="return.php">Вернуть ведомость</a></li>
              <li><a href="adduser.php">Доб. нов. пользователя *</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

      <!-- Main component for a primary marketing message or call to action -->
	  
	<div role="tabpanel">
	<h3>Получить ведомость</h3>
	
	<?php if($userinfo['type'] == 2): ?>
	<h4 class="alert alert-danger">У студентов нет доступа к этой странице.</h4>
	<?php endif; ?>
	
	<?php if($userinfo['type'] == 1): ?>
	
	<form class="form-horizontal" id="get-form" role="form" data-toggle="validator" action="get_submit.php" method="post" style="margin-top: 20px;">
	  <div class="form-group">
		<label for="lesson" class="col-sm-2 control-label">Выберите занятие</label>
		<div class="col-sm-10">
			<select class="form-control" id="lesson" name="lesson" required>
				<?php
					
					$stmt = $conn->prepare("SELECT id, discipline, `group` FROM lessons WHERE teacher = :teacher_id");
					$stmt->bindParam(':teacher_id', $userinfo['teacher_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					while ($lessons = $stmt->fetch()) {
						$stmt2 = $conn->prepare("SELECT name FROM disciplines WHERE id = :discipline_id");
						$stmt2->bindParam(':discipline_id', $lessons['discipline'], PDO::PARAM_INT);
						$stmt2->execute();
						$discipline = $stmt2->fetch();
						
						$stmt3 = $conn->prepare("SELECT name FROM groups WHERE id = :group_id");
						$stmt3->bindParam(':group_id', $lessons['group'], PDO::PARAM_INT);
						$stmt3->execute();
						$group = $stmt3->fetch();
						
						echo "<option value=\"".$lessons['id']."\">".$discipline['name']." для группы ".$group['name']."</option>";
					}
					
				?>
			</select>
		</div>
	  </div>
	  <div class="form-group date">
		<label for="lesson-date" class="col-sm-2 control-label">Дата занятия</label>
		<div class="col-sm-10">
			<input type="text" type="text" class="form-control" id="lesson-date" name="lesson-date" pattern="^([0-9]){4}-([0-9]){2}-([0-9]){2}$" required>
			<span class="help-block">Дата проведения будущего занятия</span>
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default">Проверить и получить</button>
		</div>
	  </div>
	</form>
	
	<div class="panel panel-info">
      <div class="panel-heading">
        <h3 class="panel-title">Заполнение ведомости</h3>
      </div>
      <div class="panel-body">
        Сохраните файл ведомости у себя на компьютере, планшете или телефоне. В редакторе электронных таблиц отметьте <b>любым прописным символом</b> нужную оценку студенту напротив его имени, заполнив <b>только одну</b> из пяти ячеек (<i>Пропустил, Не справился, Удв., Хор., Отл.</i>).
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
    <script src="jquery-1.11.2.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="validator.js"></script>
	<script src="datepicker/js/bootstrap-datepicker.js"></script>
	<script src="datepicker/locales/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
	<script>
	$('#lesson-date').datepicker({
		format: "yyyy-mm-dd",
		language: "ru",
		keyboardNavigation: false,
		orientation: "top auto",
		startDate: "today",
		todayHighlight: true
	});
	</script>
  </body>
</html>