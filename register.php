<?php

// セッション開始

session_start();
require_once('config.php');

// 空間チェック

if(isset($_POST['submit']))
{
    if(isset($_POST['first_name'],$_POST['last_name'],$_POST['email'],$_POST['password']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']))
    {
        // trim — 文字列の先頭および末尾にあるホワイトスペースを取り除く
        // https://www.php.net/manual/ja/function.trim.php
        $firstName = trim($_POST['first_name']);
        $lastName = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // https://www.php.net/manual/ja/function.password-hash.php

        $options = array("cost"=>4);
        $hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
        $date = date('Y-m-d H:i:s');

        // https://www.php.net/manual/ja/filter.filters.validate.php
        if(filter_var($email, FILTER_VALIDATE_EMAIL))
		{
            $sql = 'select * from members where email = :email';
            $stmt = $pdo->prepare($sql);
            $p = ['email'=>$email];
            $stmt->execute($p);
            
            if($stmt->rowCount() == 0)
            {
                $sql = "insert into members (first_name, last_name, email, `password`, created_at,updated_at) values(:fname,:lname,:email,:pass,:created_at,:updated_at)";
            
                try{
                    $handle = $pdo->prepare($sql);
                    $params = [
                        ':fname'=>$firstName,
                        ':lname'=>$lastName,
                        ':email'=>$email,
                        ':pass'=>$hashPassword,
                        ':created_at'=>$date,
                        ':updated_at'=>$date
                    ];
                    
                    $handle->execute($params);
                    
                    $success = 'ユーザーが正常に作成されました';
                    
                }
                catch(PDOException $e){
                    $errors[] = $e->getMessage();
                }
            }
            else
            {
                $valFirstName = $firstName;
                $valLastName = $lastName;
                $valEmail = '';
                $valPassword = $password;

                $errors[] = '電子メールアドレスはすでに登録されています';
            }
        }
        else
        {
            $errors[] = "メールアドレスが無効です";
        }
    }
    else
    {
        if(!isset($_POST['first_name']) || empty($_POST['first_name']))
        {
            $errors[] = '名が必要です';
        }
        else
        {
            $valFirstName = $_POST['first_name'];
        }
        if(!isset($_POST['last_name']) || empty($_POST['last_name']))
        {
            $errors[] = '姓が必要です';
        }
        else
        {
            $valLastName = $_POST['last_name'];
        }

        if(!isset($_POST['email']) || empty($_POST['email']))
        {
            $errors[] = 'メールが必要です';
        }
        else
        {
            $valEmail = $_POST['email'];
        }

        if(!isset($_POST['password']) || empty($_POST['password']))
        {
            $errors[] = 'パスワードが必要です';
        }
        else
        {
            $valPassword = $_POST['password'];
        }
        
    }

}
?>

<!---------------------------------------------------------------BOOTSTRAP FORM----------------------------------------------------------------------->
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body class="bg-dark">

<div class="container h-100">
	<div class="row h-100 mt-5 justify-content-center align-items-center">
		<div class="col-md-5 mt-3 pt-2 pb-5 align-self-center border bg-light">
			<h1 class="mx-auto w-25" >登録</h1>
			<?php 
                // エラーメッセージ
				if(isset($errors) && count($errors) > 0)
				{
					foreach($errors as $error_msg)
					{
						echo '<div class="alert alert-danger">'.$error_msg.'</div>';
					}
                }
                // 成功メッセージ
                if(isset($success))
                {
                    
                    echo '<div class="alert alert-success">'.$success.'</div>';
                }
			?>
			<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
                <div class="form-group">
					<label for="email">姓:</label>
					<input type="text" name="first_name" placeholder="姓" class="form-control" value="<?php echo ($valFirstName??'')?>">
				</div>
                <div class="form-group">
					<label for="email">名:</label>
					<input type="text" name="last_name" placeholder="名" class="form-control" value="<?php echo ($valLastName??'')?>">
				</div>

                <div class="form-group">
					<label for="email">メール:</label>
					<input type="text" name="email" placeholder="メール" class="form-control" value="<?php echo ($valEmail??'')?>">
				</div>
				<div class="form-group">
				<label for="password">パスワード:</label>
					<input type="password" name="password" placeholder="パスワー" class="form-control" value="<?php echo ($valPassword??'')?>">
				</div>

				<button type="submit" name="submit" class="btn btn-primary">送信</button>
				<p class="pt-2"> ログインへ <a href="login.php">ログインへ</a></p>
				
			</form>
		</div>
	</div>
</div>
</body>
</html>
