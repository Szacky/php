<?php

if (isset($_POST["verify_email"]))
{
    $email = $_POST["email"];
    $verification_code = $_POST["verification_code"];

    // connect with database
    $conn = mysqli_connect('localhost', 'root', 'root', 'user');

    // mark email as verified
    
  

        $sql_select_t = "SELECT verified FROM user WHERE email = '$email'"; //执行SQL语句
        $ret_t = mysqli_query($conn, $sql_select_t);
        $row_t = mysqli_fetch_array($ret_t); //判断用户名是否已存在
        $verified = $row_t['verified'];
        //echo "<script> alert ('$verified'); history.go(-1); </script>";
        
        if ($verified == 0)
        {
            $sql = "UPDATE user SET email_verified_at = NOW() WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'";
            $result  = mysqli_query($conn, $sql);
            $sql_v = "UPDATE user SET verified= 1 WHERE email = '" . $email . "' AND verification_code = '" . $verification_code . "'";
            $result  = mysqli_query($conn, $sql_v);
            if (mysqli_affected_rows($conn) == 0)
            {
                die("Verification code failed.");
            }
            echo "<script> alert ('you can log now'); history.go(-1); </script>";
        }
        else
        {
            echo "<script> alert ('请不要重复激活'); history.go(-1); </script>";
        }
    //exit();
    
}

?>

<form method="POST">
    <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" required>
    <input type="text" name="verification_code" placeholder="Enter verification code" required />

    <input type="submit" name="verify_email" value="Verify Email">
    <tr> <td colspan="2" align="center"> 如果已有账号，快去<a href="login.php">登录</a>吧！ </td> </tr>
</form>