<?php

// init.php already included mylib.php and config.php
//require_once	'init.php';

require_once 'encrypt.php';
if (isset($_REQUEST['act']) == 'login') {

    if (empty($_REQUEST['username'])) {
	sys_error("Username is not specified.");
    }

    // connect
    $conn = new mysqli("localhost", "root", "root", "db_shy16121");
    // test the connection
    if ($conn->connect_error) {
        die("can not connect: " . $conn->connect_error);
    } 

    $name=$_POST["username"];
    $password=$_POST["password"];

    $sql = "SELECT passwords,salt FROM students WHERE username = '$name' ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // print results
        while($row = $result->fetch_assoc()) {
            //echo "<br> passwords: ". $row["passwords"];
            $realPassword = encrypt($row['passwords'],'D',$row['salt']);
            if($password == $realPassword){
                //setcookie("csp1_shy16121","$name",0,"/prj1shy16121"); 
                function createSalt()
                {
                    $text = md5(uniqid(rand(), TRUE));
                    return substr($text, 0, 3);
                }
                $salt = createSalt();
                $hashname = encrypt($name, 'E', $salt);
                setcookie("csp1_shy16121_cookie1",$hashname,0,"/prj1shy16121"); 
                setcookie("csp1_shy16121_cookie2",$salt,0,"/prj1shy16121"); 

                echo "hello,";
                echo $name; 
               
                echo "<br><a href='http://localhost/prj1shy16121/user.php'>user</a>";
                echo "<br><a href='http://localhost/prj1shy16121/account.php'>account</a>";
                echo "<br><a href='http://localhost/prj1shy16121/admin.php'>admin</a>";
                echo "<br><a href='http://localhost/prj1shy16121/logout.php'>logout</a>";
            }else{
                echo "password is wrong";
            }
        }
    } else {
        echo "'$name' is not found in the system ";
    }
    $conn->close();


}
else {
    include "login.html";
}
?>