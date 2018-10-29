<?php

session_start();

require_once('auth_check.php');

require_once('db_connect.php'); // переменная $conn

require_once('userinfo.php');

require_once('phpexcel/Classes/PHPExcel.php');

if(isset($_FILES['excelfile'])) {
if($_FILES['excelfile']['tmp_name']) {
if(!$_FILES['excelfile']['error']) {
    $inputFile = $_FILES['excelfile']['tmp_name'];
	$inputFileType = PHPExcel_IOFactory::identify($inputFile);
    if($inputFileType == 'Excel2007') {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFile);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        } catch(Exception $e) {
            die($e->getMessage());
        }
		
		// расшифровка идентификатора ведомости
		$stmt = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->execute();
		$password = $stmt->fetch();
		$password = $password['password'];
		$format_check = "/^([0-9]){1,10}\|([0-9]){4}-([0-9]){2}-([0-9]){2}$/"; // число|ГГГГ-ММ-ДД
		$ved_code_hex = $objPHPExcel->getActiveSheet()->getCell('P2')->getValue();
		$ved_code_bin = pack("H*" , $ved_code_hex);
		$ved_code = mcrypt_decrypt(MCRYPT_BLOWFISH, $password, $ved_code_bin, MCRYPT_MODE_ECB);
		$ved_code = trim($ved_code); // удаляем лишние пробелы
		
		if(preg_match($format_check, $ved_code) ) { // верный формат идентификатора
			$vedomost = explode("|", $ved_code);
			$lesson = $vedomost[0];
			$lesson_date = $vedomost[1];
			
			$stmt2 = $conn->prepare("SELECT `group` FROM lessons WHERE id = :lesson_id");
			$stmt2->bindValue(':lesson_id', $lesson, PDO::PARAM_INT);
			$stmt2->execute();
			$group = $stmt2->fetch();
			$group = $group['group'];
			
			// подсчёт количества студентов в группе
			$count = $conn->prepare("SELECT count(*) FROM students WHERE `group` = :group_id AND id <> 0");
			$count->bindValue(':group_id', $group, PDO::PARAM_INT);
			$count->execute(); 
			$count = intval($count->fetchColumn() );
			
			$stmt3 = $conn->prepare("SELECT id FROM students WHERE `group` = :group_id AND id <> 0 ORDER BY last_name ASC");
			$stmt3->bindValue(':group_id', $group, PDO::PARAM_INT);
			$stmt3->execute();
			
			for($i = 11; $i <= 10 + $count; $i++) {
				$NULLcount = 0;
				for($j = 'O'; $j <= 'S'; $j++) {
					$value = $objPHPExcel->getActiveSheet()->getCell($j.$i)->getValue();
					if($value == NULL) {
						$NULLcount++;
					} else {
						$evaluation = $j;
					}
				}
				if($NULLcount == 4) {
					$evaluation = str_replace('O', '1', $evaluation);
					$evaluation = str_replace('P', '2', $evaluation);
					$evaluation = str_replace('Q', '3', $evaluation);
					$evaluation = str_replace('R', '4', $evaluation);
					$evaluation = str_replace('S', '5', $evaluation);
					$student_id = intval($stmt3->fetchColumn() );
					try {
						$insert = $conn->prepare("INSERT INTO registry (student_id,date,lesson,evaluation) VALUES (:student_id,:date,:lesson,:evaluation)");
						$insert->bindValue(':student_id', $student_id, PDO::PARAM_INT);
						$insert->bindValue(':date', $lesson_date, PDO::PARAM_STR);
						$insert->bindValue(':lesson', $lesson, PDO::PARAM_INT);
						$insert->bindValue(':evaluation', $evaluation, PDO::PARAM_INT);
						$insert->execute();
						
						$message = "Данные успешно сохранены.";
						$flag = 0;
					} catch(Exception $e) {
						if( $e->getCode() == 23000) {
							$message = 'Попытка создать дубль.';
							$flag = 1;
						}
						else {
							$message = $e->getMessage();
							$flag = 1;
						}
					}
				} else {
					$message = "Ведомость заполнена неправильно. В одной строке может быть только одна отмеченная ячейка.";
					$flag = 1;
					break;
				}
			}
		} else {
			$message = "Некорректный XLSX-файл.";
			$flag = 1;
		}
		
    }
    else{
        $message = "Пожалуйста, загрузите XLSX-файл.";
		$flag = 1;
    }
}
else{
    $message = $_FILES['spreadsheet']['error'];
	$flag = 1;
}
}
}

if($flag == 1) {
	header("Location: return.php?error=$message");
} elseif ($flag == 0) {
	header("Location: return.php?success=$message");
}

?>