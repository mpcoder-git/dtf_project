<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">   
	<title><?php  echo $title_page;  ?></title>
    <base href="<?php echo DOMAIN_NAME; ?>">
	<link href="/styles.css" rel="stylesheet"  type="text/css">	
</head>
<body>
	<div id="page">
	<?php if (isset($_SESSION['session_adminid'])){ include './Views/cpheader.php'; } ?>
	<?php include './Views/'.$content_view; ?>
	<?php if (isset($_SESSION['session_adminid'])){ include './Views/cpfooter.php'; } ?>
	</div>
</body>
</html>