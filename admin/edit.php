<?php
$dsn = "mysql:host=localhost;dbname=movies_admin;charset=utf8";
$username = "testuser";
$password = "testpass";

// データベースに接続
try {
    $dbh = new PDO($dsn, $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// フォームからのデータを受け取る
$id = $_POST['id'];
$movie = $_POST['movie'];
$release_date = $_POST['release_date'];
$end_date = $_POST['end_date'];
$time = $_POST['time'];
$screen = $_POST['screen'];
$genru = $_POST['genru'];
$price = $_POST['price'];


// 更新関数を呼び出す
update($dbh, $id, $movie, $release_date, $end_date, $time, $screen, $genru, $price);

echo "";

// データベース接続を閉じる
$dbh = null;

// update()関数の定義
function update($dbh, $id, $movie, $release_date, $end_date, $time, $screen, $genru, $price) {
    $sql = "UPDATE users SET movie = ?, release_date = ?, end_date = ?, time = ?, screen = ?, genru = ?, price = ? WHERE id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$movie, $release_date, $end_date, $time, $screen, $genru, $price, $id]);
}

include 'edit.tmpl';
?>
