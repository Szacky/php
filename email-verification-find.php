<?php
require 'vendor/autoload.php';

if (isset($_POST["change"]))
{
    $email = $_POST["email"];
    $find_code = $_POST["find_code"];
    $password = $_POST["password"];
    $re_password = $_POST["re_password"];


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
                                if ($password == $re_password)
                                { 
                                    $conn = mysqli_connect('localhost', 'root', 'root', 'user'); //准备SQL语句
                                    $sql_select = "SELECT email, find_code FROM user WHERE email = '$email' AND find_code = '$find_code'"; //执行SQL语句
                                    $ret = mysqli_query($conn, $sql_select);
                                    $row = mysqli_fetch_array($ret); //判断用户名或密码是否正确
        
                                    if ($email == $row['email']) 
                                    { //选中“记住我”

                                        if ($email == $row['email'] && $find_code == $row['find_code']) 
                                        {    
                                        $sql_2 = "UPDATE user SET find_code = null WHERE email = '" . $email . "' ";
                                        $result_2  = mysqli_query($conn, $sql_2);    
                                        header("Location:email-verification-find.php?err=4");
                                        if ($stmt = $conn-> prepare('UPDATE user SET password = ? WHERE email = ?'))
                                        {
                                            $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
                                            $stmt-> bind_param('ss',$encrypted_password, $email);
                                            $stmt-> execute();
                                            $stmt->store_result();
                                        }
                
                                        }
                                        else
                                        {
                                        header("Location:email-verification-find.php?err=2");
                                        }
                                    }
                                    else
                                    {
                                        header("Location:email-verification-find.php?err=1"); 
                                    }

                                }
                                else
                                {
                                    header("Location:email-verification-find.php?err=3");
                                }
                            
                        }
                    }
    
                    
    
    // connect with database
    
}
?>

<body> 
<div class="content" align="center">
    <!--头部-->
        <div class="header"> <h1>找回验证页面</h1> </div> 
    <!--中部--> 
        <div class="middle">
        <table border="0">
        <form method="POST">
            <tr><td>email：</td> <td><input type="text" name="email" value="<?php echo $_GET['email']; ?>" required></td> </tr>
            <tr><td>密   码：</td> <td><input type="password" name="password" placeholder="Enter password" required /></td> </tr>
            <tr><td>重复密码：</td> <td><input type="password" name="re_password" placeholder="Enter password" required /></td> </tr>
            <tr><td>邮箱验证码：</td><td><input type="text" name="find_code" placeholder="Enter find_code" required /></td> </tr>

            <tr> <td colspan="2" align="center" style="color:red;font-size:10px;"> 
            <!--提示信息-->
            <?php $err = isset($_GET["err"]) ? $_GET["err"] : "";
            switch ($err) {
                case 1:
                    echo "email和找回码不匹配！";
                    break;

                case 2:
                    echo "验证码错误！";
                    break;


                case 3:
                    echo "两次密码不一致！";
                    break;

                case 4:
                    echo "修改成功！";
                    break;
                    } ?>
            </td> </tr> 

            <tr><td colspan="2" align="center"><input type="submit" name="change" value="change"></td> </tr>
            <tr> <td colspan="2" align="center"> 如果找回成功，快去<a href="login.php">登录</a>吧！ </td> </tr>

        </form>
        </div>
        </table>
</div>
</body>