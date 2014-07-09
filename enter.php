<?php
include('lib/config.inc.php');
include('lib/db.inc.php');
include('lib/classes/utility.class.php');
include('lib/classes/contest.class.php');

// misc
$x = rand(1,9999);
$step=1;


// url code
$url = array();
foreach($_GET as $key=>$value){
	$url[] = $key;
}
$urlCode = $url[0];


// intialize contest object
$c = new Contest($urlCode);
$contest = $c->getContestDetails();


// get calendar for display
$calendarHTML = $c->getCalendar();


// do contest entry
if(isset($_POST['addForm'])){
	if($c->doEntry($_POST,'add',$contest['id'])){
		if($_POST['social_link']!=''){ $socialEmbed = Utility::getEmbed(trim($_POST['social_link']),'s'); }
		$step=2;
	}
	else {
		$step=3;
	}
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
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css?x=<?php echo $x; ?>" media="screen" />
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/jquery.fancybox.css?x=<?php echo $x; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/flexslider.css?x=<?php echo $x; ?>">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/font-awesome.min.css?x=<?php echo $x; ?>">
<link href='http://fonts.googleapis.com/css?family=Nobile:400,700|Medula+One' rel='stylesheet' type='text/css'>
<style>
.pageContainer div.header {
	background-image:url('<?php echo IMG_PATH.$contest['header_img']; ?>')!important;
}
</style>


<!-- pagecontainer -->
<div class="pageContainer">

	<!-- header -->
	<div class="header">
		<div class="buttonbox">
			<a href="index.php?<?php echo $urlCode; ?>" class="button big entervote"><i class="fa fa-reply"></i> Back To Main</a>
		</div>
		<div class="clear"></div>
	</div>

	<!-- content column -->
	<div class="lCol">
		
		<?php
		// entry form
		if($step==1){ ?><iframe src="http://clearchannelphoenix.com/contest/enter.php?r=<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&t=<?php echo $contest['contest_type']; ?>" frameborder="0" marginheight="0" marginwidth="0" width="660" height="1050" scrolling="no"></iframe><?php }
		
		// successful submission
		if($step==2){ ?>

			<h2>Thank you for your entry</h2>
			<div class="callout-box">
				<h3>Your Information</h3>
				<p><strong>Your Name:</strong> <?php echo trim($_POST['fname']).' '.trim($_POST['lname']).'<br />'; ?>
				<strong>Your Email &amp; Phone: </strong> <?php echo trim($_POST['email']).' / '.trim($_POST['phone']); ?></p>
				<p><strong>Your Entry:</strong><br />
				<?php if($_POST['userfile']!=''){ ?><img src="<?php echo CONTEST_IMG_PATH.$_POST['userfile']; ?>" width="570" /></p><?php } ?>
				<?php if($socialEmbed!=''){ echo $socialEmbed; ?></p><?php } ?>

			</div>
			<div class="callout-box">
				<h3>What's the Next Step?</h3>
				<p>Please allow 24-48 hours for us to review your submission. Once it is approved it will appear in the contest gallery sorted alphabetically by first name, last initial.</p>
			</div>

		<?php } 
		// error
		if($step==3){ echo '<p class="error"><i class="fa fa-exclamation-triangle"></i> Sorry, this email has already entered the contest. If you recently uploaded a photo, please allow 24-48 hrs for it to show up in the gallery.</p>'; }
			?>

	</div>
    

	<!-- sidebar column -->
	<div class="rCol">
      
	    <!-- sharing buttons -->
	    <div id="shareit">
	    	Share this page:<br />
	    	<a href="https://www.facebook.com/dialog/feed?app_id=<?php echo FB_APP_ID; ?>&link=<?php echo $shareLink; ?>&picture=<?php echo IMG_PATH . $contest['thumb_img']; ?>&name=<?php echo $page_title; ?>&caption=<?php echo $caption; ?>&description=<?php echo $contest['description']; ?>&redirect_uri=<?php echo $shareLink; ?>" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a>
	    	<a href="http://twitter.com/share?text=<?php echo $contest['name'].'-'.$caption; ?>&url=<?php echo $shareLink; ?>" target="_blank"><i class="fa fa-twitter-square fa-3x"></i></a>
	    </div>

	    <!-- box ad -->
	    <div class="adbox">
	        <div id="DARTad300x250"><script>DFP.pushAd({div:"DARTad300x250",size:"300x250",position:"3307"} );</script></div>
	    </div>
    
    
	    <?php echo $contest['release_form']; if($contest['release_form']!=''){ ?>
		    <!-- release form -->
		    <div class="rulesbox rounded colored">
		    <p><a href="pdf/<?php echo $contest['release_form']; ?>"><i class="fa fa-pencil-square-o fa-2x"></i> PARENTAL RELEASE FORM</a></p>
		    </div>
	    <?php } ?>

	    <?php if($contest['rules_form']!=''){ ?>
		    <!-- rules form -->
		    <div class="rulesbox rounded">
		    <p><a href="pdf/<?php echo $contest['rules_form']; ?>"><i class="fa fa-pencil-square-o fa-2x"></i> CONTEST RULES</a></p>
		    </div>
	    <?php } ?>

		<!-- calendar -->
		<div class="calendar">
			<h3>Contest Calendar</h3>
		    <?php echo $calendarHTML; ?>
		</div>
	    

	    

	</div>

	<div class="clear"></div>

</div>
<!-- end pagecontainer -->

<!-- local scripts -->

<?php include 'CCOMRfooter.template'; ?>