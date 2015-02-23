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


// page
if(!isset($_GET['p'])){ $page=1; }
else { $page = $_GET['p']; }
$id = $_GET['id'];


// status
if(!isset($_GET['s'])){ $status=1; }
else { $status = $_GET['s']; }
if($status==1){ $statusName = 'Pending'; }
if($status==2){ $statusName = 'Active'; }


// update statuses
if(isset($_POST['statusForm'])){
	$a->updateStatuses($_POST['entrants'],$status);
}


// delete entrant
if(isset($_POST['deleteForm'])){
	$a->deleteEntrant($_POST['id']);
}


// get page content
$contest = $a->getContestDetails($id);
$entrants = $a->getContestEntrants($id,$status, $page, 50);
$totalPages = ceil($entrants['total']/50);


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

	<img src="<?php echo IMG_PATH.$contest['thumb_img']; ?>" class="admin-thumb" /><h2 class="admin-h2"><?php echo $contest['name']; ?></h2>


	<?php
	// overview table with pagination
	include('../inc/pagination.html.php');
	echo $entrants['html'];
	include('../inc/pagination.html.php');
	?>


</div>
<!-- end pagecontainer -->

	<!-- local scripts -->
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
	<!-- end local scripts -->

<?php include 'CCOMRfooter.template'; ?>