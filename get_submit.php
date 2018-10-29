<?php

session_start();

require_once('auth_check.php');

require_once('db_connect.php'); // переменная $conn

require_once('userinfo.php');

require_once('phpexcel/Classes/PHPExcel.php');

$stmt = $conn->prepare("SELECT discipline, `group`, teacher FROM lessons WHERE id = :lesson_id");
$stmt->bindValue(':lesson_id', $_POST['lesson'], PDO::PARAM_INT);
$stmt->execute();
while($vedomost = $stmt->fetch() ) {
	
	$group_id = $vedomost['group'];
	
	$stmt2 = $conn->prepare("SELECT name FROM disciplines WHERE id = :discipline_id");
	$stmt2->bindValue(':discipline_id', $vedomost['discipline'], PDO::PARAM_INT);
	$stmt2->execute();
	$discipline = $stmt2->fetch();
	$discipline = $discipline['name'];
	
	$stmt3 = $conn->prepare("SELECT name FROM groups WHERE id = :group_id");
	$stmt3->bindValue(':group_id', $vedomost['group'], PDO::PARAM_INT);
	$stmt3->execute();
	$group = $stmt3->fetch();
	$group = $group['name'];
	
	$stmt3 = $conn->prepare("SELECT last_name, first_name, middle_name FROM teachers WHERE id = :teacher_id");
	$stmt3->bindValue(':teacher_id', $vedomost['teacher'], PDO::PARAM_INT);
	$stmt3->execute();
	$teacher = $stmt3->fetch();
}

$teacher_short = $teacher['last_name']." ".substr($teacher['first_name'], 0, 2).". ".substr($teacher['middle_name'], 0, 2)."."; // Фамилия И. О.

// шифруем идентификатор ведомости (номер занятия и дата) паролем пользователя
$stmt4 = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
$stmt4->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt4->execute();
$password = $stmt4->fetch();
$password = $password['password'];
$ved_code = $_POST['lesson']."|".$_POST['lesson-date'];
$ved_code_bin = mcrypt_encrypt(MCRYPT_BLOWFISH, $password, $ved_code, MCRYPT_MODE_ECB);
$ved_code_hex = bin2hex($ved_code_bin);

$objPHPExcel = PHPExcel_IOFactory::load("template.xlsx");


$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('P2', $ved_code_hex);
$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('P4', $discipline);
$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('P5', $group);
$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('P6', $_POST['lesson-date']);
$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('P7', $teacher_short);

$stmt5 = $conn->prepare("SELECT last_name, first_name, middle_name FROM students WHERE `group` = :group_id AND id <> 0 ORDER BY last_name ASC");
$stmt5->bindValue(':group_id', $group_id, PDO::PARAM_INT);
$stmt5->execute();

$i = 11;

while($students = $stmt5->fetch() ) {
	$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, strval(($i - 10).". ") );
	$cellValue = $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $students['last_name']." ".substr($students['first_name'], 0, 2).". ".substr($students['middle_name'], 0, 2).".");// Фамилия И. О.
	$i++;
}

$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);

$i--;

$objPHPExcel->getActiveSheet()->getStyle('C11:S'.$i)->applyFromArray($styleArray);
unset($styleArray);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Ведомость '.$discipline.' '.$group.' ('.$_POST['lesson-date'].').xlsx"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');


?>