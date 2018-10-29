<?php

session_start();

$reflink = $_GET['ref'];

?>
<?php if(!isset( $_SESSION['user_id'] ) ): ?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
    <title>Вход в систему</title>
	<link href="signin.css" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    

    <!-- Custom styles for this template -->
    

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="container">

      <form class="form-signin" action="login_submit.php" method="post">
        <h2 class="form-signin-heading">Вход в систему</h2>
		<?php if(isset($_GET['error'])): ?>
		<div class="alert alert-danger">
			<?php echo $_GET['error'] ?>
		</div>
		<?php endif; ?>
        <label for="username" class="sr-only">Логин</label>
		<input type="hidden" name="reflink" value="<?php echo $reflink; ?>">
        <input type="text" id="username" name="username" class="form-control" placeholder="Ваш логин" maxlength="20" required autofocus>
        <label for="password" class="sr-only">Пароль</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Ваш пароль" maxlength="20" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>
		<p>После авторизации вы вернётесь на страницу <?php echo $reflink ?></p>
		<p><a href="adduser.php">Добавить нового пользователя (только для администратора)</a></p>
	 </form>
	
    </div> <!-- /container -->

  </body>
</html>
<?php else: ?>
<?php header("Location: index.php"); ?>
<?php endif; ?>