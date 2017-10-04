<script>
function  validateForm()
{
    var s = document.forms["theForm"]["message"].value;  
    if (s.length > 10) {
        alert("The size of the message is limited ");
        return false;
    }
}
</script>



<?php
require_once 'encrypt.php';
//echo "haha";
echo "<br>";
/*
function createSalt()
{
    $text = md5(uniqid(rand(), TRUE));
    return substr($text, 0, 3);
}
$salt = createSalt();
echo $salt;
echo"<br>";
$p1 = encrypt("userpass", 'E', $salt);
echo $p1;
echo "<br>";
echo "heihei";
$p2 = encrypt($p1,'D',$salt);
echo $p2;
echo "haha";
*/

/*
$p1 = hash('md5', 'yushaoyan'|$salt);
echo "<br>";
//$password = hash('md5', $salt . $hash);
echo $p1;
echo "<br>";
$p2 = hash('md5',$p1|$salt);
echo $p2;
*/

$conn = new mysqli("localhost", "root", "root", "db_shy16121");

//send message
if($_POST["add"]==="send"){
    $newMessage=$_POST["message"];
    function createSalt()
    {
      $text = md5(uniqid(rand(), TRUE));
      return substr($text, 0, 3);  
    }
    $salt = createSalt();
    $encrpytMessage = encrypt($newMessage, 'E', $salt);
    //echo $newMessage;
    //$currentTime = $date("Y-m-d H:i:s");
    //echo $currentTime;
    date_default_timezone_set("America/New_York");
    //echo "NOW is " . date("Y-m-d h:i:sa");
    $newTime = date("Y-m-d h:i:sa");
    $addsql = "INSERT INTO Messages (sender,receiver,content,sendtime,salt)
               VALUES ('user', 'admin', '$encrpytMessage','$newTime','$salt')";
    mysqli_query($conn,$addsql);  
}

//delete message
if($_POST["receiverDelete"]==="delete"){
    //echo "hahaddd";
    $thistime = $_POST["sendtime0"];
    $thissender = $_POST["sender0"];
    //echo $thistime;
    mysqli_query($conn,"DELETE FROM Messages WHERE sendtime = '$thistime' and sender = '$thissender'");
}

if($_POST["senderDelete"]==="delete"){
    //echo "haha";
    $thattime = $_POST["sendtime1"];
    $thisreceiver = $_POST["receiver1"];
    //echo $thattime;
    //echo $thisreceiver;
    mysqli_query($conn,"DELETE FROM Messages WHERE sendtime = '$thattime' and receiver = '$thisreceiver'");
}


echo "<br>Delete Message as a recipient<br><br>";
echo "<table width='80%' border=1 align='center' cellpadding=4 cellspacing=0>";
echo "<tr align='center'><td>Message</td><td>From</td><td>Time</td><td>delete</td></tr>";
//$conn = new mysqli("localhost", "root", "root", "db_shy16121");
$sql0 = "SELECT sender,content,sendtime,salt FROM messages WHERE receiver = 'user'";
$result0 = $conn->query($sql0);
if ($result0->num_rows > 0) {
    while($row = $result0->fetch_assoc()) {
         if ($conn->connect_error) {
                    die("can not connect: " . $conn->connect_error);
         }
         //uptdate 
         echo "<form action='test.php' method='post'>";
    
         $sendtime0 = $row['sendtime'];
         $sender0 = $row['sender'];
         echo " <input type='hidden' name='sendtime0' value = '$sendtime0'>";
         echo " <input type='hidden' name='sender0' value = '$sender0'>";    

         echo "<tr align='center'>";        
         $temp0 = encrypt($row["content"],'D',$row['salt']);
         echo "<td>" . $temp0 ."</td>";
        // echo "<td>" . $row['content'] ."</td>";
         echo "<td>" . $row['sender'] ."</td>";
         echo "<td>" . $row['sendtime'] ."</td>";
         echo "<td><input type='submit' value='delete' name='receiverDelete'></td>";
         echo "</tr>";
         echo "</form>";
    }
    //echo "</form>";
}
echo "</table>";

echo "<br><hr /><br>";

echo "Delete Message as a sender<br><br>";
echo "<table width='80%' border=1 align='center' cellpadding=4 cellspacing=0>";
echo "<tr align='center'><td>Message</td><td>To</td><td>Time</td><td>delete</td></tr>";
$sql1 = "SELECT receiver,content,sendtime,salt FROM messages WHERE sender = 'user'";
$result1 = $conn->query($sql1);
if ($result1->num_rows > 0) {
    while($row = $result1->fetch_assoc()) {
         if ($conn->connect_error) {
                    die("can not connect: " . $conn->connect_error);
         }
         //uptdate 
         echo "<form action='test.php' method='post'>";
         $receiver1 = $row['receiver'];
         $sendtime1 = $row['sendtime'];
         echo " <input type='hidden' name='receiver1' value = '$receiver1'>";
         echo " <input type='hidden' name='sendtime1' value = '$sendtime1'>";


         echo "<tr align='center'>";      
         $temp1 = encrypt($row["content"],'D',$row['salt']);     
         echo "<td>" . $temp1 ."</td>";
         //echo "<td>" . $row['content'] ."</td>";
         echo "<td>" . $row['receiver'] ."</td>";
         echo "<td>" . $row['sendtime'] ."</td>";
         echo "<td><input type='submit' value='delete' name='senderDelete'></td>";
         echo "</tr>";
         echo "</form>";
    }
    //echo "</form>";
}
echo "</table>";

echo "<br><hr /><br>";

echo "<form name='theForm' action='test.php' method='post' id='usrform' onsubmit='return validateForm();'>";         
echo "TO: <input name='receiver' type='text' value='' size='20'>";
echo "<input type='submit' value='send' name = 'add'></form>";
echo "CONTENT:<br><textarea rows='10' cols='50' form='usrform' name='message'></textarea>";


?>

