<!-- install phpmailer -->

<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

if (isset($_POST["register"]))
{
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $sex = $_POST["sex"];
    $re_password = $_POST["re_password"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $code = $_POST["yzm"];
    $question = $_POST["question"];
    $answer = $_POST["answer"];

    //Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);
    
        if (strlen(trim($name)) > 5)
        {
            if ($password == $re_password)
            {
                // connect with database
                $conn = mysqli_connect('localhost', 'root', 'root', 'user');


                $sql_select = "SELECT name FROM user WHERE name = '$name'"; //执行SQL语句
                $ret = mysqli_query($conn, $sql_select);
                $row = mysqli_fetch_array($ret); //判断用户名是否已存在

                $sql_select2 = "SELECT email FROM user WHERE email = '$email'"; //执行SQL语句
                $ret2 = mysqli_query($conn, $sql_select2);
                $row2 = mysqli_fetch_array($ret2); //判断email是否已存在

                session_start();
                if ($name == $row['name']) 
                { //用户名已存在，显示提示信息
                    //header("Location:register.php?err=2");
                    echo "<script> alert ('用户名已存在！');history.go(-1);</script>"; 
                }
                else 
                { //用户名不存在，插入数据 //准备SQL语句
                if ($email == $row2['email'])
                { //email已存在，显示提示信息
                    //header("Location:register.php?err=5");
                    echo "<script> alert ('email已存在！');history.go(-1);</script>";
                }
                else
                {
                    if (strtolower($code) == strtolower($_SESSION["code"]))
                    {    
                   
                        if (strlen(trim($password)) < 8)
                        {//必须大于7个字符
                            //return '密码必须大于7字符';
                            //alert("密码必须大于7字符");
                            echo "<script> alert ('密码必须大于7字符');history.go(-1);</script>";
                            //echo "<p>密码必须大于11字符.</p>";
                        }
                        if (strlen(trim($password)) > 7)
                        {


                            if ((preg_match("/^[0-9]+$/", $password))|| (preg_match("/^[a-zA-Z]+$/", $password)))
                            {
                            //return '密码不能全是数字或字母，请包含数字，字母大小写';
                            echo "<script> alert ('密码不能全是数字或字母，请包含数字，字母大小写');history.go(-1);</script>";	
                            }
                    
                            if ((preg_match("/^[0-9A-Z]+$/", $password))|| (preg_match("/^[0-9a-z]+$/", $password)))
                            {
                            //return '请包含数字，字母大小写';
                            echo "<script> alert ('请包含数字，字母大小写');history.go(-1);</script>";	
                            }

                            else
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
                                        $mail->Username = '邮箱名';
    
                                        //SMTP password
                                        $mail->Password = '密钥';
    
                                        //Enable TLS encryption;
                                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    
                                        //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                                        $mail->Port = 587;
    
                                        //Recipients
                                        $mail->setFrom('邮箱名', '验证');
    
                                        //Add a recipient
                                        $mail->addAddress($email, $name);
    
                                        //Set email format to HTML
                                        $mail->isHTML(true);
    
                                        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    
                                        $mail->Subject = 'Email verification';
                                        $mail->Body    = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
    
                                        $mail->send();
                                        // echo 'Message has been sent';
    
                                        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
                                        
    
            
    
                                        // insert in users table
                                        $sql = "INSERT INTO user(name, email, password, verification_code, email_verified_at, sex, address, phone, question, answer) VALUES ('" . $name . "', '" . $email . "', '" . $encrypted_password . "', '" . $verification_code . "' ,NULL, '" . $sex . "', '" . $address . "', '" . $phone . "','" . $question . "','" . $answer . "')";
                                        mysqli_query($conn, $sql);
    
                                        header("Location: email-verification.php?email=" . $email);
                                        exit();
                                    } catch (Exception $e) 
                                        {
                                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                                        }
                            }
                        }
                    
                    
                    }
                    else
                    {   //验证码错误
                        //header("Location:register.php?err=1"); 
                        echo "<script> alert ('验证码错误');history.go(-1);</script>";  
                    }
                }
                }
            }
            else
            {   //重复密码不一致
            //header("Location:register.php?err=3");
            echo "<script> alert ('重复密码不一致');history.go(-1);</script>";  
            }
        }
        else
        {
        echo "<script> alert ('用户名不小于6位字符');history.go(-1);</script>";
        }
    
    
    
}







?>
<html > <!-- lang="en" -->
<head>
 <meta charset="utf-8" />
 <title>注册页</title>
 <style type="text/css">
 
 </style>
</head>
<body> 
    <div class="content" align="center"> <!--头部--> 
    <div class="header"> <h1>邮箱验证页面</h1> </div> <!--中部--> 
    <div class="middle">
    <form method="POST">
    <table border="0"> 
        <tr><td title= "用户名是唯一的，不可与其他用户重名，且用户名不小于4位字符" style="color: blue;">用户名：</td> <td ><input type="text" id="name" name="name" placeholder="Enter name" required/></td> </tr>
        <tr><td title= "email唯一的，不可重复注册" style="color: blue;">Email：</td> <td><input type="email" id="email" name="email" placeholder="Enter email" required /></td> </tr>
        <tr><td title= "密码大于8为位，且包含数字和大小写字母" style="color: blue;">密   码：</td> <td><input type="password" id="password" name="password" placeholder="Enter password" required /></td> </tr>
        <tr><td>重复密码：</td> <td><input type="password" id="re_password" name="re_password" placeholder="Enter password" required /></td> </tr>
        <tr><td>性别：</td> <td> <input type="radio" id="sex" name="sex" value="man">男 <input type="radio" id="sex" name="sex" value="woman">女<td>*可选项</td> </td></tr>
        <tr><td>地址：</td> <td><input type="text" id="address" name="address" required="required"></td></tr>
        <tr><td>电话：</td> <td><input type="text" id="phone" name="phone" required="required"></td></tr>
        <tr><td title= "设置找回密码和修改密码的密保问题" style="color: blue;">问题：</td> <td><input type="text" id="question" name="question" required="required"></td></tr>
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
                <span onclick="shuaxin()"><a href="register.php">刷新</a></span><br/>
        </td></tr>
        <tr> <td colspan="2" align="center" style="color:red;font-size:10px;"> 
            <!--提示信息-->
            <?php $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "验证码错误！";
                    break;
                     
                case 2:
                    echo "用户名已存在！";
                    break;
                
                case 3:
                    echo "密码与重复密码不一致！";
                    break;
                
                case 4:
                    echo "注册成功！";
                    break;

                case 5:
                    echo "邮箱存在！";
                    break;}

                    ?>
        </td></tr>
        <tr><td colspan="2" align="center"><input type="submit" name="register" value="Register"></td> </tr>
        <tr> <td colspan="2" align="center"> 如果已有账号，快去<a href="login.php">登录</a>吧！ </td> </tr>
    </table>
    </form>
    </div> 
    <!--脚部--> 
    <div class="footer"> <small>Copyright &copy; 版权所有·欢迎翻版 </div> </div>
</body>
</html>

