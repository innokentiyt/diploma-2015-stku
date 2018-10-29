<?php

session_start();

require_once('db_connect.php'); // переменная $conn

$message = 'Что-то пошло не так.';

if ($_POST['usertype'] == 2) {
	//это студент
	if(!isset( $_POST['student-username'], $_POST['student-password'], $_POST['student-lastname'], $_POST['student-firstname'], $_POST['student-group'])) {
		$message = "Заполните все поля.";
		$flag = 1;
	} else {
		// очистка полей
		$username = filter_var($_POST['student-username'], FILTER_SANITIZE_STRING);
		$password = filter_var($_POST['student-password'], FILTER_SANITIZE_STRING);
		$lastname = filter_var($_POST['student-lastname'], FILTER_SANITIZE_STRING);
		$firstname = filter_var($_POST['student-firstname'], FILTER_SANITIZE_STRING);
		$group = filter_var($_POST['student-group'], FILTER_SANITIZE_STRING);
		$usertype = filter_var($_POST['usertype'], FILTER_SANITIZE_STRING);
		
		// шифрование пароля
		$password = sha1( $password );
		
		try	{
			$stmt = $conn->query('SELECT `id`,`last_name`,`first_name`,`group` FROM `students`');
			while ($row = $stmt->fetch()) {
				if($row['last_name'] == $lastname && $row['first_name'] == $firstname) {
					$student_group = $row['group'];
					$student_group = intval($student_group);
					foreach ($conn->query("SELECT DISTINCT `name` FROM `groups` WHERE `id` = $student_group") as $row2) {
						if($row2['name'] == $group) {
							// студент найден в базе данных
							
							$student_id = $row['id'];
							
							$stmt2 = $conn->prepare("INSERT INTO users (`username`, `password`, `type`, `student_id`) VALUES (:username, :password, :type, :student_id)");
							
							$stmt2->bindParam(':username', $username, PDO::PARAM_STR, 20);
							$stmt2->bindParam(':password', $password, PDO::PARAM_STR, 40);
							$stmt2->bindParam(':type', $usertype, PDO::PARAM_INT);
							$stmt2->bindParam(':student_id', $student_id, PDO::PARAM_INT);
							
							$stmt2->execute();
							
							$message = "Пользователь успешно добавлен!";
							
							$flag = 0;
							break 2;
						} else {
							$message = "Такого студента не существует в базе данных.";
							$flag = 1;
							break 2;
						}
					}
				} else {
					$flag = 1;
				}
			}
		} catch(Exception $e) {
			/*** check if the username already exists ***/
			if( $e->getCode() == 23000) {
				$message = 'Попытка создать дубль.';
				$flag = 1;
			}
			else {
				/*** if we are here, something has gone wrong with the database ***/
				$message = 'Что-то пошло не так.';
				$flag = 1;
			}
		}		
	}
	
	
} elseif ($_POST['usertype'] == 1) {
	// это преподаватель
	if(!isset( $_POST['teacher-username'], $_POST['teacher-password'], $_POST['teacher-lastname'], $_POST['teacher-firstname'])) {
		$message = "Заполните все поля.";
		$flag = 1;
	} else {
		// очистка полей
		$username = filter_var($_POST['teacher-username'], FILTER_SANITIZE_STRING);
		$password = filter_var($_POST['teacher-password'], FILTER_SANITIZE_STRING);
		$lastname = filter_var($_POST['teacher-lastname'], FILTER_SANITIZE_STRING);
		$firstname = filter_var($_POST['teacher-firstname'], FILTER_SANITIZE_STRING);
		$usertype = filter_var($_POST['usertype'], FILTER_SANITIZE_STRING);
		
		// шифрование пароля
		$password = sha1( $password );
		
		try	{
			foreach ($conn->query("SELECT `id`,`last_name`,`first_name` FROM `teachers`") as $row) {
				$message = "Такого преподавателя не существует в базе данных.";
				$flag = 1;
				if($row['last_name'] == $lastname && $row['first_name'] == $firstname) {
					
					// преподаватель найден в базе данных
					
					$teacher_id = $row['id'];
					
					$stmt2 = $conn->prepare("INSERT INTO users (`username`, `password`, `type`, `teacher_id`) VALUES (:username, :password, :type, :teacher_id)");
					
					$stmt2->bindParam(':username', $username, PDO::PARAM_STR, 20);
					$stmt2->bindParam(':password', $password, PDO::PARAM_STR, 40);
					$stmt2->bindParam(':type', $usertype, PDO::PARAM_INT);
					$stmt2->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
					
					$stmt2->execute();
					
					$message = "Пользователь успешно добавлен!";
					
					$flag = 0;
					break;
				}
			}
		}
		catch(Exception $e) {
			/*** check if the username already exists ***/
			if( $e->getCode() == 23000) {
				$message = 'Попытка создать дубль.';
				$flag = 1;
			}
			else {
				/*** if we are here, something has gone wrong with the database ***/
				$message = 'Что-то пошло не так.';
				$flag = 1;
			}
		}
	}
	
} else {
	$message = "Ошибка формы.";
	$flag = 1;
}

if($flag == 1) {
	header("Location: adduser.php?error=$message");
} elseif ($flag == 0) {
	header("Location: adduser.php?success=$message");
}

?>