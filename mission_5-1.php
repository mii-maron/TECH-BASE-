<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset ="UTF-8">
	<title>mission_5-1</title>
</head>
<body>
	<?php
	//DB接続設定
	$dsn = 'データベース名';
	//username	
	$user = 'ユーザーネーム';
	//password
	$password = 'パスワード';
	//PHP Data Objects
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

/*tablem51を削除する
        $sql = 'DROP TABLE tablem51';
        $stmt = $pdo->query($sql);*/

	//mysql上で、tablem51という表がなければつくる
	$sql = "CREATE TABLE IF NOT EXISTS tablem51"
	//一行目を名前とコメントで設定する、整数で番号を割り振る
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"//文字数は文字列、半角英数で32文字まで
	."str TEXT,"//コメントを入れる。文字列、長めの文章も入る
	."date DATETIME,"//日付を入れる
	."password char(32)"//パスワードを入れる
	.");";
	//データベースに送る
	$stmt = $pdo ->query($sql);	

	$editid="";
	$editname="";
	$editstr="";
	$editpw="";		
//編集番号が入力されている時
if(!empty($_POST["editid"])){
	$id=$_POST["editid"];
	$editpass=$_POST["editpass"];
	//表の中の内容を取得
	$sql = 'SELECT * FROM tablem51 WHERE id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$results = $stmt->fetchAll();
	//ループ開始
	foreach($results as $row){
		//パスワードが正しく、投稿番号とIDが一致する時
		if($id==$row['id'] && $editpass==$row['password']){
                       	//次の変数を書き込む
			$editid=$row['id'];
                       	$editname=$row['name'];
                       	$editstr=$row['str'];
			$editpw=$row['password'];
		}else{
			echo "エラーがあります<br>";
		}
	}
}		
?>

	<!--フォーム作成-->
	<form action="" method="post">
            <p>名前<br>
            <input type="text" name="name" placeholder="名前を入力してください"
            value="<?php echo $editname; ?>">
            <br>コメント<br>
            <input type="text" name="str" placeholder="コメントを入力してください"
            value="<?php echo $editstr;?>">
            <input type="text" name="password" placeholder="パスワード"
	    value="<?php echo $editpw;?>">
            <input type="submit" name="submit" value="送信"><br>
            <input type="hidden"name="ifedit"value="<?php echo $editid;?>"></p>
            <p>削除<br>
            <input type="number"name="deleteid" placeholder="削除番号">
            <input type="text" name="deletepass" placeholder="削除パスワード">
            <input type="submit"name="deletesubmit"value="削除"></p>
            <p>編集<br>
            <input type="number"name="editid"placeholder="編集番号">
            <input type="text" name="editpass"placeholder="編集パスワード">
            <input type="submit"name="editsubmit"value="編集"></p>
        </form>

<?php 
//送信ボタンが押された時
if(isset($_POST["submit"])){
	$name=$_POST["name"];
	$str=$_POST["str"];
	$password=$_POST["password"];
	$date = date ("Y/m/d H:i:s");
        	date_default_timezone_set('Asia/Tokyo');

	//名前とコメント・パスワードが記入されている時
	if(!empty($_POST["name"]) && !empty($_POST["str"]) && !empty($_POST["password"])){
            	//隠れフォームに数字が存在しない時（通常通り入力されている）
		if(empty($_POST["ifedit"])){
			//そのままフォームに入力されたものを書き込む
			$sql = $pdo -> prepare("INSERT INTO tablem51(name, str, password, date) 
						VALUES (:name, :str, :password, :date)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':str', $str, PDO::PARAM_STR);
			$sql -> bindParam(':password', $password, PDO::PARAM_STR);
			$sql -> bindParam(':date', $date, PDO::PARAM_STR);
			$sql -> execute();
		
		//隠れフォームに数字が存在する時(編集ボタンが押された)
		}else{
			$id =$_POST["ifedit"];
				//UPDATE文で編集する
    				$sql = 'UPDATE tablem51 SET name=:name, str=:str, date=:date
						WHERE id=:id';
    				$stmt = $pdo->prepare($sql);
    				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
    				$stmt->bindParam(':str', $str, PDO::PARAM_STR);
				$stmt->bindParam(':date', $date, PDO::PARAM_STR);
    				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    				$stmt->execute();
		}	
	}
}


//削除番号が入力されている時
if(!empty($_POST["deleteid"])){
	$id=$_POST["deleteid"];
	$deletepass=$_POST["deletepass"];
	
	//表の中の内容を取得
	$sql = 'SELECT * FROM tablem51 WHERE id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	$dresults = $stmt->fetchAll();
	//ループ開始
	foreach($dresults as $drow){
		//Idが削除番号と一致し、パスワードが正しい時
		if($id==$drow['id'] && $deletepass==$drow['password']){
				//DELETE文を使って削除する
    				$sql = 'DELETE from tablem51 where id=:id';
    				$stmt = $pdo->prepare($sql);
    				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    				$stmt->execute();	
		}else{
			echo "エラーがあります<br>";
		}
	}			
}
$sql = 'SELECT * FROM tablem51';
		$stmt = $pdo->query($sql);
		$results = $stmt->fetchAll();
		foreach($results as $row){
			echo $row['id'].',';
			echo $row['name'].',';
			echo $row['str'].',';
			echo $row['date'].'<br>';
			echo "<hr>";
		}

?>
</body>
</html>