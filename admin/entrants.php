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

if(!isset($_COOKIE['adminLogged'])){ header("Location: index.php"); }


if(!isset($_GET['p'])){ $page=1; }
else { $page = $_GET['p']; }

$adminHTML = $a->getEntrantOverview($page,30);
$totalEntrants = $a->getTotalEntrants();
$totalEntrants = ceil($totalEntrants/30);


// search for entrants
if(isset($_POST['searchForm'])){
	$adminHTML = $a->getEntrantSearch($searchStr, $page, 30);
	$searchStrText = '<p class="searchText"><a class="button" href="entrants.php"><i class="fa fa-long-arrow-left"></i> View All Entrants</a> Searching for <strong>"'.htmlspecialchars(trim($_POST['searchStr'])).'"</strong></p>';
	$step=2;
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
	<h2>Clear Channel Contesting: Entrants</h2>

	<?php if($step==1){
		echo '<form action="" method="post" id="searchForm"><input type="hidden" name="searchForm" value="y" /><input type="text" name="searchStr" placeholder="Name or Email" required/><input type="submit" class="button blue" value="Find an Entrant"/></form>';
		echo '<div class="clear"></div>';
		include('../inc/pagination.html.php');
		echo $adminHTML;
		include('../inc/pagination.html.php');
	}
	?>

	<?php if($step==2){
		echo '<form action="" method="post" id="searchForm"><input type="hidden" name="searchForm" value="y" /><input type="text" name="searchStr" placeholder="Name or Email" required/><input type="submit" class="button blue" value="Find an Entrant"/></form>' . $searchStrText;
		echo '<div class="clear"></div>';
		echo $adminHTML;
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
		var curSlide=1;

		$(document).ready(function() {
				
			$('.fancybox').fancybox();
			$("#searchForm").validate();

		});

	
	</script>

	<script type="text/javascript" src="/cc-common/wss/hbx.js"></script>
	<script type="text/javascript">
		<!-- 
		s.pageName="contest:<?php echo $urlCode; ?>"
		/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
		var s_code=s.t();if(s_code)document.write(s_code)
		//-->
	</script>

<!-- local scripts -->

<?php include 'CCOMRfooter.template'; ?>