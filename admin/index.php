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

if(isset($_COOKIE['adminLogged'])){ $step=2; }

if(isset($_POST['loginForm'])){
	
	// logged in
	if($a->doLogin($_POST)){
		$step = 2;
	}

	// bad username / email combo
	else { $error = '<p class="error"><i class="fa fa-exclamation-triangle"></i> Username / Password is incorrect. Please retry.</p>'; }
}


// delete entrant
if(isset($_POST['deleteForm'])){
	$a->deleteContest($_POST['id']);
}



if($step==2){
	if(!isset($_GET['p'])){ $page=1; }
	else { $page = $_GET['p']; }
	$adminHTML = $a->getContestOverview($page,30);
	$totalContests = $a->getTotalContests();

	$totalPages = ceil($totalContests/30);
	
}

// search for entrants
if(isset($_POST['searchForm'])){
	$adminHTML = $a->getContestSearch($searchStr, $page, 30);
	$step=3;
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

	<h2>Clear Channel Contesting: Contests</h2>


	<?php
	if($step==1){ ?>
		<!-- login form -->
		<?php echo $error; ?>
		<form action="" method="post" id="adminForm">
			<input type="hidden" name="loginForm" value="y" />
			<p><label>Email:</label><input type="email" name="email" required/></p>
			<p><label>Password</label><input type="password" name="pword" required/></p>
			<input type="submit" class="button" value="Login" />
		</form>

	<?php }
	
	
	// default table view
	if($step==2){
		echo '<!-- overview table -->';
		echo '<form action="" method="post" id="searchForm"><input type="hidden" name="searchForm" value="y" /><input type="text" name="searchStr" placeholder="Search" required/><input type="submit" class="button blue" value="Find a Contest" /></form>';
		echo '<p class="searchText"><a class="button blue" href="contest-add.php"><i class="fa fa-plus-square"></i> Add Contest</a></p>';
		echo '<div class="clear"></div>';
		include('../inc/pagination.html.php');
		echo $adminHTML;
		include('../inc/pagination.html.php');
	}


	// search results
	if($step==3){
		echo '<!-- overview table -->';
		echo '<form action="" method="post" id="searchForm"><input type="hidden" name="searchForm" value="y" /><input type="text" name="searchStr" placeholder="Search" required/><input type="submit" class="button blue" value="Find a Contest" /></form>';
		echo '<p class="searchText"><a class="button" href="index.php"><i class="fa fa-long-arrow-left"></i> View All Contests</a> Searching for <strong>"'.htmlspecialchars(trim($_POST['searchStr'])).'"</strong></p>';
		echo '<div class="clear"></div>';
		include('../inc/pagination.html.php');
		echo $adminHTML;
		include('../inc/pagination.html.php');

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