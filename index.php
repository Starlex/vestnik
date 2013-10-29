<?php
require_once 'block/header.php';
require_once 'block/menu.php';
require_once 'admin/block/db.php';
?>

<div id="content">

<?php
if(isset($_GET['nm']))
{
    $name = $_GET['nm'];
}
else{
    $name = 'index';
}
if(($name == 'archive') and !isset($_GET['j'])){
    $dir = scandir('./journals');
    array_shift($dir);
    array_shift($dir);
    foreach($dir as $journal){
        $journal = '../journals/'.$journal;
        $sql_j = "SELECT number, dateUni FROM vestnik_journal WHERE dirname = '$journal'";
        $query_j = mysql_query($sql_j);
        while($row_j = mysql_fetch_assoc($query_j)){
            $number = $row_j['number'];
            $dateUni = $row_j['dateUni'];
            echo '<a href="index.php?nm=archive&j='.$journal.'"><h3>Номер журнала: '.$number.' ('.$dateUni.' год)</h3></a>';
        }
    }
}
if(($name == 'archive') and isset($_GET['j'])){
        $sql = "SELECT * FROM vestnik_article";
        $query = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($query)){
            $id_a = $row['id_a'];
            $title_rus = str_replace("_", " ",$row['title_rus']);
            $title_eng = str_replace("_", " ",$row['title_eng']);
            $abstract_rus = str_replace("_", " ",$row['abstract_rus']);
            $abstract_eng = str_replace("_", " ",$row['abstract_eng']);
            $udk = $row['udk'];
            $file = $row['file'];

            $sql_a = "SELECT surname_rus, initials_rus, surname_eng, initials_eng FROM vestnik_author WHERE id_a = $id_a";
            $query_a = mysql_query($sql_a) or die(mysql_error());
            $row_a = mysql_fetch_assoc($query_a);
                $surname_rus = $row_a['surname_rus'];
                $initials_rus = str_replace("_", " ", $row_a['initials_rus']);
                $surname_eng = $row_a['surname_eng'];
                $initials_eng = str_replace("_", " ", $row_a['initials_eng']);
            ?>
            <table>
                <tr>
                    <td class="file"><a href="<?='./journals/'.$date_add.'/'.$file;?>"><img src="./view/pdf.png"></a></td>
                    <td class="name">
                        <?=$title_rus;?><br>
                        <span class="udk">УДК: <?=$udk?></span><br>
                        <span class="author"><?=$surname_rus.' '.$initials_rus?></span>
                    </td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td class="spoiler">
                        <div class="spoiler_style" onClick="open_close('spoiler<?=$id_a?>')">
                            <a href="#spoiler<?=$id_a?>"><b>Подробнее...</b></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #ffffff;">
                        <div id="spoiler<?=$id_a?>" style="display:none; background:#f5f5f5;">
                            <!-- контент -->
                            <h3>Описание:</h3>
                            <p class="reviev"><?=$abstract_rus;?></p>
                            <h3>Описание на английском языке:</h3>
                            <p class="reviev"><b>Title:</b> <?=$title_eng;?></p>
                            <p class="reviev"><b>UDK:</b> <?=$udk;?></p>
                            <p class="reviev"><?=$abstract_eng;?></p>
                            <p class="reviev"><b>Author:</b> <?=$surname_eng." ".$initials_eng;?></p>
                        </div>
                    </td>
                </tr>
            </table>
        <?php
        }
}
    else{
        $fp = fopen('admin/content/'.$name.'.html', 'r');
        if($fp)
        {
            while(!feof($fp))
            {
                $text = fgetc($fp);
                echo $text;
            }
        }
        fclose($fp);
    }
?>

</div>

<?php require_once 'block/footer.php';?>