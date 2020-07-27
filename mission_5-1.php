<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
    
<body>
    <form action ="" method="post">
        <br>
        【投稿欄】<br>
        名前：<input type ="text" name ="name" placeholder ="名前">
        <br>
        コメント：<input type ="text" name ="comment" placeholder ="コメント">
        <br>
        pass：<input type ="text" name ="pass" placeholder ="パスワード">
        <br>
        <input type ="submit" value ="送信">
        <br>
        <br>
        【削除欄】<br>
        削除番号：<input type ="number" name ="deleteNO" placeholder ="削除番号">
        <br>
        pass：<input type ="text" name ="dpass" placeholder ="パスワード">
        <br>
        <input type ="submit" value ="削除">
        <br>
        <br>
        【編集欄】<br>
        編集番号：<input type ="number" name ="editNO" placeholder ="編集番号">
        <br>
        pass：<input type ="text" name ="epass" placeholder ="パスワード">
        <br>
        <input type ="submit" value ="編集">
    </form>

<?php
    // DBの接続設定
    $dsn='データベース名';
    $user='ユーザー名';
    $password='パスワード';
    // データベース操作でエラーが出たら警告表示
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

    // テーブル作成（テーブル名：keijiban）
    $sql = "CREATE TABLE IF NOT EXISTS keijiban"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    ."date datetime,"
    ."pass char(32)"
	.");";
    $stmt = $pdo->query($sql);
    $comment =(string)filter_input( INPUT_POST,"comment");
    
    // データ入力
    if(!empty($_POST["comment"]) && !empty($_POST["name"])){
        // データの編集
        if(!empty($_POST["editNO"]) && !empty($_POST["epass"])){
            $sql = 'SELECT * FROM keijiban';
	        $stmt = $pdo->query($sql);
	        $results = $stmt->fetchAll();
	        foreach ($results as $row){
                //パスワードとidの一致確認
                if($_POST["epass"] == $row["pass"] && $_POST["editNO"] == $row["id"]){
                    $id = $_POST["editNO"];// 変更する投稿番号
                    $name = $_POST["name"];// 変更する名前
                    $comment = $_POST["comment"];// 変更するコメント
                    $date =date("Y/m/d H:i:s");// 変更する日付

                    $sql = "UPDATE keijiban SET name=:name,comment=:comment,date=:date WHERE id=:id";
                    $stmt = $pdo -> prepare($sql);
                    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt -> execute();
                }
            }
        // 新規投稿
        }else{
            if(!empty($_POST["pass"])){
                // insert文でデータ入力
                $sql = $pdo -> prepare("INSERT INTO keijiban (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_INT);
	            $name = $_POST["name"];
                $comment = $_POST["comment"];
                $date = date("Y/m/d H:i:s");
                $pass =$_POST["pass"];

                $sql -> execute();
            }
        }
    }

    $deleteNO =(string)filter_input( INPUT_POST,"deleteNO");
    // データを削除
    if(!empty($deleteNO) && !empty($_POST["dpass"])){
        $sql = 'SELECT * FROM keijiban';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
            //パスワードとidの一致確認
            if($_POST["dpass"] == $row["pass"] && $_POST["deleteNO"] == $row["id"]){
                $id = $_POST["deleteNO"];//  削除する投稿番号
                $sql = 'delete from keijiban where id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        } 
    }

    //最新情報を表示
    $sql = 'SELECT * FROM keijiban';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
        foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].'<br>';
            echo $row["date"]."<br>";
            echo "<hr>";
        }

?>

</body>
</html>

