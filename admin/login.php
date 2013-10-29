<?php
session_start();
require_once 'block/header.php';
require_once 'block/menu.php';
require_once 'block/db.php';

if(isset($_POST['submit'])){
    $login = mysql_real_escape_string(strip_tags(trim($_POST['login'])));
    $password = mysql_real_escape_string(strip_tags(trim(md5($_POST['password']))));

    $sql = "SELECT count(*) FROM vestnik_users WHERE login = '$login' AND password = '$password'";
    $query = mysql_query($sql);

    $num = mysql_fetch_assoc($query);

    if($num['count(*)'] == 0){
        echo "<h3>Неверные логин и/или пароль.</h3>";
    }
    else{
		$_SESSION['login'] = $login;
		echo "<h3>Вы успешно авторизировались.</h3>";
    }
}
?>

<div id="content">
    <form name="loginForm" method="post" action="login_action.php">
        <p><label>Введите логин:</label></p>
        <p><input name="login" type="text" size="40"></p>
        <p><label>Введите пароль:</label></p>
        <p><input name="password" type="password" size="40"></p>
        <p><input name="submit" type="submit" value="Войти"></p>
    </form>
</div>

<?php require_once '../block/footer.php';?>