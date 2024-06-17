<?php
$dsn = "mysql:host=localhost;dbname=movies;charset=utf8";
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
if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // データを削除する関数を呼び出す
    deleteMovie($dbh, $id);
}

// データベース接続を閉じる
$dbh = null;

// データ削除関数の定義
function deleteMovie($dbh, $id) {
    $sql = "DELETE FROM user_data WHERE id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$id]);

    echo "削除しました。";
    echo '<p><a href="admin_reservation.php"> 一覧に戻る</a></p>';
}
?>
