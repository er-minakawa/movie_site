<?php
//DB接続に必要な情報をまとめておきます
$dsn ="mysql:host=localhost;dbname=movie;charset=utf8";
$id="testuser";
$pass="testpass";
$input = $_GET["id"];
$input2 = $_GET["pass"];
$mode = $_GET["mode"];
$name = $_GET["namae"];
$birthday = $_GET["birthday"];

#utf-8に統一
$enc = mb_detect_encoding($input);
$input = mb_convert_encoding($input, "UTF-8", $enc);

#クロスサイトスクリプティング対策
$input = htmlentities($input, ENT_QUOTES, "UTF-8");

#改行
$input = str_replace("\r\n", "_kaigyou_", $input);
$input = str_replace("\n", "_kaigyou_", $input);
$input = str_replace("\r", "_kaigyou_", $input);






//データベースへ接続
try{
    $dbh =new PDO($dsn,$id,$pass,$name,$birthday);
     if(isset($mode) && ($mode== "register")){
    // フォームからのデータがNULLでないことを確認する
    if (!empty($input) && !empty($input2) && !empty($name) && !empty($birthday)) {
        register();
        echo "登録完了しました。";
        } 
    }else {
       echo "";
    }
}catch(PDOException $e){
    echo"接続失敗…";
    echo"エラー内容:". $e->getMessage();
}

function register(){
    global $dbh,$input,$input2;
    $sql =<<<sql
    insert into users (id,pass,namae,birthday)values(?,?,?,?);
sql;

    $stmt =$dbh ->prepare($sql);
    $stmt->bindParam(1,$input);
    $stmt->bindParam(2,$input2);
    $stmt->bindParam(3,$name);
    $stmt->bindParam(4,$birthday);


    $stmt ->execute();
}
# エラーチェック
$error_notes="";
if(!isset($_GET["id"]) || empty($_GET["id"])){
    $error_notes.="・id未入力です。<br>";
}
if(!isset($_GET["pass"])|| $_GET["pass"] === ""){
    $error_notes.="passが未入力です。<br>";
}
if(!isset($_GET["namae"])|| $_GET["namae"] === ""){
    $error_notes.="namaeが未入力です。<br>";
}
if(!isset($_GET["birthday"])|| $_GET["birthday"] === ""){
    $error_notes.="birthdayが未入力です。<br>";
}

# エラーが存在する場合
if($error_notes !== "") {
    error($error_notes);
}

function error($error_message) {
    echo "<br>";
    echo "<div style='color: red;'><strong>エラーが発生しました:</strong><br>" . $error_message . "</div>";
}


    
?>