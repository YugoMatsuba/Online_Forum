<?php 
//押されるまではpostには何もない。だからポストのなんかを指定してもデータ取れない。
//submitされてやっと、キーと値が配列の中に入る。
//だってnameを定義した後にpost出力しても、エラー出るから、
//キーはあるけど値何もないっていうわけじゃない
//配列の中にそのキーが定義されていない

//押された時に作るんじゃなくて、押された時にデータベースに追加されるようにする。

//　INSERT INTO `forum-data` (`id`, `username`, `comment`, `postDate`) VALUES ('3', 'yugo3', 'jfeijfefjeifjei', '2023-04-12 06:34:38.000000');
// これがSQLの文。
date_default_timezone_set("Asia/Tokyo");
//この関数はphpにもともと備え付けられている関数でphp内で扱う時間をどこ時間か設定できる。

$db = new PDO ("mysql:host=localhost;dbname=form-php","Yugo","asdfghjkl555666");

//過去のデータの取得
$getData = $db->query("SELECT * FROM `forum-data`");
$commentArray = $getData->fetchAll();
//PDOっていう設計図にはqueryが書かれてて、できたインスタンスは元々クエリーメソッドを持っている
//queryの中にsql文を書いて、データベースからデータを取得して配列として返せる。

$insert = $db->prepare ("INSERT INTO `forum-data` (`username`, `comment`, `postDate`) VALUES (:username,:comment,:postDate);");

//ifの実行された時点でもうすでに$_POSTの配列にデータが入っているから、それらのデータを使える
//ボタン押されたら、mysqlのデータベースに追加される。
if (!empty($_POST ["submitButton"]) && !empty($_POST["username"]) && !empty ($_POST["body"]))
{
     $postDate = date("Y-m-d H:i:s");
     //date関数はそれが実行された時の時間を返す。これはボタン押された時に発動するから提出された時の時間を返す。
     $insert->bindParam(":username",$_POST["username"],PDO::PARAM_STR);
     $insert->bindParam(":comment",$_POST["body"],PDO::PARAM_STR);
     $insert->bindParam(":postDate",$postDate,PDO::PARAM_STR);

     $insert->execute();}

elseif (!empty($_POST ["submitButton"]) && empty($_POST["username"]) && empty ($_POST["body"])){
        echo "未入力です";
   }

elseif (!empty($_POST ["submitButton"]) && empty($_POST["username"])){
     echo "名前を入力してください";
}
elseif (!empty($_POST ["submitButton"]) && empty($_POST["body"])){
    echo "コメントを書き込んでください";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class = "title">PHP掲示板</h1>
    <hr>
    <div class ="boardWrapper">
        <section>
        <?php foreach ($commentArray as $comment):?>
        <article>
           <div class = "nameArea">
              <span>名前：</span>
              <p class = "username"><?php echo $comment["username"] ?>&nbsp&nbsp&nbsp</p>
              <time><?php 
              echo $comment["postDate"];
              ?></time>
           </div>    
           <p class ="comment"><?php echo $comment["comment"] ?></p>
        </article>
        <?php endforeach?> 
        <!-- 塊のところ（section）でループを使っているから、塊が複数できるようにしている。
        そしたらしっかり、改行された過去のコメントが異なる塊として表示される。
        過去の塊の下に、ボタン押されたら、新しい塊ができるようにしている-->
        <?php if(!empty($_POST ["submitButton"]) && !empty($_POST["username"]) && !empty ($_POST["body"])): ?>
            <article>
           <div class = "nameArea">
              <span>名前：</span>
              <p class = "username">
                <?php 
              $getData = $db->query("SELECT * FROM `forum-data`");
              $commentArray = $getData->fetchAll();
              $newData= end ($commentArray);
              ?>
              <?php echo ($newData["username"]);
              //このキーの名前は、データベースで設定したようになる。データベースで設定した値。
              ?>&nbsp&nbsp&nbsp</p>
              <time><?php echo $newData["postDate"] ?></time>
           </div>
           <!-- divはブロックやからdivで囲われたとこはまず -->
           <p class ="comment"><?php echo $newData["comment"] ?></p>
        </article>   
        <?php endif?>  
        </section>
 
        <form class ="formWrapper" method="POST">
            <div>
                <input type="submit" value="書き込む" name ="submitButton">
                <label for="">名前：</label>
                <input type="text" name ="username">
                <!--この中のタグは全てインラインやから横に並ぶ
            このディブタグは横に並べる奴らの塊 -->
            </div>
            <div>
                 <textarea class = "commentTextArea" name ="body"></textarea>
            </div>    
        </form>

    </div>
<!-- hrタグは閉じタグ要らなくて、線を書いてくれる
ブロックタグやから一つ改行して線が出る。 -->
    
</body>
</html>



