<?php
//DB接続に必要な情報をまとめておきます
$dsn ="mysql:host=localhost;dbname=movie_admin;charset=utf8";
$id="testuser";
$pass="testpass";
$input =$_GET["id"];
$release_date =$_GET["release_date"];
$end_date =$_GET["end_date"];
$time =$_GET["time"];
$movie=$_GET["movie"];
$screen =$_GET["screen"];
$genru =$_GET["genru"];
$price =$_GET["price"];
$mode = $_GET["mode"];

#utf-8に統一
$enc = mb_detect_encoding($input);
$input = mb_convert_encoding($input, "UTF-8", $enc);

#クロスサイトスクリプティング対策
$input = htmlentities($input, ENT_QUOTES, "UTF-8");

#改行
$input = str_replace("\r\n", "_kaigyou_", $input);
$input = str_replace("\n", "_kaigyou_", $input);
$input = str_replace("\r", "_kaigyou_", $input);
try{
    $dbh =new PDO($dsn,$id,$pass);
    if(isset($mode) && ($mode== "register")){
        // フォームからのデータがNULLでないことを確認する
        if (!empty($input) && !empty($movie) && !empty($release_date) && !empty($end_date) && !empty($time) && !empty($screen) && !empty($genru) &&!empty($price)) {
            register();
            echo "登録完了しました。";
            echo '<p><a href="admin_page.html"> 画面を戻る。</a></p>';
            } 
    }else {
       echo "";
    }
}catch(PDOException $e){
    echo"接続失敗…";
    echo"エラー内容:". $e->getMessage();
}

function register(){
    global $dbh,$input,$movie,$release_date,$end_date,$time,$screen,$genru,$price;
    $sql =<<<sql
    insert into users (id,movie,release_date,end_date,time,screen,genre,price)values(?,?,?,?,?,?,?,?);
sql;

    $stmt =$dbh ->prepare($sql);
    $stmt->bindParam(1,$input);
    $stmt->bindParam(2,$movie);
    $stmt->bindParam(3,$release_date);
    $stmt->bindParam(4,$end_date);
    $stmt->bindParam(5,$time);
    $stmt->bindParam(6,$screen);
    $stmt->bindParam(7,$genru);
    $stmt->bindParam(8,$price);


    $stmt->execute();
    $error = $stmt->errorInfo();
    if($error[0] !== '00000') {
        echo "エラーが発生しました：" . $error[2];
    }
    
}
# エラーチェック
$error_notes="";
if(!isset($_GET["id"]) || empty($_GET["id"])){
    $error_notes.="・id未入力です。<br>";
}
if(!isset($_GET["movie"])|| $_GET["movie"] === ""){
    $error_notes.="映画名が未入力です。<br>";
}
if(!isset($_GET["release_date"])|| $_GET["release_date"] === ""){
    $error_notes.="公開日が未入力です。<br>";
}
if(!isset($_GET["end_date"])|| $_GET["end_date"] === ""){
    $error_notes.="公開終了日が未入力です。<br>";
}
if(!isset($_GET["time"])|| $_GET["time"] === ""){
    $error_notes.="上映時間が未入力です。<br>";
}
if(!isset($_GET["screen"])|| $_GET["screen"] === ""){
    $error_notes.="スクリーンが未入力です。<br>";
}
if(!isset($_GET["genru"])|| $_GET["genru"] === ""){
    $error_notes.="ジャンルが未入力です。<br>";
}
if(!isset($_GET["price"])|| $_GET["price"] === ""){
    $error_notes.="priceが未入力です。<br>";
}

# エラーが存在する場合
if($error_notes !== "") {
    error($error_notes);
}

function error($error_message) {
    echo "<br>";
    echo "<div style='color: red;'><strong>エラーが発生しました:</strong><br>" . $error_message . "</div>";
}

$getinfo = $_GET;
function conf_form(){
    global $dbh;
    global $getinfo;
    $sql = "select * from users";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();


  global $input;
  global $movie;
  global $release_date;
  global $end_date;
  global $time;
  global $screen;
  global $genru;
  global $price;

#テンプレート読み込み
$conf = fopen("admin_page.tmpl", "r") or die("Unable to open file!");
$size = filesize("admin_page.tmpl");
$data = fread($conf , $size);
fclose($conf);
$block = "";

while($row = $stmt->fetch()){
    $id = $row['id'];
    $movie = $row['movie'];
    $release_date = $row['release_date'];
    $end_date = $row['end_date'];
    $time = $row['time'];
    $screen = $row['screen'];
    $genru = $row['genru'];
    $price = $row['price'];

}

  # 文字置き換え
$data = str_replace("!id!", $input, $data);
$data = str_replace("!movie!", $movie, $data);
$data = str_replace("!release_date!", $release_date, $data);
$data = str_replace("!end_date!", $end_date, $data);
$data = str_replace("!time!", $time, $data);
$data = str_replace("!screen!", $screen, $data);
$data = str_replace("!genru!", $genru, $data);
$data = str_replace("!price!", $price, $data);

$block .=$data;
# 表示
echo $data;
}

$fh_stock = fopen("admin_page.html","r+");
$fs_stock = filesize("admin_page.html");
$top = fread($fh_stock,$fs_stock);
fclose($fh_stock);

$top = str_replace("!block!",$block, $top);
echo $top;


