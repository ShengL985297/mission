<!--完了！-->
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>m5-1</title>
    </head>
        <body>
        <?php
        //データベース設定
        $dsn = 'データベース名'; 
        $user = 'ユーザー名';
	$password = 'パスワード';
        $dbh = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::ATTR_EMULATE_PREPARES => false,]);
        //テーブル作成
        $sql = "CREATE TABLE IF NOT EXISTS m5_1" 
	    ." ("
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
	    . "name char(32),"
            . "comment TEXT,"
            . "date DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,"
            . "password TEXT"
	    .");";
        $stmt = $dbh->query($sql);

        //送信が押された時：データ入力
        //新規投稿
        if (isset($_POST['submit'])) {
            $name = $_POST["name"];
            $comment = $_POST["comment"];
            $date =date("Y/m/d/ H:i:s");
            $password =$_POST["password"];
            $eddit_Number =$_POST["eddit_Number"];//新規投稿と編集の見分け
            if ($name!="" && $comment!="" && $eddit_Number==""){
                $sql = $dbh -> prepare("INSERT INTO m5_1 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql->execute(); 
            }elseif ($name=="" && $name!="") {
                echo "名前を入力してください";
            }elseif ($name!="" && $comment=="") {
                echo "コメントを入力してください";
            }
        }

        //削除が押された時：該当データ削除
        if (isset($_POST['delete'])) {
            $del_num = $_POST["deleteNumber"];
            $del_pass = $_POST["deletePassword"];
            if(isset($del_num,$del_pass)){
                if ($del_num!=="" && $del_pass!=="") {
                    $sql = 'SELECT * FROM m5_1';
                    $stmt = $dbh->query($sql);
                    $results = $stmt->fetchAll();
                    foreach($results as $row){
                        if ($del_num==$row['id'] && $del_pass==$row['password']) {
                            $sql = 'delete from m5_1 where id=:id';
                            $stmt = $dbh->prepare($sql);
	                        $stmt->bindParam(':id', $del_num, PDO::PARAM_INT);
	                        $stmt->execute();
                        }
                    }
                }
            }
        }

        //編集が押された時：該当データ表示→編集
        //表示
        if(isset($_POST['eddit'])){
            $edi_num = $_POST["edditNumber"];//フォームに入力された番号を$edi_numとする
            $edi_pass = $_POST["edditPassword"];
            if(isset($edi_num,$edi_pass)){
                if($edi_num!=="" && $edi_pass!==""){
                    $sql = 'SELECT * FROM m5_1';
                    $stmt = $dbh->query($sql);
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        //$rowの中にはテーブルのカラム名が入る
                        if($edi_num==$row['id'] && $edi_pass==$row['password']){
                            $edinum=$row["id"];
                            $ediname=$row["name"];
                            $edicom=$row["comment"];
                            $edipass=$row["password"];
                        }   
                    }
                }
            }
        }
        //編集(送信が押された時：データ入力)
        if (isset($_POST['submit'])) {
            $edi_name = $_POST["name"];
            $edi_comment = $_POST["comment"];
            $date =date("Y/m/d/ H:i:s");
            $edi_password = $_POST["password"];
            $eddit_Number =$_POST["eddit_Number"];
            if (isset($edi_name,$edi_comment,$eddit_Number)) {
                if ($edi_name!="" && $edi_comment!="" && $eddit_Number!==""){
                    $sql = 'UPDATE m5_1 SET name=:name,comment=:comment,date=:date,password=:password  WHERE id=:id';
                    $stmt = $dbh -> prepare($sql);
                    $stmt -> bindParam(':name', $edi_name, PDO::PARAM_STR);
                    $stmt -> bindParam(':comment', $edi_comment, PDO::PARAM_STR);
                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                    $stmt -> bindParam(':id', $eddit_Number, PDO::PARAM_STR);
                    $stmt -> bindParam(':password', $edi_password, PDO::PARAM_STR);
                    $stmt->execute(); 
                }
            }
        } 
        ?>
        
        <form action="m5-1.php" method="post">
            <!--投稿用フォーム-->
            <input type="text" name="name" placeholder ="名前" value=<?php if(isset($ediname)){echo $ediname;}?>><br>
            <input type="text" name="comment" placeholder ="コメント" value=<?php if(isset($edicom)){echo $edicom;}?>>
            <input type="hidden" name="eddit_Number" placeholder="編集番号" value=<?php if(isset($edinum)){echo $edinum;}?>><br>
            <input type="text" name="password" placeholder="パスワード" value=<?php if(isset($edipass)){echo $edipass;}?>><br>
            <input type="submit" name="submit" value="送信"><br>
            <!--削除番号指定用フォーム-->
            <input type="number" name="deleteNumber" placeholder="削除対象番号"><br>
            <input type="text" name="deletePassword" placeholder="パスワード"><br>
            <input type="submit" name="delete" value="削除"><br>
            <!--編集番号指定用フォーム-->
            <input type="number" name="edditNumber" placeholder="編集対象番号"><br>
            <input type="text" name="edditPassword" placeholder="パスワード"><br>
            <input type="submit" name="eddit" value="編集"><br>
        </form>
        
        <?php
        //データレコード抽出、表示
        $sql = 'SELECT * FROM m5_1'; //4-6の表示させる機能
	$stmt = $dbh->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';               
            echo "<hr>";
        }
        ?>
    </body>
</html>
