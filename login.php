<?php

if (isset($_POST["login"]))
{
    $email = $_POST["email"];
    $password = $_POST["password"];
    $code=$_POST['yzm'];

    // connect with database
    $conn = mysqli_connect('localhost', 'root', 'root', 'user');

    // check if credentials are okay, and email is verified
    $sql = "SELECT * FROM user WHERE email = '" . $email . "'";
    $result = mysqli_query($conn, $sql);


        if (mysqli_num_rows($result) == 0)
        {
            //die("Email not found.");
            header("Location:login.php?err=1");
        }

        $user = mysqli_fetch_object($result);

        if (!password_verify($password, $user->password))
        {   
        echo "<script> alert ('密码错误'); history.go(-1); </script>";
        }

        if ($user->email_verified_at == null)
        {
        die("Please verify your email <a href='email-verification.php?email=" . $email . "'>from here</a>");
        
        }

        //echo "<p>Your login logic here</p>";
        session_start();
        if (strtolower($code)==strtolower($_SESSION["code"])) 
        {   
        if (password_verify($password, $user->password))
        {
        //die("Password is not correct");
        $sql_wrong_login = "UPDATE user SET wrong_number_of_login = 0 WHERE email = '" . $email . "' ";
        $result  = mysqli_query($conn, $sql_wrong_login);

        $stmt = $conn->prepare('SELECT id, superuser FROM user WHERE email = $email');
        session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['email'] = $_POST['email'];
        //$_SESSION['id'] = $id; 
        $stmt = $conn->prepare('SELECT superuser FROM user WHERE email = ?');
				// In this case we can use the account ID to get the account info.
				$stmt->bind_param('s', $_SESSION['email']);
				$stmt->execute();
				$stmt->bind_result($superuser);
				$stmt->fetch();
				$stmt->close();
        if ($superuser == 0)
        {
		    header('Location: home.php');
            //echo "<script> alert ('$superuser');history.go(-1);</script>";
        }
        else
        {
            header('Location: auth.php');
            //echo "<script> alert ('$superuser');history.go(-1);</script>";
        }
        }
        }
        else 
        {
            header("Location:login.php?err=3");
        }
        exit();
    

}
?>


<!DOCTYPE html>
<html>
<body> 
    
<div class="content" align="center">
    <!--头部-->
        <div class="header"> <h1>登录页面</h1> </div> 
    <!--中部--> 
        <div class="middle">
        <table border="0">


        <form method="POST" ;>
        <tr><td>email：</td> <td><input type="email" name="email" placeholder="Enter email" required /></td> </tr>
        <tr><td>密码：</td> <td><input type="password" name="password" placeholder="Enter password" required /></td> </tr>
        <tr><td>验证码：</td> <td><input type="text" id="yzm" name="yzm" required="required"></td> </tr>
        
        <tr><td colspan="2" align="center">
            <script type="text/javascript">
            function shuaxin()
            {
            document.getElementById('code').src = "yzm.php?"+Math.random();
            }
            </script>
            <img src="yzm.php" onclick="shuaxin()" name="code"/>
                <span onclick="shuaxin()"><a href="login.php">刷新</a></span><br/>
        </td></tr>

        <tr> <td colspan="2" align="center" style="color:red;font-size:10px;"> 
            <!--提示信息-->
            <?php 

            
            $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "用户名不存在！";
                    break;

                case 2:
                    echo "用户名或密码错误！你共有5次机会";
                    break;


                case 3:
                    echo "验证码错误！";
                    break;

                case 4:
                    echo "当天最后一次机会用尽！";
                    break;
                    } ?>
        </td> </tr> 

        <tr><td colspan="2" align="center"><input type="submit" name="login" value="Login" ></td> </tr>

        <tr><td colspan="2" align="center"> 
            还没有账号，快去<a href="register.php">注册</a>吧
        </td></tr> 

        <tr><td colspan="2" align="center"> 
            Forgot password? click <a href="/forgotPassword.php">here</a>
        </td> </tr>


        </form>

        </table>
</body>
</html>