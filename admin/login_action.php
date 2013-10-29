<?php
session_start();
require_once 'block/db.php';

if(isset($_POST['submit'])){
    $login = mysql_real_escape_string(strip_tags(trim($_POST['login'])));
    $password = mysql_real_escape_string(strip_tags(trim(md5($_POST['password']))));

    $sql = "SELECT count(*) FROM vestnik_users WHERE login = '$login' AND password = '$password'";
    $query = mysql_query($sql);

    $num = mysql_fetch_assoc($query);

    if($num == 0){
        echo "<h3>Неверные логин и/или пароль.</h3>";
    }
    else{
        $_SESSION['login'] = $login;
        header('Location:index.php');
    }
}

?>