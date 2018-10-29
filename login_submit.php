<?php

session_start();

require_once('db_connect.php'); // переменная $conn

$reflink = $_POST['reflink'];

/*** пользователь уже авторизован? ***/
if(isset( $_SESSION['user_id'] ))
{
    header("Location: ".$reflink); // отправляем пользователя обратно
}
/*** все формы заполнены? ***/
if(!isset( $_POST['username'], $_POST['password']))
{
	$message = "Заполните все поля.";
}
/*** проверка длины ***/
elseif (strlen( $_POST['username']) > 20 || strlen($_POST['username']) < 4)
{
	$message = "Неправильная длина логина.";
}
/*** проверка длины ***/
elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 6)
{
	$message = "Неправильная длина пароля";
}
/*** проверка символов ***/
elseif (!preg_match("/^([-_A-z0-9]){4,20}$/", $_POST['username']) )
{
    $message = "Недопустимые символы в логине.";
}
/*** проверка символов ***/
elseif (!preg_match("/^([-_A-z0-9]){4,20}$/", $_POST['password']) )
{
    $message = "Недопустимые символы в пароле.";
}
else
{
    /*** очистка полей ***/
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
	
	$password = sha1( $password );
	
    try
    {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username AND password = :password");

        $stmt->bindParam(':username', $username, PDO::PARAM_STR, 20);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
		
        /*** execute the prepared statement ***/
        $stmt->execute();
		
        /*** check for a result ***/
        $user_id = $stmt->fetchColumn();
		
        /*** if we have no result then fail boat ***/
        if($user_id == false || $user_id == 0)
        {
            $message = 'Такого пользователя не существует.';
        }
        /*** if we do have a result, all is well ***/
        else
        {
			/*** set the session user_id variable ***/
			$_SESSION['user_id'] = $user_id;

			/*** tell the user we are logged in ***/
			//$message = 'You are now logged in';
			header("Location: ".$reflink);
			break 2;
        }
    }
    catch(Exception $e)
    {
        /*** if we are here, something has gone wrong with the database ***/
        $message = 'Ошибка базы данных. Проверьте ваши данные.';
    }
}

header("Location: login.php?error=".$message."&ref=".$reflink);

?>