<?php
 
session_start();
$password = htmlentities($_POST["pass"],ENT_QUOTES ,"utf-8");
if($password ==""){
 
    $_SESSION["pass"] = $password;
 
    echo "";
 
}else{
    echo '<p style="color:red;"> ログイン失敗。</p>';
    echo <<<_FORM_
    <form>
        <input type="button" value=" 前画面に戻る" onclick="history.back()">
    </form>
_FORM_;
}

?>