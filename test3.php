<?php
$conn = new mysqli("localhost", "root", "root", "db_shy16121");

$sql = "SELECT receiver,content,encrypted_KE,encrypted_KI,encoded_IV1,encoded_IV2,encoded_IV3,sendtime FROM messages WHERE sender = 'admin'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
            echo $row['receiver'];
            echo "<br>";
            echo $row['content'];
            echo "<br>";
            echo $row['encrypted_KE'];
            echo "<br>";
            echo $row['encrypted_KI'];
            echo "<br>";
            echo "<hr />";
            
            $systemKey = "KEY";
            $ciphertext = $row['content'];
            $IV1 = base64_decode($row['encoded_IV1']);
            $IV2 = base64_decode($row['encoded_IV2']);
            $IV3 = base64_decode($row['encoded_IV3']);
            echo "haha";
            //getKE,KI
            $encrypted_KE = $row['encrypted_KE'];
            $encrypted_KI = $row['encrypted_KI'];
            $KE = openssl_decrypt("$encrypted_KE","AES-128-CBC","$systemKey",0,"$IV2");
            $KI = openssl_decrypt("$encrypted_KI","AES-128-CBC","$systemKey",0,"$IV3");

            $message = openssl_decrypt("$ciphertext","AES-128-CBC","$KE",0,"$IV1");
            echo $message;


            



        }
}

?>