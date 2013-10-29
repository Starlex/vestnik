<?php
require_once 'block/auth.php';
require_once 'block/header.php';
require_once 'block/menu.php';
require_once 'block/db.php';
?>


<div id="content">
    <?php
    if(file_exists('index.html'))
    {
        $fp = fopen('content/index.html', 'r+');
    }

    $files = scandir('content');
    array_shift($files);
    array_shift($files);
    ?>
    <!-- Форма выбора страницы для редактирования начало-->
    <form name="redact" method="post" action="">
        <p><label>Выберите файл для редактирования:</label></p>
        <p><select name="names">
           <option value="" selected>-- не выбрано --</option>
               <?php
               foreach($files as $file)
               {
                   $file = str_ireplace(".html", "", $file);
                   $sql = "SELECT name_rus, name_eng, red FROM vestnik_page WHERE name_eng = '$file'";
                   $query = mysql_query($sql) or die(mysql_error());
                   while($row = mysql_fetch_assoc($query))
                   {
                       if($row['red'] == 1)
                       {
                           echo '<option value="'.$file.'">'.str_replace("_", " ", $row['name_rus']).'</option>';
                       }
                   }
               }
               ?>
        </select>&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="redsubmit" type="submit" value="Редактировать"></p>
        </form>
    <!-- Форма выбора страницы для редактирования конец-->

    <!-- Форма редактирования содержимого страницы начало-->
    <?php
    if(isset($_POST['redsubmit']) and ($_POST['names'] != ""))
    {
        $names = $_POST['names'];
        $sql = "SELECT name_rus, name_eng FROM vestnik_page WHERE name_eng = '$names'";
        $query = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        $name_rus = str_replace("_", " ", $row['name_rus']);
    ?>
        <form name="redcon" method="post" action="">
            <p><label>Редактируемая страница: <span><?=$name_rus;?></span></label>
            <input name="name_eng" type="hidden" value="<?=$row['name_eng'];?>">
            <input style="float:right;" name="submit" type="submit" value="Сохранить"></p>
            <p><textarea class="ckeditor" name="content" id="txtContent" rows="4" cols="30">
            <?php
            $fp = fopen('content/'.$names.'.html', 'r');
            if($fp)
            {
                while(!feof($fp))
                    {
                        $text = fgetc($fp);
                        echo $text;
                    }
            }
            fclose($fp);
            ?>
            </textarea></p>
        </form>
    <?php
    }
    if(isset($_POST['submit']))
    {
        $filename = $_POST['name_eng'];
        $content = stripslashes($_POST['content']);
        $fp = fopen('content/'.$filename.'.html', 'r+');
        if($fp)
        {
            if(fwrite($fp, $content)){
                echo '<label>Данные успешно сохранены.</label>';
            }
            else if(fwrite($fp, $content) == 0){
                fclose($fp);
                $fp = fopen('content/'.$filename.'.html', 'w+');
                echo '<label>Данные успешно сохранены.</label>';
            }
            else
            {
                echo '<label>Ошибка при сохранении данных. Повторите попытку позже.</label>';
            }
        }
        fclose($fp);
    }
    ?>

    <!-- Форма редактирования содержимого страницы конец-->

</div>

<?php require_once '../block/footer.php';?>