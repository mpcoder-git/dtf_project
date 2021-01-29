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
	<?php include './Views/Blocks/header.php'; ?>
	<?php include './Views/'.$content_view; ?>
	<?php include './Views/Blocks/footer.php'; ?>
	</div>
</body>
</html>