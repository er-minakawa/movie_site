<?php
// データベース接続情報
$dsn = "mysql:host=localhost;dbname=movies;charset=utf8";
$id = "testuser";
$pass = "testpass";


// var_dump($_POST);
// フォームから送信されたデータを取得
if (isset($_POST) ==['reservation']) {
    // $user_id = $_POST["id"];
    $user_name = $_POST["name"];
    $movie = $_POST["movie"];
    $time = $_POST["time"];

    try {
        // データベースに接続
        $dbh = new PDO($dsn, $id, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 予約情報をデータベースに挿入
        $sql = "INSERT INTO user_data (name,movie,time) VALUES (?,?,?)";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$user_name,$movie,$time]);

        // 予約成功のメッセージを表示
        echo "予約が完了しました。";
    } catch (PDOException $e) {
        die("PDO Error:" . $e->getMessage());
    }
}
?>