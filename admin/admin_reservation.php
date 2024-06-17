<?php
// データベース接続情報
$dsn = "mysql:host=localhost;dbname=movies;charset=utf8";
$id = "testuser";
$pass = "testpass";

$tmpl_input = "admin_reservation.tmpl";
// 映画一覧表示
function movie()
{
    global $dbh, $tmpl_input;

    try {
        // データベースに接続
        $sql = "SELECT * FROM user_data";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        echo "<h2>予約一覧</h2>";
        echo '<p><a href="admin_first.html"> 管理者TOPへ</a></p>';

        $block = "";

        while ($row = $stmt->fetch()) {
            $input = $row['id'];
            $name = $row['name'];
            $movie = $row['movie'];
            $time = $row['time'];

            $data = file_get_contents($tmpl_input);
            $data = str_replace("!id!", $input, $data);
            $data = str_replace("!name!", $name, $data);
            $data = str_replace("!movie!", $movie, $data);
            $data = str_replace("!time!", $time, $data);

            $block .= $data;
    }
        echo $block;
    }catch (PDOException $e) {
    die("PDO Error:" . $e->getMessage());
    }
}
try {
    $dbh = new PDO($dsn, $id, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 映画一覧表示の呼び出し
    movie($dbh, $tmpl_input);

} catch (PDOException $e) {
    die("PDO Error:" . $e->getMessage());
}
// 削除
function delete()
{
    global $dbh, $in;

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $in["id"]);
    $stmt->execute();
}
