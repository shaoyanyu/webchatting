<?php
require_once 'encrypt.php';
if (isset($_COOKIE["csp1_shy16121_cookie1"])){
	//$username = $_COOKIE["csp1_shy16121"];
    $username = encrypt($_COOKIE["csp1_shy16121_cookie1"],'D',$_COOKIE["csp1_shy16121_cookie2"]);
    echo "Wlcome " . $username . "!<br>";
    if (isset($_REQUEST['act']) == 'change') {
        $oldPassword=$_POST["oldPassword"];
        $newPassword=$_POST["newPassword"];
        $conn = new mysqli("localhost", "root", "root", "db_shy16121");
        // test the connection
        if ($conn->connect_error) {
            die("can not connect: " . $conn->connect_error);
        } 
        $sql = "SELECT passwords,salt FROM students WHERE username = '$username' ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // print results
            while($row = $result->fetch_assoc()) {
                $realPassword = encrypt($row['passwords'],'D',$row['salt']);
                if($oldPassword == $realPassword){
                    //echo $newPassword;
                    $hashPassword = encrypt($newPassword, 'E',$row["salt"]);
                    mysqli_query($conn,"UPDATE Students SET passwords = '$hashPassword'
                        WHERE username = '$username'");
                    echo "change OK";
                
        
                }else{
                    echo "old password is wrong";
                }
            }
        } else {
            echo "'$username' is not found in the system ";
        }
        $conn->close();
        }
}
else{
	echo "Please sign in!<br>";
}
?>
<form name="theForm" action="account.php?act=change" method="POST"
    onsubmit="return validateForm();" >
    Old Password: <input name="oldPassword" type="text" value="" size="20">
<br>
    New Pasword: <input name="newPassword" type="text" value="" size="20">
<br>
    <input type="submit" value="Submit">
</form>