<html><head><title>Message Board</title></head>
<?php
session_start();
if(isset($_GET['logout']))
{
  if($_GET['logout']==1)
  {
    session_unset();
    header("Location:login.php");
    exit;
  }
}
if(isset($_POST['message']))
{
  $message=$_POST['message'];
  if($message!=''&&isset($_POST['Post']))
  {
    $id=uniqid();
    $postedby=$_SESSION['username'];
    try
    {
      $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $dbh->beginTransaction();
      $dbh->exec('insert into posts(id,postedby,datetime,message) values("'.$id.'","'.$postedby.'",NOW(),"'.$message.'")')
        or die(print_r($dbh->errorInfo(), true));
      $dbh->commit();
    }
    catch (PDOException $e)
    {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }
  }
  else if($message!=''&&isset($_POST['reply']))
  {
    try
    {
      $id=uniqid();
      $postedby=$_SESSION['username'];
      $replyto=$_POST['reply'];
      $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
      $dbh->beginTransaction();
      $dbh->exec('insert into posts(id,replyto,postedby,datetime,message) values("'.$id.'","'.$replyto.'","'.$postedby.'",NOW(),"'.$message.'")')
        or die(print_r($dbh->errorInfo(), true));
      $dbh->commit();
    }
    catch (PDOException $e)
    {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }
  }
}
?>
<body>
<form action="board.php">
<input type="hidden" name="logout" value="1"/>
<input type="submit" value="logout">
</form>
<b>Type Message below :</b>
<form action="board.php" method="POST" id="form">
<textarea rows="5" cols="50" name="message">
</textarea>
<input type="submit" value="Post" name="Post">
</form>
<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
try 
{
  $dbh = new PDO("mysql:host=127.0.0.1:3306;dbname=board","root","",array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  $dbh->beginTransaction();
  $stmt = $dbh->prepare('select p.id,p.replyto,p.postedby,p.datetime,p.message,u.fullname from posts as p join users as u on p.postedby=u.username order by datetime DESC');
  $stmt->execute();
  print "<pre>";
  while ($row = $stmt->fetch())
  {
    if($row['replyto']!="")
    {
      ?>
      <fieldset><b>This is Reply to Message ID: </b><?php print $row['replyto'] ?><br/>
      <b>Message: </b><?php print $row['message'] ?><br/>
      <b>Message ID: </b><?php print $row['id'] ?>
      <b>Username: </b><?php print $row['postedby'] ?>
      <b>Full Name: </b><?php print $row['fullname'] ?>
      <b>Date and Time: </b><?php print $row['datetime'] ?><br/>
      <button type="submit" form="form" value="<?php print $row['id'] ?>" name="reply">Reply</button>
      </fieldset>
      <?php
    }
    else
    {
      ?>
      <fieldset><b>Message: </b><?php print $row['message'] ?><br/>
      <b>Message ID: </b><?php print $row['id'] ?>
      <b>Username: </b><?php print $row['postedby'] ?>
      <b>Full Name: </b><?php print $row['fullname'] ?>
      <b>Date and Time: </b><?php print $row['datetime'] ?><br/>
      <button type="submit" form="form" value="<?php print $row['id'] ?>" name="reply">Reply</button>
      </fieldset>
      <?php
    }
  }
  print "</pre>";
}
catch (PDOException $e)
{
  print "Error!: " . $e->getMessage() . "<br/>";
  die();
}
?>
</body>
</html>