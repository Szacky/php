<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}



                
?>

<!DOCTYPE html>
<html>
<head>
    <title>数据库显示</title>
</head>
<body>
//以表格的形式进行显示
<table style='text-align:left;' border='1'>

         <tr><th>id</th><th>名字</th><th>图片</th><th>上传时间</th><th>用户</th><th>评价</th></tr>
    <?php
    session_start();
    include "connection.php";
    $con = mysqli_connect($host, $uname, $pwd, $db_name);
    //$content = $_POST["content"];
    
    $stmt = $con->prepare('SELECT  img_id,img_name,email, img_path, image_time, image_content FROM image_table');
    // In this case we can use the account ID to get the account info.
    //$stmt->bind_param('s',$_SESSION['email']);
    $stmt->execute();
    $stmt->bind_result( $img_id, $img_name, $email, $img_path, $image_time, $image_content);
    $stmt->fetch();
    $stmt->close();
    //$fp = fopen($file_path, "r");
    //$str = fread($fp, filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
    //echo $str = str_replace("\r\n", "<br />", $str);
    //$file=fopen("photo/mmexport1666584024169.jpg","r") or exit("Unable to open file!");
    


    
    $sql_w = "SELECT img_id,img_name,image_time,email, image_content,img_path, email FROM image_table  ";
    $ret = mysqli_query($con, $sql_w);
    $row = mysqli_fetch_array($ret); 
    $sql = mysqli_query($con,$sql_w);
    $datarow = mysqli_num_rows($ret); //长度
                //循环遍历出数据表中的数据
                $bianli = "a";
                for($i=0;$i<$datarow;$i++){
                    $sql_arr = mysqli_fetch_assoc($sql);
                    $id = $sql_arr['img_id'];
                    $name = $sql_arr['img_name'];
                    $time = $sql_arr['image_time'];
                    $path = $sql_arr['img_path'];
                    $content = $sql_arr['image_content'];
                    $email = $sql_arr['email'];
                    
                    
                    
                    
                    //$str = fread($fp, filesize($file_path));
                    echo "<tr><td>$id</td><td>$name</td><td><img src='$path'  border='0'/></td><td>$time</td><td>$email</td><td>$content<form method='POST'><input type='text' name = 'content' ><input type='submit' name = '".$bianli."'  value = 'confirm'></td></form></tr> ";
                    $content=$_POST['content'];
                    if (isset($_POST[$bianli]))
                                {
                                    if ($stmt = $con-> prepare('UPDATE image_table SET image_content = ? WHERE email = ?'))
                                        {
                                            
                                            $stmt-> bind_param('ss',$content, $email);
                                            $stmt-> execute();
                                            $stmt->store_result();
                                            echo "<script> alert ('成功');  </script>";
                                            //echo $name;
                                        }
                                        //echo "<script> alert ('失败');  </script>";
                                            //echo $name;
                                            
                                        }
                                        
                                        
                
                $bianli .= "a";

                }
    

                
               
    ?>

</table>

<tr> <td colspan="2" align="center"> <a href="auth.php">返回</a>！ </td> </tr>

</body>
</html>




<html>
<body>

