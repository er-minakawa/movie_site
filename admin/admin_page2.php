<?php
// DB接続に必要な情報をまとめておきます
$dsn = "mysql:host=localhost;dbname=movies_admin;charset=utf8";
$id = "testuser";
$pass = "testpass";

// テンプレートファイル
$tmpl_input = "movie.tmpl";

// 表示
parse_form(); // ＄in配列にデータを入れる

try {
    $dbh = new PDO($dsn, $id, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $in = parse_form(); // $in 配列の準備

    if(isset($in['search_movie'])) {
        search_movie($in['search_movie']);
    } else {
        movie(); // 通常の映画一覧を表示
    }

} catch (PDOException $e) {
    die("PDO Error:" . $e->getMessage());
}

// form受け取り
function parse_form()
{
    global $in;

    $param = array();
    if (isset($_POST) && is_array($_POST)) {
        $param += $_POST;
    }

    foreach ($param as $key => $val) {
        $enc = mb_detect_encoding($val);
        $val = mb_convert_encoding($val, "UTF-8", $enc);
        $val = htmlentities($val, ENT_QUOTES, "UTF-8");
        $in[$key] = $val;
    }

    return $in;
}

// 登録
function register()
{
    global $dbh, $in;

    $sql = <<<sql
    insert into users (id,movie,release_date,end_date,time,screen,genru,price,flag)values(?,?,?,?,?,?,?,?,?);
sql;

    $flag = 1;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $in["id"]);
    $stmt->bindParam(2, $in["movie"]);
    $stmt->bindParam(3, $in["release_date"]);
    $stmt->bindParam(4, $in["end_date"]);
    $stmt->bindParam(5, $in["time"]);
    $stmt->bindParam(6, $in["screen"]);
    $stmt->bindParam(7, $in["genru"]);
    $stmt->bindParam(8, $in["price"]);
    $stmt->bindParam(9, $flag);

    $stmt->execute();
    $error = $stmt->errorInfo();
    if ($error[0] !== '00000') {
        echo "エラーが発生しました：" . $error[2];
    }
}

// 編集
function update()
{
    global $dbh, $in;

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

// 削除
function delete()
{
    global $dbh, $in;

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(1, $in["id"]);
    $stmt->execute();
}

// 映画一覧表示
function movie()
{
    global $dbh, $tmpl_input;

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

    $data = file_get_contents("insert.tmpl");
    $movies = str_replace("!movies!", $block, $data);
    echo $movies;
}

// 映画検索
function search_movie($search_term)
{
    global $dbh, $tmpl_input;

    $sql = "SELECT * FROM users WHERE movie LIKE :search_term";
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':search_term', "%{$search_term}%", PDO::PARAM_STR);
    $stmt->execute();
    $rec_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($rec_list) {
        echo "<h2>検索結果</h2>";
        echo '<p><a href="admin_page2.php">映画一覧へ</a></p>';
        foreach ($rec_list as $row) {
            $data = file_get_contents($tmpl_input);
            $data = str_replace("!id!", $row['id'], $data);
            $data = str_replace("!movie!", $row['movie'], $data);
            $data = str_replace("!release_date!", $row['release_date'], $data);
            $data = str_replace("!end_date!", $row['end_date'], $data);
            $data = str_replace("!time!", $row['time'], $data);
            $data = str_replace("!screen!", $row['screen'], $data);
            $data = str_replace("!genru!", $row['genru'], $data);
            $data = str_replace("!price!", $row['price'], $data);
            echo $data;
        }
    } else {
        echo "<p>該当する映画はありません。</p>";
    }
}
?>
