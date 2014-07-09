<?php
/**
 * Slide pageview tracker - registers pageview on each slide view in container page
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php include_once("/export/home/cc-common/wss/wss_include.html"); ?>

	<script type="text/javascript" src="/cc-common/wss/hbx.js"></script>
	<script type="text/javascript">
		<!-- 
		s.pageName="contest:<?php echo $_GET['g']; ?>:slideview"
		/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
		var s_code=s.t();if(s_code)document.write(s_code)
		//-->
	</script>

	

</body>
</html>