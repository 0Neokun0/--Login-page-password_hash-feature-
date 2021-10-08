<?php
//セッション開始

session_start();
require_once('config.php');

//空間チェック

if(isset($_POST['submit']))
{
	if(isset($_POST['email'],$_POST['password']) && !empty($_POST['email']) && !empty($_POST['password']))
	{
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

			//trim — 文字列の先頭および末尾にあるホワイトスペースを取り除く
			// https://www.php.net/manual/ja/function.trim.php
			// https://www.php.net/manual/ja/filter.filters.validate.php

		if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$sql = "select * from members where email = :email ";
			$handle = $pdo->prepare($sql);
			$params = ['email'=>$email];
			$handle->execute($params);

			// パスワードと電子メールの不一致チェック

			if($handle->rowCount() > 0)
			{
				$getRow = $handle->fetch(PDO::FETCH_ASSOC);
				if(password_verify($password, $getRow['password']))
				{
					unset($getRow['password']);
					$_SESSION = $getRow;
					header('location:dashboard.php');
					exit();
				}
				else
				{
					$errors[] = "メールアドレスまたはパスワードが間違っています";
				}
			}
			else
			{
				$errors[] = "メールアドレスまたはパスワードが間違っています";
			}
			
		}
		else
		{
			$errors[] = "メールアドレスが無効です"; 
		}

	}
	else
	{
		$errors[] = "メールアドレスとパスワードが必要です"; 
	}

}
?>
<!----------------------------------------------------------------------BOOTSTRAP LOGIN----------------------------------------------------------------->
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body class="bg-dark">

<div class="container h-100">
	<div class="row h-100 mt-5 justify-content-center align-items-center">
		<div class="col-md-5 mt-5 pt-2 pb-5 align-self-center border bg-light">
			<h1 class="mx-auto w-25" >ログイン</h1>
			<?php 
			// エラーメッセージ
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="alert alert-danger">'.$error_msg.'</div>';
					}
				}
			?>

			<!--フォームインプット-->

			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<div class="form-group">
					<label for="email">メール:</label>
					<input type="text" name="email" placeholder="メール" class="form-control">
				</div>
				<div class="form-group">
				<label for="email">パスワード:</label>
					<input type="password" name="password" placeholder="パスワー" class="form-control">
				</div>

				<button type="submit" name="submit" class="btn btn-primary">送信</button>
				
				<a href="register.php" class="btn btn-primary">登録</a>
			</form>
		</div>
	</div>
</div>
</body>
</html>