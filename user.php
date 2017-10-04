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
$systemKey = "KEY";
if (isset($_COOKIE["csp1_shy16121_cookie1"])){
	//echo $_COOKIE["csp1_shy16121_cookie1"];
	//echo $_COOKIE["csp1_shy16121_cookie2"];
	$myName = encrypt($_COOKIE["csp1_shy16121_cookie1"],'D',$_COOKIE["csp1_shy16121_cookie2"]);
	echo "welcome " . $myName . "!";
	echo "<br><hr /><br>";

	$conn = new mysqli("localhost", "root", "root", "db_shy16121");

	//send message
	if($_POST["add"]==="send"){
		$newMessage=$_POST["message"];
		$receiver = $_POST["receiver"];
		$idsql = "SELECT ID FROM students WHERE username = '$myName'";
		$idresult = $conn->query($idsql);
		$id = 0;
		if ($idresult->num_rows > 0) {
    		// 输出每行数据
    		while($row = $idresult->fetch_assoc()) {
        		$id = $row[ID];
       	 	}
 	 	}
		date_default_timezone_set("America/New_York");
		$newTime = date("Y-m-d h:i:sa");
		$message = $newMessage . $id . $newTime;
		$KI = openssl_random_pseudo_bytes(16);
		$KE = openssl_random_pseudo_bytes(16);
		$method = 'AES-128-CBC';
		$ivlen = openssl_cipher_iv_length($method);
		$IV1 = openssl_random_pseudo_bytes($ivlen);
		$IV2 = openssl_random_pseudo_bytes($ivlen);
		$IV3 = openssl_random_pseudo_bytes($ivlen);
		$keyed_hash = hash_hmac('md5', '$message', '$KI');
        $message = $newMessage . $keyed_hash;
		//ciphertext
		$ciphertext = openssl_encrypt("$message","AES-128-CBC","$KE",0,"$IV1");
		//encrypt KE
		$encrypted_KE = openssl_encrypt("$KE","AES-128-CBC","$systemKey",0,"$IV2");
		//encrypt KI
		$encrypted_KI = openssl_encrypt("$KI","AES-128-CBC","$systemKey",0,"$IV3");
		//encode IV1,IV2,IV3
		$encoded_IV1 = base64_encode($IV1);
		$encoded_IV2 = base64_encode($IV2);
		$encoded_IV3 = base64_encode($IV3);
		//insert
		$addsql = "INSERT INTO Messages(sender,receiver,content,encrypted_KE,encrypted_KI,encoded_IV1,encoded_IV2,encoded_IV3,sendtime)
				VALUES ('$myName', '$receiver', '$ciphertext','$encrypted_KE','$encrypted_KI','$encoded_IV1','$encoded_IV2','$encoded_IV3','$newTime')";
		mysqli_query($conn,$addsql);
		/*
		$addsql = "INSERT INTO Messages (sender,receiver,content,sendtime,salt)
				VALUES ('$myName', '$receiver', '$encrpytMessage','$newTime','$salt')";
		mysqli_query($conn,$addsql);  
		*/
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


	echo "Delete Message as a recipient<br><br>";
	echo "<table width='80%' border=1 align='center' cellpadding=4 cellspacing=0>";
	echo "<tr align='center'><td>Message</td><td>From</td><td>Time</td><td>delete</td></tr>";
	//$conn = new mysqli("localhost", "root", "root", "db_shy16121");
	//$sql0 = "SELECT sender,content,sendtime,salt FROM messages WHERE receiver = '$myName'";
	$sql0 = "SELECT sender,content,encrypted_KE,encrypted_KI,encoded_IV1,encoded_IV2,encoded_IV3,sendtime FROM messages WHERE receiver = '$myName'";
	$result0 = $conn->query($sql0);
	if ($result0->num_rows > 0) {
		while($row = $result0->fetch_assoc()) {
			if ($conn->connect_error) {
						die("can not connect: " . $conn->connect_error);
			}
			//uptdate 
			echo "<form action='user.php' method='post'>";
		
			$sendtime0 = $row['sendtime'];
			$sender0 = $row['sender'];
			echo " <input type='hidden' name='sendtime0' value = '$sendtime0'>";
			echo " <input type='hidden' name='sender0' value = '$sender0'>";    
			//decrypt
			$ciphertext = $row['content'];
            $IV1 = base64_decode($row['encoded_IV1']);
            $IV2 = base64_decode($row['encoded_IV2']);
            $IV3 = base64_decode($row['encoded_IV3']);
			$encrypted_KE = $row['encrypted_KE'];
            $encrypted_KI = $row['encrypted_KI'];
            $KE = openssl_decrypt("$encrypted_KE","AES-128-CBC","$systemKey",0,"$IV2");
            $KI = openssl_decrypt("$encrypted_KI","AES-128-CBC","$systemKey",0,"$IV3"); 
		    $temp0 = openssl_decrypt("$ciphertext","AES-128-CBC","$KE",0,"$IV1");
			$length0 = strlen($temp0);;
			$temp0 = substr($temp0,0,$length0 - 32);  

			echo "<tr align='center'>";        
			//$temp0 = encrypt($row["content"],'D',$row['salt']);
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
	//$sql1 = "SELECT receiver,content,sendtime,salt FROM messages WHERE sender = '$myName'";
	$sql1 = "SELECT receiver,content,encrypted_KE,encrypted_KI,encoded_IV1,encoded_IV2,encoded_IV3,sendtime FROM messages WHERE sender = '$myName'";
	$result1 = $conn->query($sql1);
	if ($result1->num_rows > 0) {
		while($row = $result1->fetch_assoc()) {
			if ($conn->connect_error) {
						die("can not connect: " . $conn->connect_error);
			}
			//uptdate 
			echo "<form action='user.php' method='post'>";
			$receiver1 = $row['receiver'];
			$sendtime1 = $row['sendtime'];
			echo " <input type='hidden' name='receiver1' value = '$receiver1'>";
			echo " <input type='hidden' name='sendtime1' value = '$sendtime1'>";

			//decrypt
			$ciphertext = $row['content'];
            $IV1 = base64_decode($row['encoded_IV1']);
            $IV2 = base64_decode($row['encoded_IV2']);
            $IV3 = base64_decode($row['encoded_IV3']);
			$encrypted_KE = $row['encrypted_KE'];
            $encrypted_KI = $row['encrypted_KI'];
            $KE = openssl_decrypt("$encrypted_KE","AES-128-CBC","$systemKey",0,"$IV2");
            $KI = openssl_decrypt("$encrypted_KI","AES-128-CBC","$systemKey",0,"$IV3");
            $temp1 = openssl_decrypt("$ciphertext","AES-128-CBC","$KE",0,"$IV1");
			$length1 = strlen($temp1);;
			$temp1 = substr($temp1,0,$length1 - 32);  

			echo "<tr align='center'>";      
			//$temp1 = encrypt($row["content"],'D',$row['salt']);     
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

	echo "<form name='theForm' action='user.php' method='post' id='usrform' onsubmit='return validateForm();'>";         
	echo "TO: <input name='receiver' type='text' value='' size='20'>";
	echo "<input type='submit' value='send' name = 'add'></form>";
	echo "CONTENT:<br><textarea rows='10' cols='50' form='usrform' name='message'></textarea>";
 
    $conn->close();

}
else{
	echo "Please sign in!<br>";
}
?>

