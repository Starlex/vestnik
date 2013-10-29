<?php
	include "block/fun.lib.php";
	$denied_filetype = array('php', 'js', 'cs', 'cpp', 'vb'); //запрещенные к загрузке типы файлов
    $callback = $_GET['CKEditorFuncNum'];
    $file_name = str_replace(" ", "_", rus2translit($_FILES['upload']['name']));
	$ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); //расширение файла
    $file_name_tmp = $_FILES['upload']['tmp_name'];
	$row = mysql_fetch_assoc($query);
    $file_new_name = '../ckuploadfiles';
	$full_path = $file_new_name.'/'.$file_name;
    $http_path = '/ckuploadfiles/'.$file_name;
    $error = '';

	if(in_array($ext, $denied_filetype))
			die('Not allowed to upload this type of file.');
    if( move_uploaded_file($file_name_tmp, $full_path) )
	{

    } 
	else
	{
		$error = 'При загрузке произошла ошибка. Повторите попытку позже.';
		$http_path = '';
    }
?>
    <script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("<?=$callback;?>",  "<?=$http_path;?>", "<?=$error;?>" );</script>
?>