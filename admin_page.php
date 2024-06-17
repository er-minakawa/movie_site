<?php
//DB接続に必要な情報をまとめておきます
$dsn = "mysql:host=localhost;dbname=movies_admin;charset=utf8";
$id = "testuser";
$pass = "testpass";

//テンプレートファイル
$tmpl_input = "movie.tmpl";

// #改行
// $input = str_replace("\r\n", "_kaigyou_", $input);
// $input = str_replace("\n", "_kaigyou_", $input);
// $input = str_replace("\r", "_kaigyou_", $input);


//表示
parse_form();//＄in配列にデータを入れる

try {
    $dbh = new PDO($dsn, $id, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $in = parse_form(); // $in 配列の準備

    
    if(isset($in) && $in['mode'] == 'register'){
        register();
        header('location:admin_page2.php');
        exit();
    }
    if(isset($in) && $in['mode'] == 'edit'){
        update();
    }
    if(isset($in) && $in['mode'] == 'delete'){
        delete();
    }
    movie();
} catch (PDOException $e) {
    die("PDO Error:" . $e->getMessage());
}


//form受け取り
function parse_form()
{
    global $in;

    $param = array();
    if (isset($_POST) && is_array($_POST)) {
        $param += $_POST;
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
    // var_dump($in);
    return $in;
}


//登録
function register()
{
    global $dbh, $in,$input, $movie, $release_date, $end_date, $time, $screen, $genru, $price,$flag;
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

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $dbh->prepare($sql);

    $stmt->bindParam(1, $in["id"]);

    $stmt->execute();
}

function movie()
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
    $data = file_get_contents("insert.tmpl");
    $movies = str_replace("!movies!", $block, $data);
    echo $movies;
}
function serch()
{

}
