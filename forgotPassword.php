<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

if (isset($_POST["find"]))
{
    
    $email = $_POST["email"];
    $answer = $_POST["answer"];
    $code=$_POST['yzm'];
    $mail = new PHPMailer(true);

    // connect with database
    $conn = mysqli_connect('localhost', 'root', 'root', 'user');

    // check if credentials are okay, and email is verified
    $sql = "SELECT * FROM user WHERE email = '" . $email . "'";
    $result = mysqli_query($conn, $sql);

 
        if (mysqli_num_rows($result) == 0)
        {
        //die("Email not found.");
        header("Location:forgotPassword.php?err=1");
        }

        $user = mysqli_fetch_object($result);

        if (!password_verify($answer, $user->answer))
        {
        //die("Password is not correct");
        header("Location:forgotPassword.php?err=2");
        }

        if ($user->email_verified_at == null)
        {
            die("Please verify your email <a href='email-verification.php?email=" . $email . "'>from here</a>");
        
        }

        session_start();
        if (strtolower($code)==strtolower($_SESSION["code"])) 
        {   
        if (password_verify($answer, $user->answer))
        {
            try {
                //Enable verbose debug output
                $mail->SMTPDebug = 0;//SMTP::DEBUG_SERVER;
        
                //Send using SMTP
                $mail->isSMTP();
        
                //Set the SMTP server to send through
                $mail->Host = 'smtp.qq.com';
        
                //Enable SMTP authentication
                $mail->SMTPAuth = true;
        
                //SMTP username
                $mail->Username = '邮箱号';
        
                //SMTP password
                $mail->Password = '密钥';
        
                //Enable TLS encryption;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
                //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                $mail->Port = 587;
        
                //Recipients
                $mail->setFrom('邮箱号', '验证');
        
                //Add a recipient
                $mail->addAddress($email, $name);
        
                //Set email format to HTML
                $mail->isHTML(true);
        
                $find_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        
                $mail->Subject = 'Email verification';
                $mail->Body    = '<p>Your find_code code is: <b style="font-size: 30px;">' . $find_code . '</b></p>';
        
                $mail->send();
                // echo 'Message has been sent';
        
                //$encrypted_password = password_hash($password, PASSWORD_DEFAULT);
                $encrypted_answer = password_hash($answer, PASSWORD_DEFAULT);
                mysqli_query($conn,"UPDATE user SET find_code = $find_code WHERE email = '" . $email . "' ");
               
        
                header("Location: email-verification-find.php?email=" . $email);
                exit();
            } catch (Exception $e) 
                {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
        }
        }
        else 
        {
        header("Location:forgotPassword.php?err=3");
        }
        exit();
    
}
?>




<body> 
    <div class="content" align="center"> <!--头部--> 
    <div class="header"> <h1>忘记密码</h1> </div> <!--中部--> 
    <div class="middle">
    <form method="POST">
    <table border="0"> 
        <tr><td>Email：</td> <td><input type="email" name="email" placeholder="Enter email" required /></td> </tr>
        <tr><td>答案：</td> <td><input type="text" id="answer" name="answer" required="required"></td></tr>
        <tr><td>验证码：</td> <td><input type="text" id="yzm" name="yzm" required="required"></td> </tr>
        <tr><td colspan="2" align="center">
            <script type="text/javascript">
            function shuaxin()
            {
            document.getElementById('code').src = "yzm.php?"+Math.random();
            }
            </script>
            <img src="yzm.php" onclick="shuaxin()" name="code"/>
                <span onclick="shuaxin()"><a href="#">刷新</a></span><br/>
        </td></tr>
        <tr> <td colspan="2" align="center" style="color:red;font-size:10px;"> 
            <!--提示信息-->
            <?php $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "email不存在！";
                    break;

                case 2:
                    echo "安全答案错误！";
                    break;


                case 3:
                    echo "验证码错误！";
                    break;

                //case 4:
                    //echo "安全问题错误！";
                    //break;
                    } ?>
        </td> </tr>
        <tr><td colspan="2" align="center"><input type="submit" name="find" value="find"></td> </tr>
        <tr> <td colspan="2" align="center"> 如果已有账号，快去<a href="login.php">登录</a>吧！ </td> </tr>
    </table>
    </form>
    </div> 
    <!--脚部--> 
    <div class="footer"> <small>Copyright &copy; 版权所有·欢迎翻版 </div> </div>
</body>