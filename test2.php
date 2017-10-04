<?php

$conn = new mysqli("localhost", "root", "root", "db_shy16121");
$myName = "admin";
$idsql = "SELECT ID FROM students WHERE username = '$myName'";
$idresult = $conn->query($idsql);
$id = 0;
if ($idresult->num_rows > 0) {
    // 输出每行数据
    while($row = $idresult->fetch_assoc()) {
        //$id = $row[maxID];
        echo "check";
        $id = $row[ID];
        }
  }
$message0 = "HelloShaoyan";
$length = strlen($message);
$systemKey = "KEY";

//encrypt

date_default_timezone_set("America/New_York");
$newTime = date("Y-m-d h:i:sa");
$message = $message0 . $id . $newTime;
$KI = openssl_random_pseudo_bytes(16);
$KE = openssl_random_pseudo_bytes(16);
//echo $KE;

$method = 'AES-128-CBC';
$ivlen = openssl_cipher_iv_length($method);
$IV1 = openssl_random_pseudo_bytes($ivlen);
$IV2 = openssl_random_pseudo_bytes($ivlen);
$IV3 = openssl_random_pseudo_bytes($ivlen);


//keyed_hash
$keyed_hash = hash_hmac('md5', '$message', '$KI');
$message = $message0 . $keyed_hash;
//echo strlen($keyed_hash);

//ciphertext
$ciphertext = openssl_encrypt("$message","AES-128-CBC","$KE",0,"$IV1");
//echo $ciphertext;

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
        VALUES ('user', 'admin', '$ciphertext','$encrypted_KE','$encrypted_KI','$encoded_IV1','$encoded_IV2','$encoded_IV3','$newTime')";
//$addsql = "INSERT INTO Messages(sender,receiver,content,encrypted_KE,encrypted_KI,encoded_IV1,encoded_IV2,encoded_IV3,sendtime)
  //      VALUES ('admin', 'user', '0','0','0','0','0','0','0')";
mysqli_query($conn,$addsql);











?>