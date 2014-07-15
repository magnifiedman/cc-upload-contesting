<?php
include('../lib/config.inc.php');
include('../lib/db.inc.php');
include('../lib/classes/admin.class.php');
include('../lib/classes/utility.class.php');
include('../lib/classes/contest.class.php');

$x = rand(1,9999);
$step=1;
$error='';

$a = new Admin();

// logged in?
if(!isset($_COOKIE['adminLogged'])){ header("Location: index.php"); }


// add new contest
if(isset($_POST['addForm'])){
	

	// logged in
	if($a->doContest($_POST,'add')){
		$step = 2;
	}

	// bad username / email combo
	else { $error = '<p class="error"><i class="fa fa-exclamation-triangle"></i> Unable to add contest. Please contact the <a href="mailto:traviswachenorf@clearchannel.com?Subject=Upload+Contesting+Systen+Error">developer</a> or try again.</p>'; }
	
}


//updated local page template
include_once('/export/home/common/template/T25globalincludes'); // do not modify this line
include_once (CDB_REFACTOR_ROOT."feed2.tool"); // do not modify this line

//set variables for og tags and other meta data
$page_title = $contest['name'];
$page_description = $contest['description'];
$keywords = $contest['keywords'];
$url = "http://" . $_SERVER["HTTP_HOST"] .$PHP_SELF; // do not modify this line

$useT27Header = true; //this is a global flag that controls the header file that will be included. Do not change or remove this variable.
include 'CCOMRheader.template'; // do not modify this line
?>

<!-- stylesheets -->
<link rel="stylesheet" href="../<?php echo BASE_URL; ?>css/style.css?x=<?php echo $x; ?>" media="screen" />
<link rel="stylesheet" href="../<?php echo BASE_URL; ?>css/jquery.fancybox.css?x=<?php echo $x; ?>">
<link rel="stylesheet" href="../<?php echo BASE_URL; ?>css/flexslider.css?x=<?php echo $x; ?>">
<link rel="stylesheet" href="../<?php echo BASE_URL; ?>css/font-awesome.min.css?x=<?php echo $x; ?>">
<link href='http://fonts.googleapis.com/css?family=Nobile:400,700|Medula+One' rel='stylesheet' type='text/css'>
<style>
#masthead_topad { display:none; }
</style>

<!-- pagecontainer -->
<div class="pageContainer">


	<?php

	// entry form
	if($step==1){
		?><iframe src="http://clearchannelphoenix.com/contest/add.php?r=<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&e=<?php echo $error; ?>&b=<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/common/contest/admin/'; ?>" frameborder="0" marginheight="0" marginwidth="0" width="990" height="1620" scrolling="no"></iframe><?php
	}


	// success
	if($step==2){
		echo '<!-- contest added -->';
		echo '<a href="/common/contest/admin" class="button blue"><i class="fa fa-reply"></i> Back to Main</a>';
		echo '<p class="success"><i class="fa fa-thumbs-up"></i> Good job Captain! Contest "<strong>' . stripslashes($_POST['name']) . '</strong>" has been successfully added.</p>';

	}
	?>

</div>
<!-- end pagecontainer -->

	<!-- <script src="<?php echo BASE_URL; ?>js/jquery-1.10.1.min.js"></script> -->
	<script src="../<?php echo BASE_URL; ?>js/jquery.flexslider-min.js?"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.validate.min.js"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.fancybox.pack.js"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
	<script>

		$(document).ready(function() {
				
			$('.fancybox').fancybox();
			$("#adminForm").validate();

		});

	
	</script>


<?php include 'CCOMRfooter.template'; ?>