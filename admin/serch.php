<?php
    // データベースに接続
    $dsn = "mysql:host=localhost;dbname=movies_admin;charset=utf8";
    $username = "testuser";
    $password = "testpass";

    try {
        $dbh = new PDO($dsn, $username, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }

    if (isset($_POST["search"])) {
        // フォームからの検索キーワードを取得
        $search_name = isset($_POST["movie"]) ? $_POST["movie"] : '';
        // SQLクエリを構築して実行
        $sql = "SELECT * FROM users WHERE movie LIKE '%{$search_name}%'";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $rec_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 検索結果を表示
        if (!empty($rec_list)) {
            echo "<h3>検索結果</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>映画名</th><th>上映時間</th><th>スクリーン</th></tr>";
            foreach ($rec_list as $rec) {
                echo "<tr>";
                echo "<td>{$rec['id']}</td>";
                echo "<td>{$rec['movie']}</td>";
                echo "<td>{$rec['time']}</td>";
                echo "<td>{$rec['screen']}</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo '<p><a href="serch.html">前の画面に戻る</a></p>';
        } else {
            echo "<p>該当する映画は見つかりませんでした。</p>";
        }
    }

    // データベース接続を閉じる
    $dbh = null;
?>
