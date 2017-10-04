<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>admin</title>
</head>
 
<body>

<?php
    require_once 'encrypt.php';
    $passwords=$_POST["password"];
    //echo $passwords;
    //echo $_POST["userName"];

    $q = isset($_POST['state'])? htmlspecialchars($_POST['state']) : '';
    if($q) {
            if($q ==='admin') {
                    //echo $q;
            } else {
                   // echo 'normal';
            }
    } else {
        //echo "no chance";
    }
    //echo $_POST["change"];
?>


<?php 
if (isset($_COOKIE["csp1_shy16121_cookie1"])){
    //echo "hello " . $_COOKIE["csp1_shy16121"] . "!<br>";
    $myName = encrypt($_COOKIE["csp1_shy16121_cookie1"],'D',$_COOKIE["csp1_shy16121_cookie2"]);
    echo "hello " . $myName . "!<br>";
    $conn = new mysqli("localhost", "root", "root", "db_shy16121");
    $presql = "SELECT state FROM students WHERE username = '$myName'";
    $preresult = $conn->query($presql);
    if ($preresult->num_rows > 0) {
        while($row = $preresult->fetch_assoc()) {
            if($row["state"] ==="admin"){
                // connect
                //$conn = new mysqli("localhost", "root", "root", "db_shy16121");
                // test the connection
                if ($conn->connect_error) {
                    die("can not connect: " . $conn->connect_error);
                } 
                $username = $_POST["userName"];
                if($_POST["change"]==="change"){
                    function createSalt0()
                    {
                        $text = md5(uniqid(rand(), TRUE));
                        return substr($text, 0, 3);
                    }
                    $salt0 = createSalt0();
                    $passwords0 = encrypt($passwords, 'E', $salt0);
                    mysqli_query($conn,"UPDATE Students SET passwords = '$passwords0', state = '$q',salt = '$salt0'
                    WHERE username = '$username'");
                }
                if($_POST["delete"]==="delete"){
                    echo $username;
                    mysqli_query($conn,"DELETE FROM Students WHERE username = '$username'");
                }
                if($_POST["add"]==="add"){
                    $newName = $_POST["newUserName"];
                    $newPassword = $_POST["newPassword"];
                    function createSalt()
                    {
                        $text = md5(uniqid(rand(), TRUE));
                        return substr($text, 0, 3);
                    }
                    $salt = createSalt();
                    $newPassword1 = encrypt($newPassword, 'E', $salt);
                    $p = isset($_POST['addState'])? htmlspecialchars($_POST['addState']) : '';
                    $idsql = "SELECT max(ID) as maxID FROM students";
                    $idresult = $conn->query($idsql);
                    $id = 0;
                    if ($idresult->num_rows > 0) {
                    // 输出每行数据
                        while($row = $idresult->fetch_assoc()) {
                            $id = $row[maxID];
                        }
                    }
                    $id = $id + 1;
                    //echo $id;

                    $addsql = "INSERT INTO Students (username, passwords, state,salt,ID)
                    VALUES ('$newName', '$newPassword1', '$p','$salt','$id')";
                    mysqli_query($conn,$addsql);
                }

                //update
                $sql = "SELECT username, passwords,state,salt FROM students";
                $result = $conn->query($sql);
                echo "<table width='80%' border=1 align='center' cellpadding=5 cellspacing=0>";
                echo "<tr align='center'><td>name</td><td>password</td><td>state</td><td>change</td><td>delete</td></tr>";
                if ($result->num_rows > 0) {
                    // 输出每行数据
                    while($row = $result->fetch_assoc()) {
                        echo "<form action='admin.php' method='post'>";
                        $temp0 = $row["username"] ;
                        echo " <input type='hidden' name='userName' value = '$temp0'>";
                        echo "<tr align='center'>";
                    
                        echo "<td>" . $row['username'] ."</td>";
                        
                       
                        //$temp1 = $row["passwords"] ;
                        $temp1 = encrypt($row["passwords"],'D',$row['salt']);

                        echo "<td> <input type='text' name='password' value = '$temp1'></td>";
                        $temp2 = $row["state"] ;
                        $res =strcmp($temp2,"admin");
                        if($res == 0){
                            echo "<td>
                            <select name='state'>
                            <option value='normal'>normal</option>
                            <option value='admin' selected='selected'>admin</option>
                            </select></td>";
                        }else{
                            echo "<td>
                            <select name='state'>
                            <option value='normal' selected='selected'>normal</option>
                            <option value='admin'>admin</option>
                            </select></td>";
                        }
                        echo "<td><input type='submit' value='change' name='change'></td>";
                
                        echo "<td><input type='submit' value='delete' name='delete'></td>";
                    
                        echo "</tr>";
                        echo "</form>";
                        //echo "</tr>";
                    }
                } else {
                    echo "0 results";
                }
                echo "</table>";
                //$conn->close();

                //add
                echo "<br><br>";
                echo "<table width='80%' border=1 align='center' cellpadding=4 cellspacing=0>";
                echo "<tr align='center'><td>name</td><td>password</td><td>state</td><td>add</td></tr>";
                echo "<form action='admin.php' method='post'>";
                echo "<tr align='center'>";
                echo "<td> <input type='text' name='newUserName' ></td>";
                echo "<td> <input type='text' name='newPassword' ></td>";
                echo "<td>
                            <select name='addState'>
                            <option value='normal'>normal</option>
                            <option value='admin' selected='selected'>admin</option>
                            </select></td>";
                echo "<td><input type='submit' value='add' name='add'></td>";
                echo "</tr>";
                echo "</form>";
                echo "</table>";
                
        }else{
            echo "You are not admin ";
        }

      }
            
    } else {
            echo "no state ";
     }
    $conn->close();
}else{
    echo "please sign in";
}

?> 
</form>
</body>
</html>