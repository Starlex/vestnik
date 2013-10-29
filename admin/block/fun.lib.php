<?php
function rus2translit($string)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',    'ы' => 'y',   'ъ' => ' ',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}


function extractZip($zipFile = '', $dirFromZip = '') {
    // Папка для распаковки.
    $time = time();
    $date = date("Y-m-d", $time);
    $zipDir = '../journals/'.$date;

    $zip = zip_open($zipFile);

    if ($zip) {
        while ($zip_entry = zip_read($zip)) {
            // Перекодируем с CP866 в CP1251
            $completePath = $zipDir .'/'. rus2translit(dirname(iconv('CP866', 'UTF-8', zip_entry_name($zip_entry))));
            $completeName = $zipDir .'/'. rus2translit(iconv('CP866', 'UTF-8', zip_entry_name($zip_entry)));

            if (!file_exists($completePath) && preg_match('#^' . $dirFromZip .'.*#', dirname(zip_entry_name($zip_entry)))) {
                $tmp = '';
                foreach (explode('/', $completePath) as $k) {
                    $tmp .= $k . '/';
                    if (!file_exists($tmp)) {
                        @mkdir($tmp, 0777);
                    }
                }
            }

            if (zip_entry_open($zip, $zip_entry, "r")) {
                if (preg_match( '#^' . $dirFromZip . '.*#', dirname(zip_entry_name($zip_entry)))) {
                    if ($fd = @fopen($completeName, 'w+')) {
                        fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                        fclose($fd);
                    } else {
                        mkdir($completeName, 0777);
                    }

                    zip_entry_close($zip_entry);
                }
            }
        }

        zip_close($zip);
    }

    return $zipDir;
}

function xml2db($dir)
{
    $xmlFind = scandir($dir);
    array_shift($xmlFind);
    array_shift($xmlFind);
    foreach($xmlFind as $xmlF){
        $ext = pathinfo($xmlF, PATHINFO_EXTENSION);
        if($ext == "xml"){
            copy($dir.'/'.$xmlF, "./".$xmlF);
            $xml = simplexml_load_file($xmlF);
            $number = $xml->issue->number;
            $dateUni = $xml->issue->dateUni;
            $issTitle = str_replace(" ", "_", $xml->issue->issTitle);
            $date_add = time();
            $sql = "INSERT INTO vestnik_journal(number, dateUni, issTitle, date_add, dirname) VALUES ('$number', '$dateUni', '$issTitle', '$date_add', '$dir')";
            $query = mysql_query($sql) or die(mysql_error());

            $articleNum = 0;
            $authorNum = 0;
            foreach($xml->issue->articles->article as $article)
            {
                $articles[] = $article->artTitles->artTitle[0];
                $articles[] = $article->artTitles->artTitle[1];
                $articles[] = $article->abstracts->abstract[0];
                $articles[] = $article->abstracts->abstract[1];
                $articles[] = $article->codes->udk;
                $articles[] = rus2translit($article->files->file);
                $articleNum+=6;

                foreach($article->authors->author as $author){
                    $authors[] = $author->individInfo[0]->surname;
                    $authors[] = rus2translit($author->individInfo[0]->surname);
                    $authors[] = str_replace(" ", "_", $author->individInfo[0]->initials);
                    $authors[] = str_replace(" ", "_", $author->individInfo[1]->initials);
                    $authors[] = str_replace(" ", "_", $author->individInfo[0]->orgName);
                    $authors[] = str_replace(" ", "_",$author->individInfo[0]->email);
                    $authors[] = str_replace(" ", "_", $author->individInfo[0]->address);
                    $authors[] = str_replace(" ", "_", $author->individInfo[0]->otherInfo);
                    $authorNum+=8;
                }
            }
            for($i=0; $i<$articleNum; $i+=6)
            {
                $title_rus = str_replace(" ", "_", $articles[$i]);
                $title_eng = str_replace(" ", "_", $articles[$i+1]);
                $abstract_rus = $articles[$i+2];
                $abstract_eng = $articles[$i+3];
                $udk = str_replace(" ", "_", $articles[$i+4]);
                $file = str_replace(" ", "_", $articles[$i+5]);

                $sql = "INSERT INTO vestnik_article(title_rus, title_eng, abstract_rus, abstract_eng, udk, file) VALUES ('$title_rus', '$title_eng', '$abstract_rus', '$abstract_eng', '$udk', '$file')";
                $query = mysql_query($sql) or die(mysql_error());
                $id_a = mysql_insert_id();
                for($j=0; $j<$authorNum; $j+=8){
                    $surname_rus = $authors[$j];
                    $surname_eng = $authors[$j+1];
                    $initials_rus = $authors[$j+2];
                    $initials_eng = $authors[$j+3];
                    $orgName = $authors[$j+4];
                    $email = $authors[$j+5];
                    $address = $authors[$j+6];
                    $otherInfo = $authors[$j+7];

                    $str = similar_text($file, $surname_eng);
                    if($str == strlen($surname_eng)){
                        $sql = "INSERT INTO vestnik_author(id_a, surname_rus, surname_eng, initials_rus, initials_eng, orgName, email, address, otherInfo) VALUES ('$id_a', '$surname_rus', '$surname_eng', '$initials_rus', '$initials_eng', '$orgName', '$email', '$address', '$otherInfo')";
                        $query = mysql_query($sql) or die(mysql_error());
                    }
                }
            }
        }
    }
    $delCopy = scandir('./');
    array_shift($delCopy);
    array_shift($delCopy);
    foreach($delCopy as $delFile){
        $ext = pathinfo($delFile, PATHINFO_EXTENSION);
        if($ext == "xml")
            unlink($delFile);
    }

    return true;
}
?>