<?php
//DB接続に必要な情報をまとめておきます
$dsn = "mysql:host=localhost;dbname=movies_admin;charset=utf8";
$id = "testuser";
$pass = "testpass";

//テンプレートファイル
$tmpl_input = "touroku.tmpl";
$tmpl_update = "henkou.tmpl";

#改行
$input = str_replace("\r\n", "_kaigyou_", $input);
$input = str_replace("\n", "_kaigyou_", $input);
$input = str_replace("\r", "_kaigyou_", $input);

//表示
parse_form();

try {
    $dbh = new PDO($dsn, $id, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    parse_form(); // $in 配列の準備

    movie_search();
} catch (PDOException $e) {
    die("PDO Error:" . $e->getMessage());
}


//form受け取り
function parse_form()
{
    global $in;

    $param = array();
    if (isset($_GET) && is_array($_GET)) {
        $param += $_GET;
    }
    if (isset($_POST) && is_array($_POST)) {
        $param += $_POST;
    }

    foreach ($param as $key => $val) {
        if (is_array($val)) {
            $val = array_shift($val);
        }

        $enc = mb_detect_encoding($val);
        $val = mb_convert_encoding($val, "UTF-8", $enc);

        $val = htmlentities($val, ENT_QUOTES, "UTF-8");

        $in[$key] = $val;
    }
    return $in;
}


//登録
function insert()
{
    global $dbh;
    global $in;

    if (isset($mode) && ($mode == "register")) {
        // フォームからのデータがNULLでないことを確認する
        if (!empty($input) && !empty($movie) && !empty($release_date) && !empty($end_date) && !empty($time) && !empty($screen) && !empty($genru) && !empty($price)) {
            insert();
            echo "登録完了しました。";
            echo '<p><a href="admin_first.html"> 画面を戻る。</a></p>';
        }
    } else {
        echo "";
    }
    $sql = "INSERT INTO users (id, movie, release_date, end_date, time, screen, genru, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(1, $in["id"]);
    $stmt->bindParam(2, $in["movie"]);
    $stmt->bindParam(3, $in["release_date"]);
    $stmt->bindParam(4, $in["end_date"]);
    $stmt->bindParam(5, $in["time"]);
    $stmt->bindParam(6, $in["screen"]);
    $stmt->bindParam(7, $in["genru"]);
    $stmt->bindParam(8, $in["price"]);

    $stmt->execute();
}

//編集
function update()
{
    global $dbh;
    global $in;

    $sql = "UPDATE users SET movie = ?, release_date = ?, end_date = ?, time = ?, screen = ?, genru = ?, price = ? WHERE id = ?";
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(1, $in["movie"]);
    $stmt->bindParam(2, $in["release_date"]);
    $stmt->bindParam(3, $in["end_date"]);
    $stmt->bindParam(4, $in["time"]);
    $stmt->bindParam(5, $in["screen"]);
    $stmt->bindParam(6, $in["genru"]);
    $stmt->bindParam(7, $in["price"]);
    $stmt->bindParam(8, $in["id"]);

    $stmt->execute();
}

//削除
function delete()
{
    global $dbh;
    global $in;

    $sql = "UPDATE users SET flag = 1 WHERE id = ?";
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(1, $in["id"]);

    $stmt->execute();
}

function movie_search()
{
    global $dbh;
    global $tmpl_input;

    $sql = "SELECT * FROM users";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    $block = "";

    while ($row = $stmt->fetch()) {
        $input = $row['id'];
        $movie = $row['movie'];
        $release_date = $row['release_date'];
        $end_date = $row['end_date'];
        $time = $row['time'];
        $screen = $row['screen'];
        $genru = $row['genru'];
        $price = $row['price'];

        $data = file_get_contents($tmpl_input);
        $data = str_replace("!id!", $input, $data);
        $data = str_replace("!movie!", $movie, $data);
        $data = str_replace("!release_date!", $release_date, $data);
        $data = str_replace("!end_date!", $end_date, $data);
        $data = str_replace("!time!", $time, $data);
        $data = str_replace("!screen!", $screen, $data);
        $data = str_replace("!genru!", $genru, $data);
        $data = str_replace("!price!", $price, $data);

        $block .= $data;
    }

    echo $block;
}
?>
