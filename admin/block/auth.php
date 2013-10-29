<?php
session_start();

if(isset($_SESSION['login'])){
    $login = $_SESSION['login'];
}
if(!$login){
    header('Location:login.php');
}