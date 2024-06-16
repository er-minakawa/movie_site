<?php
//DB接続に必要な情報をまとめておきます
$dsn ="mysql:host=localhost;dbname=movies;charset=utf8";
$id="testuser";
$pass="testpass";
$rogin = isset($_GET["rogin"]) ? $_GET["rogin"] : null;

$origin = [];
if(isset($_GET) || isset($_POST)){
    $origin += $_GET;
    $origin += $_POST;
}

foreach($origin as $key => $value){
    $enc = mb_detect_encoding($value);
    $value = mb_convert_encoding($value, "UTF-8", $enc);
    $value = htmlentities($value, ENT_QUOTES);
    $input[$key] = $value;
    
}
// ID とパスワードの取得
$input1 = isset($_POST["id"]) ? $_POST["id"] : null;
$input2 = isset($_POST["pass"]) ? $_POST["pass"] : null;

$admin_id = "1025";
$admin_pass = "hirakegoma";

try {
    $dbh = new PDO($dsn, $id, $pass);

    // ログインフォームから送信された ID とパスワードがある場合のみ処理を実行
    if(isset($input1) && isset($input2)){
        if($input1 === $admin_id && $input2 === $admin_pass){
            // 管理者用のページにリダイレクト
            header("Location:../admin/admin_first.html");
            exit;
        } else {
            // 通常のユーザーとしてログイン処理を行う
            if(isUser($dbh, $input1, $input2)){
                // 通常のユーザーのページにリダイレクト
                header("Location:../user/user_page.php");
                exit;
            } else {
                // ログインが失敗した場合の処理
                echo "エラーが発生しました。";
            }
        }
    }
}catch(PDOException $e){
    echo  $e -> getMessage();
}



function isUser ($dbh,$input,$input2){
    $sql = "SELECT * FROM users WHERE id = :input AND pass  = :input2";
    $stmt =$dbh ->prepare($sql);
    $stmt->bindParam(':input',$input);
    $stmt->bindParam(':input2',$input2);
    $stmt-> execute();

    
    return $stmt ->fetch() !== false;
}

?>