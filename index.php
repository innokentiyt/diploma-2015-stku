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

    <title>Запрос информации</title>

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
              <li class="active"><a href="index.php">Запрос информации</a></li>
              <li><a href="get.php">Получить ведомость</a></li>
			  <li><a href="return.php">Вернуть ведомость</a></li>
              <li><a href="adduser.php">Доб. нов. пользователя *</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </div>

      <!-- Main component for a primary marketing message or call to action -->
	  
	<div role="tabpanel">
	
	<?php if($userinfo['type'] == 2): ?>
	<h3>Запрос информации<br><small>Выберите дисциплину, по которой вы хотите узнать свою успеваемость</small></h3>
	
	<form class="form-horizontal" id="student-form" role="form" data-toggle="validator" method="get" style="margin-top: 20px;">
	  <div class="form-group">
		<label for="lesson" class="col-sm-2 control-label">Дисциплина</label>
		<div class="col-sm-10">
			<select class="form-control" id="lesson" name="lesson" required>
				<?php
					
					$stmt = $conn->prepare("SELECT DISTINCT lesson FROM registry WHERE student_id = :student_id");
					$stmt->bindValue(':student_id', $userinfo['student_id'], PDO::PARAM_INT);
					$stmt->execute();
					
					while ($lesson_id = $stmt->fetchColumn() ) {
						$stmt2 = $conn->prepare("SELECT discipline, teacher FROM lessons WHERE id = :lesson_id");
						$stmt2->bindValue(':lesson_id', $lesson_id, PDO::PARAM_INT);
						$stmt2->execute();
						$lesson = $stmt2->fetch();
						
						$stmt3 = $conn->prepare("SELECT name, fullname FROM disciplines WHERE id = :discipline_id");
						$stmt3->bindValue(':discipline_id', $lesson['discipline'], PDO::PARAM_INT);
						$stmt3->execute();
						$discipline = $stmt3->fetch();
						
						$stmt4 = $conn->prepare("SELECT last_name, first_name, middle_name FROM teachers WHERE id = :teacher_id");
						$stmt4->bindValue(':teacher_id', $lesson['teacher'], PDO::PARAM_INT);
						$stmt4->execute();
						$teacher = $stmt4->fetch();
						
						$teacher_short = $teacher['last_name']." ".substr($teacher['first_name'], 0, 2).". ".substr($teacher['middle_name'], 0, 2)."."; // Фамилия И. О.
						
						echo "<option value=\"".$lesson_id."\">".$discipline['name']." (".$discipline['fullname'].", ".$teacher_short.")</option>";
					}
					
				?>
			</select>
		</div>
	  </div>
	  <div class="form-group date">
		<label for="lesson-date" class="col-sm-2 control-label">Дата занятия</label>
		<div class="col-sm-10">
			<input type="text" type="text" class="form-control" id="lesson-date" name="lesson-date" pattern="^([0-9]){4}-([0-9]){2}-([0-9]){2}$" placeholder="Необязательно">
			<span class="help-block">Дата проведённого занятия. Оставьте строку пустой, чтобы получить результаты за всё время.</span>
		</div>
	  </div>
	  <div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
		  <button type="submit" class="btn btn-default">Показать</button>
		</div>
	  </div>
	</form>
	
	<?php endif; ?>
	<?php
	if($userinfo['type'] == 2) {
		if(!isset($_GET['lesson'], $_GET['lesson-date']) ) { // ничего не заполнено
			// пусто
		} elseif(empty($_GET['lesson-date'])) { // не заполнено только поле даты
			echo "<h4>".$discipline['name'].' <small>('.$discipline['fullname'].", ".$teacher_short.")</small></h4>";
			echo '<table class="table table-hover">
						  <thead>
							<tr>
							  <th>№</th>
							  <th>Дата</th>
							  <th>Оценка</th>
							</tr>
						  </thead>
						  <tbody>';
			$stmt5 = $conn->prepare("SELECT date, evaluation FROM registry WHERE student_id = :student_id ORDER BY date DESC");
			$stmt5->bindValue(':student_id', $userinfo['student_id'], PDO::PARAM_INT);
			$stmt5->execute();
			$i = 1;
			while($results = $stmt5->fetch() ) {
				if ($results['evaluation'] == "1") $evaluation = "пропустил";
				if ($results['evaluation'] == "2") $evaluation = "не справился";
				if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
				if ($results['evaluation'] == "4") $evaluation = "хорошо";
				if ($results['evaluation'] == "5") $evaluation = "отлично";
				echo '	<tr>
							<th scope="row">'.$i.'</th>
							<td>'.$results['date'].'</td>
							<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
						</tr>';
				$i++;
			}
			echo '</tbody>
				</table>';
		} else { // всё заполнено
			
			$stmt5 = $conn->prepare("SELECT evaluation FROM registry WHERE student_id = :student_id AND date = :date");
			$stmt5->bindValue(':student_id', $userinfo['student_id'], PDO::PARAM_INT);
			$stmt5->bindValue(':date', $_GET['lesson-date'], PDO::PARAM_INT);
			$stmt5->execute();
			$results = $stmt5->fetch();
			if($results['evaluation'] == NULL) {
				echo '<div class="alert alert-danger">
						<strong>Нет оценки по этой дате.</strong>
					</div>';
			} else {
				if ($results['evaluation'] == "1") $evaluation = "пропустил";
				if ($results['evaluation'] == "2") $evaluation = "не справился";
				if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
				if ($results['evaluation'] == "4") $evaluation = "хорошо";
				if ($results['evaluation'] == "5") $evaluation = "отлично";
				echo "<h4>".$discipline['name'].' <small>('.$discipline['fullname'].", ".$teacher_short.")</small></h4>";
				echo '<table class="table table-hover">
							  <thead>
								<tr>
								  <th>№</th>
								  <th>Дата</th>
								  <th>Оценка</th>
								</tr>
							  </thead>
							  <tbody>';
				echo '	<tr>
							<th scope="row">1</th>
							<td>'.$_GET['lesson-date'].'</td>
							<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
						</tr>';
				$i++;
				echo '</tbody>
					</table>';
			}	
		}
	}

	?>
	
	<?php if($userinfo['type'] == 1): ?>
	<h3>Запрос информации<br><small>Выберите вариант получения информации</small></h3>
	
	<!-- вкладки -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" <?php if($_GET['tab'] == 1 || empty($_GET['tab']) ) echo 'class="active"' ?>><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Дисциплина по группе</a></li>
		<li role="presentation" <?php if($_GET['tab'] == 2) echo 'class="active"' ?>><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Студент</a></li>
		<li role="presentation" <?php if($_GET['tab'] == 3) echo 'class="active"' ?>><a href="#profile2" aria-controls="profile2" role="tab" data-toggle="tab">Дата</a></li>
	</ul>

	
	<div class="tab-content">
		<!-- первая вкладка. поиск по студенту -->
		<div role="tabpanel" class="tab-pane <?php if($_GET['tab'] == 1 || empty($_GET['tab']) ) echo 'active' ?>" id="home" style="margin-top: 20px;">
			<form class="form-horizontal" id="teacher-form-group" role="form" data-toggle="validator" method="get">
				<input type="hidden" name="tab" value="1">
				<div class="form-group">
					<label for="lesson" class="col-sm-2 control-label">Дисциплина</label>
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
						<input type="text" type="text" class="form-control" id="lesson-date" name="lesson-date" pattern="^([0-9]){4}-([0-9]){2}-([0-9]){2}$" placeholder="Необязательно">
						<span class="help-block">Дата проведённого занятия. Оставьте строку пустой, чтобы получить результаты за всё время.</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Проверить и показать</button>
					</div>
				</div>
			</form>
			<?php
			if($userinfo['type'] == 1) {
				if($_GET['tab'] == 1) {
					if(!isset($_GET['lesson'], $_GET['lesson-date']) ) { // ничего не заполнено
						// пусто
					} elseif(empty($_GET['lesson-date']) ) {
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
							
							
						}
						
						$stmt4 = $conn->prepare("SELECT DISTINCT date FROM registry WHERE lesson = :lesson_id");
						$stmt4->bindParam(':lesson_id', $_GET['lesson'], PDO::PARAM_INT);
						$stmt4->execute();
						while($dates = $stmt4->fetchColumn() ) {
							echo "<h4>".$discipline['name']." для группы ".$group['name']." (".$dates.")"."</h4>";
							echo '<table class="table table-hover">
									  <thead>
										<tr>
										  <th>№</th>
										  <th>ФИО</th>
										  <th>Дата</th>
										  <th>Оценка</th>
										</tr>
									  </thead>
									  <tbody>';
							$stmt6 = $conn->prepare("SELECT student_id, evaluation FROM registry WHERE lesson = :lesson_id AND date = :date ORDER BY date DESC");
							$stmt6->bindValue(':lesson_id', $_GET['lesson'], PDO::PARAM_INT);
							$stmt6->bindValue(':date', $dates, PDO::PARAM_STR);
							$stmt6->execute();
							$i = 1;
							while($results = $stmt6->fetch() ) {
								$stmt7 = $conn->prepare("SELECT last_name, first_name, middle_name FROM students WHERE id = :student_id");
								$stmt7->bindValue(':student_id', $results['student_id'], PDO::PARAM_INT);
								$stmt7->execute();
								$student_name = $stmt7->fetch();
								$student_name = $student_name['last_name']." ".substr($student_name['first_name'], 0, 2).". ".substr($student_name['middle_name'], 0, 2).".";
								
								if ($results['evaluation'] == "1") $evaluation = "пропустил";
								if ($results['evaluation'] == "2") $evaluation = "не справился";
								if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
								if ($results['evaluation'] == "4") $evaluation = "хорошо";
								if ($results['evaluation'] == "5") $evaluation = "отлично";
								echo '	<tr>
											<th scope="row" width="30px">'.$i.'</th>
											<td>'.$student_name.'</td>
											<td width="200px">'.$dates.'</td>
											<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
										</tr>';
								$i++;
							}
							echo '</tbody>
								</table>';
						}
					} else {
						
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
							
							echo "<h4>".$discipline['name']." для группы ".$group['name']."</h4>";
						}
						
						echo '<table class="table table-hover">
								  <thead>
									<tr>
									  <th>№</th>
									  <th>ФИО</th>
									  <th>Дата</th>
									  <th>Оценка</th>
									</tr>
								  </thead>
								  <tbody>';
						$stmt6 = $conn->prepare("SELECT student_id, evaluation FROM registry WHERE lesson = :lesson_id AND date = :date ORDER BY date DESC");
						$stmt6->bindValue(':lesson_id', $_GET['lesson'], PDO::PARAM_INT);
						$stmt6->bindValue(':date', $_GET['lesson-date'], PDO::PARAM_STR);
						$stmt6->execute();
						$i = 1;
						while($results = $stmt6->fetch() ) {
							$stmt7 = $conn->prepare("SELECT last_name, first_name, middle_name FROM students WHERE id = :student_id");
							$stmt7->bindValue(':student_id', $results['student_id'], PDO::PARAM_INT);
							$stmt7->execute();
							$student_name = $stmt7->fetch();
							$student_name = $student_name['last_name']." ".substr($student_name['first_name'], 0, 2).". ".substr($student_name['middle_name'], 0, 2).".";
							
							if ($results['evaluation'] == "1") $evaluation = "пропустил";
							if ($results['evaluation'] == "2") $evaluation = "не справился";
							if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
							if ($results['evaluation'] == "4") $evaluation = "хорошо";
							if ($results['evaluation'] == "5") $evaluation = "отлично";
							echo '	<tr>
										<th scope="row">'.$i.'</th>
										<td>'.$student_name.'</td>
										<td>'.$_GET['lesson-date'].'</td>
										<td>'.$results['evaluation']." (".$evaluation.')</td>
									</tr>';
							$i++;
						}
						echo '</tbody>
							</table>';
					}
				} elseif($_GET['tab'] == 2) {
					
				} elseif($_GET['tab'] == 3) {
					
				}
			}

			?>
		</div>
		<!-- вторая вкладка. поиск по студенту -->
		<div role="tabpanel" class="tab-pane <?php if($_GET['tab'] == 2) echo 'active' ?>" id="profile" style="margin-top: 20px;">
			<form class="form-horizontal" id="teacher-form-group" role="form" data-toggle="validator" method="get">
				<input type="hidden" name="tab" value="2">
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
					<label for="lesson" class="col-sm-2 control-label">Дисциплина</label>
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
						<input type="text" type="text" class="form-control" id="lesson-date2" name="lesson-date" pattern="^([0-9]){4}-([0-9]){2}-([0-9]){2}$" placeholder="Необязательно">
						<span class="help-block">Дата проведённого занятия. Оставьте строку пустой, чтобы получить результаты за всё время.</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Проверить и показать</button>
					</div>
				</div>
			</form>
			<?php
			if($userinfo['type'] == 1) {
				if(!isset($_GET['lesson'], $_GET['lesson-date'], $_GET['student-lastname'], $_GET['student-firstname']) ) { // ничего не заполнено
					// пусто
				} elseif(isset($_GET['lesson'], $_GET['student-lastname'], $_GET['student-firstname']) && empty($_GET['lesson-date']) ) { // не заполнено только поле даты
					
					$stmt = $conn->prepare("SELECT discipline, `group` FROM lessons WHERE id = :lesson_id");
					$stmt->bindParam(':lesson_id', $_GET['lesson'], PDO::PARAM_INT);
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
					}
					
					echo "<h4>".$discipline['name']." для группы ".$group['name']."</h4>";
					echo '<table class="table table-hover">
								  <thead>
									<tr>
									  <th>№</th>
									  <th>ФИО</th>
									  <th>Дата</th>
									  <th>Оценка</th>
									</tr>
								  </thead>
								  <tbody>';
					$stmt5 = $conn->prepare("SELECT id, last_name, first_name, middle_name FROM students WHERE last_name = :last_name AND :first_name = first_name");
					$stmt5->bindValue(':last_name', $_GET['student-lastname'], PDO::PARAM_STR);
					$stmt5->bindValue(':first_name', $_GET['student-firstname'], PDO::PARAM_STR);
					$stmt5->execute();
					$student_name = $stmt5->fetch();
					
					$stmt5 = $conn->prepare("SELECT date, evaluation FROM registry WHERE student_id = :student_id ORDER BY date DESC");
					$stmt5->bindValue(':student_id', $student_name['id'], PDO::PARAM_INT);
					$stmt5->execute();
					$i = 1;
					while($results = $stmt5->fetch() ) {
						if ($results['evaluation'] == "1") $evaluation = "пропустил";
						if ($results['evaluation'] == "2") $evaluation = "не справился";
						if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
						if ($results['evaluation'] == "4") $evaluation = "хорошо";
						if ($results['evaluation'] == "5") $evaluation = "отлично";
						echo '	<tr>
									<th scope="row">'.$i.'</th>
									<td>'.$student_name['last_name']." ".substr($student_name['first_name'], 0, 2).". ".substr($student_name['middle_name'], 0, 2).".".'</td>
									<td>'.$results['date'].'</td>
									<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
								</tr>';
						$i++;
					}
					echo '</tbody>
						</table>';
				} else { // всё заполнено
					
					$stmt = $conn->prepare("SELECT discipline, `group` FROM lessons WHERE id = :lesson_id");
					$stmt->bindParam(':lesson_id', $_GET['lesson'], PDO::PARAM_INT);
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
					}
					
					echo "<h4>".$discipline['name']." для группы ".$group['name']."</h4>";
					echo '<table class="table table-hover">
								  <thead>
									<tr>
									  <th>№</th>
									  <th>Дата</th>
									  <th>Оценка</th>
									</tr>
								  </thead>
								  <tbody>';
					$stmt5 = $conn->prepare("SELECT id, last_name, first_name, middle_name FROM students WHERE last_name = :last_name AND :first_name = first_name");
					$stmt5->bindValue(':last_name', $_GET['student-lastname'], PDO::PARAM_STR);
					$stmt5->bindValue(':first_name', $_GET['student-firstname'], PDO::PARAM_STR);
					$stmt5->execute();
					$student_name = $stmt5->fetch();
					
					$stmt5 = $conn->prepare("SELECT evaluation FROM registry WHERE student_id = :student_id AND date = :date");
					$stmt5->bindValue(':student_id', $student_name['id'], PDO::PARAM_INT);
					$stmt5->bindValue(':date', $_GET['lesson-date'], PDO::PARAM_STR);
					$stmt5->execute();
					$i = 1;
					while($results = $stmt5->fetch() ) {
						if ($results['evaluation'] == "1") $evaluation = "пропустил";
						if ($results['evaluation'] == "2") $evaluation = "не справился";
						if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
						if ($results['evaluation'] == "4") $evaluation = "хорошо";
						if ($results['evaluation'] == "5") $evaluation = "отлично";
						echo '	<tr>
									<th scope="row">'.$i.'</th>
									<td>'.$student_name['last_name']." ".substr($student_name['first_name'], 0, 2).". ".substr($student_name['middle_name'], 0, 2).".".'</td>
									<td>'.$_GET['lesson-date'].'</td>
									<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
								</tr>';
						$i++;
					}
					echo '</tbody>
						</table>';
				}
			}
			?>
		</div>
		<!-- третья вкладка. поиск по дате -->
		<div role="tabpanel" class="tab-pane <?php if($_GET['tab'] == 3) echo 'active' ?>" id="profile2" style="margin-top: 20px;">
			<form class="form-horizontal" id="teacher-form-group" role="form" data-toggle="validator" method="get">
				<input type="hidden" name="tab" value="3">
				<div class="form-group date">
					<label for="lesson-date" class="col-sm-2 control-label">Дата занятий</label>
					<div class="col-sm-10">
						<input type="text" type="text" class="form-control" id="lesson-date3" name="lesson-date" pattern="^([0-9]){4}-([0-9]){2}-([0-9]){2}$" required>
						<span class="help-block">Дата проведённого занятия.</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Проверить и показать</button>
					</div>
				</div>
			</form>
			<?php
			if($userinfo['type'] == 1) {
				if($_GET['tab'] == 3) {
					if(empty($_GET['lesson-date']) ) { // ничего не заполнено
						// пусто
					} else {
						$stmt3 = $conn->prepare("SELECT DISTINCT lesson FROM registry WHERE date = :date");
						$stmt3->bindParam(':date', $_GET['lesson-date'], PDO::PARAM_INT);
						$stmt3->execute();
						while($lessons_prop = $stmt3->fetchColumn() ) {
							$stmt = $conn->prepare("SELECT discipline, `group` FROM lessons WHERE id = :lesson_id");
							$stmt->bindParam(':lesson_id', $lessons_prop, PDO::PARAM_INT);
							$stmt->execute();
							
							while ($lessons = $stmt->fetch()) {
								
								$stmt2 = $conn->prepare("SELECT name FROM disciplines WHERE id = :discipline_id");
								$stmt2->bindParam(':discipline_id', $lessons['discipline'], PDO::PARAM_INT);
								$stmt2->execute();
								$discipline = $stmt2->fetchColumn();
								
								$stmt3 = $conn->prepare("SELECT name FROM groups WHERE id = :group_id");
								$stmt3->bindParam(':group_id', $lessons['group'], PDO::PARAM_INT);
								$stmt3->execute();
								$group = $stmt3->fetch();
								
								echo "<h4>".$discipline." для группы ".$group['name']."</h4>";
								echo '<table class="table table-hover">
										  <thead>
											<tr>
											  <th>№</th>
											  <th>ФИО</th>
											  <th>Дата</th>
											  <th>Оценка</th>
											</tr>
										  </thead>
										  <tbody>';
								$stmt6 = $conn->prepare("SELECT student_id, evaluation FROM registry WHERE lesson = :lesson_id AND date = :date ORDER BY date DESC");
								$stmt6->bindValue(':lesson_id', $lessons_prop, PDO::PARAM_INT);
								$stmt6->bindValue(':date', $_GET['lesson-date'], PDO::PARAM_STR);
								$stmt6->execute();
								$i = 1;
								while($results = $stmt6->fetch() ) {
									$stmt7 = $conn->prepare("SELECT last_name, first_name, middle_name FROM students WHERE id = :student_id");
									$stmt7->bindValue(':student_id', $results['student_id'], PDO::PARAM_INT);
									$stmt7->execute();
									$student_name = $stmt7->fetch();
									$student_name = $student_name['last_name']." ".substr($student_name['first_name'], 0, 2).". ".substr($student_name['middle_name'], 0, 2).".";
									
									if ($results['evaluation'] == "1") $evaluation = "пропустил";
									if ($results['evaluation'] == "2") $evaluation = "не справился";
									if ($results['evaluation'] == "3") $evaluation = "удовлетворительно";
									if ($results['evaluation'] == "4") $evaluation = "хорошо";
									if ($results['evaluation'] == "5") $evaluation = "отлично";
									echo '	<tr>
												<th scope="row" width="30px">'.$i.'</th>
												<td>'.$student_name.'</td>
												<td width="200px">'.$_GET['lesson-date'].'</td>
												<td width="200px">'.$results['evaluation']." (".$evaluation.')</td>
											</tr>';
									$i++;
								}
								echo '</tbody>
									</table>';								
							}
						}
					}
				}
			}
			?>
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
	<script src="datepicker/js/bootstrap-datepicker.js"></script>
	<script src="datepicker/locales/bootstrap-datepicker.ru.min.js" charset="UTF-8"></script>
	<script>
	$('#lesson-date').datepicker({
		format: "yyyy-mm-dd",
		language: "ru",
		keyboardNavigation: false,
		orientation: "top auto",
		todayHighlight: true
	});
	$('#lesson-date2').datepicker({
		format: "yyyy-mm-dd",
		language: "ru",
		keyboardNavigation: false,
		orientation: "top auto",
		todayHighlight: true
	});
	$('#lesson-date3').datepicker({
		format: "yyyy-mm-dd",
		language: "ru",
		keyboardNavigation: false,
		orientation: "top auto",
		todayHighlight: true
	});
	</script>
  </body>
</html>