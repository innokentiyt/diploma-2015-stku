<?php
	
//echo "ID пользователя: ".$_SESSION['user_id']."<br>";

$stmt = $conn->prepare("SELECT type, student_id, teacher_id FROM users WHERE id = :userid");
$stmt->bindParam(':userid', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();

$userinfo = $stmt->fetch();

//echo "Тип пользователя: ".$result['type'];

if($userinfo['type'] == 2) { // если студент
	//ФИО
	$stmt = $conn->prepare("SELECT last_name, first_name, middle_name, `group` FROM students WHERE id = :student_id");
	$stmt->bindParam(':student_id', $userinfo['student_id'], PDO::PARAM_INT);
	$stmt->execute();
	$student = $stmt->fetch();
	
	//группа
	$stmt = $conn->prepare("SELECT name, chair FROM groups WHERE id = :group_id");
	$stmt->bindParam(':group_id', $student['group'], PDO::PARAM_INT);
	$stmt->execute();
	$student_group = $stmt->fetch();
	
	//кафедра
	$stmt = $conn->prepare("SELECT name, fullname, faculty FROM chairs WHERE id = :chair_id");
	$stmt->bindParam(':chair_id', $student_group['chair'], PDO::PARAM_INT);
	$stmt->execute();
	$student_chair = $stmt->fetch();
	
	//факультет
	$stmt = $conn->prepare("SELECT name, fullname FROM faculties WHERE id = :faculty_id");
	$stmt->bindParam(':faculty_id', $student_chair['faculty'], PDO::PARAM_INT);
	$stmt->execute();
	$student_faculty = $stmt->fetch();	
} elseif($userinfo['type'] == 1) { //если преподаватель
	//ФИО
	$stmt = $conn->prepare("SELECT last_name, first_name, middle_name, chair FROM teachers WHERE id = :teacher_id");
	$stmt->bindParam(':teacher_id', $userinfo['teacher_id'], PDO::PARAM_INT);
	$stmt->execute();
	$teacher = $stmt->fetch();
	
	//кафедра
	$stmt = $conn->prepare("SELECT name, fullname, faculty FROM chairs WHERE id = :chair_id");
	$stmt->bindParam(':chair_id', $teacher['chair'], PDO::PARAM_INT);
	$stmt->execute();
	$teacher_chair = $stmt->fetch();
	
	//факультет
	$stmt = $conn->prepare("SELECT name, fullname FROM faculties WHERE id = :faculty_id");
	$stmt->bindParam(':faculty_id', $teacher_chair['faculty'], PDO::PARAM_INT);
	$stmt->execute();
	$teacher_faculty = $stmt->fetch();
}

?>