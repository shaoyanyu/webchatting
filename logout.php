<script>
function changetext()
{
 alert("log out successfully");
 var exp = new Date();
 exp.setTime(exp.getTime() - 1);

document.cookie= "csp1_shy16121_cookie1" + "="+""+";expires="+exp.toGMTString();
document.cookie= "csp1_shy16121_cookie2" + "="+""+";expires="+exp.toGMTString();

  
}
</script>


<?php
require_once 'encrypt.php';
if (isset($_COOKIE["csp1_shy16121_cookie1"])){
    $myName = encrypt($_COOKIE["csp1_shy16121_cookie1"],'D',$_COOKIE["csp1_shy16121_cookie2"]);
	echo "hello " . $myName . "!<br>";
    echo "<h1 onclick='changetext()'>click me to log out！</h1>";
}
else{
	echo "Please sign in!<br>";
}

?>
