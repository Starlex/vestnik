<?php
require_once 'block/auth.php';
require_once 'block/fun.lib.php';
require_once 'block/db.php';

if(isset($_FILES['zip'])){
    $errors = array();
    $zip = new ZipArchive();

    if(strtolower(pathinfo($_FILES['zip']['name'],PATHINFO_EXTENSION)) !== "zip"){
        $errors[] = "Данный файл не является zip-архивом";
    }

    if($_FILES['zip']['size'] == 52428800){
        $errors[] = "Данный файл превышает максимально возможный размер (50MB)";
    }

    if($zip->open($_FILES['zip']['tmp_name']) === false){
        $errors[] = "Не удалось открыть zip файл";
    }

    if(empty($errors)){
        $dir = extractZip($_FILES['zip']['tmp_name']);
        $x2d = xml2db($dir);
        if($x2d !=true)
            $errors[] = $x2d;
    }
}

?>

<?php require_once 'block/header.php';?>
<?php require_once 'block/menu.php';?>

<div id="content">
<?php

if(isset($errors)){
    if(empty($errors)){
        echo "Файлы были успешно загружены";
    }
    else{
        foreach($errors as $error){
            echo '<p>', $error, '</p>';
        }
    }
}

?>
<form name="zipUpload" action="" method="post" enctype="multipart/form-data">
    <label><h3>Выберите архив для загрузки:</h3></label>
    <p><input name="zip" type="file"></p>
    <input name="submit" type="submit" value="Загрузить">
</form>
</div>

<?php require_once '../block/footer.php';?>