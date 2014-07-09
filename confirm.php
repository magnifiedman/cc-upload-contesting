<?php

/**
 * Gallery view page - entry round shows entrants, voting round shows entrants with vote buttons.
 */
include('lib/config.inc.php');
include('lib/db.inc.php');
include('lib/classes/utility.class.php');
include('lib/classes/contest.class.php');
include('lib/classes/voter.class.php');


// break apart the url and get needed gallery values
$url = array();
foreach($_GET as $key=>$value){
	$url[] = $key;
}
$urlCode = $url[0];
if(isset($_GET['g'])){ $galleryID = $_GET['g']; }

$slideID = 0;
if(isset($_GET['s'])){ $slideID = $_GET['s']-1; }


// get our dynamic content
$c = new Contest($urlCode);
$contest = $c->getContestDetails();
$entrants = $c->getActiveSlides($contest['id'], $contest['contest_type'], $galleryID);
$galleryThumbHTML = $c->getGalleryThumbs($contest['id'], $contest['contest_type'], $urlCode, $galleryID);
$galleryCount = count($entrants);
$calendarHTML = $c->getCalendar();
$v = new Voter();

// redirect if no get vars
if(!isset($_GET['vid']) || !isset($_GET['eid'])){ header("Location: ../contest/?".$urlCode); }

$x = rand(1,9999);
$message = '';
$step=1;

// activate user and place first vote
if($v->activateUser($_GET,$contest['id'])){ 
	$entrantName = $c->getEntrantName($_GET['eid']);
}
else { header("Location: ../contest/?".$urlCode); }

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

	<!-- content column -->
	<div class="lCol">

		<h2><?php echo stripslashes($contest['name']) .': '.$actionText; ?></h2>

		<!-- gallery thumbs -->
		<?php echo $galleryThumbHTML; ?>
		
		<!-- confirmation message -->
		<p class="activated">Now you've done it. You've gone and confirmed your email address. We went ahead and placed your first vote in the system for <?php echo $entrantName; ?> while we were at it.<br /><br />
			<a class="button" href="../contest/?<?php echo $urlCode; ?>"><i class="fa fa-long-arrow-left"></i> BACK TO CONTEST MAIN PAGE</a></p>

	</div>
    

	<!-- sidebar column -->
	<div class="rCol">

	    <div class="moduleContainer" id="ad300x250">
			<iframe name="adframe" width="300" height="250" src="ad.php?" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe>
		</div>
    
    
	    <?php if($contest['release_form']!=''){ ?>
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

	<iframe name="viewframe" width="1" height="1" src="slideview.php?g=<?php echo $urlCode; ?>" frameborder="0" scrolling="no" ></iframe>

</div>
<!-- end pagecontainer -->

<!-- <script src="<?php echo BASE_URL; ?>js/jquery-1.10.1.min.js"></script> -->
	<script src="<?php echo BASE_URL; ?>js/jquery.flexslider-min.js?"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.validate.min.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.fancybox.pack.js"></script>
	<script src="<?php echo BASE_URL; ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
	<script>
		var curSlide=1;

		$(document).ready(function() {
				
			$('.fancybox').fancybox();
			$('#previewpop').fancybox();
			$("#theForm").validate();
			$(".theForm").validate();

			var index = 0, hash = window.location.hash;
			if (hash) {
		        index = /\d+/.exec(hash)[0];
		        index = (parseInt(index) || 1) - 1;
		    }

			$('.flexslider').flexslider({
		        startAt: index,
		        slideshow: false,
		        video:true,
		        smoothHeight:true,
		        start: function(slider){
		          $('body').removeClass('loading');
		        },
		        after: function(slider) {
			      var cs = slider.currentSlide;
			      window.curSlide = cs+1;
			      window.location.hash  = window.curSlide;
			    }

		      });

			var adRatio;
			adRatio=1;
			var alt; 

			// track pageviews / change ads
			$('.flex-next').click(function(event){
				if(adRatio===4) { adRatio = 1; }

				if(alt){ alt = ''; }
		        else { alt = '-alt'; }

		        viewframe.location.href='view'+alt+'.php?g=<?php echo $urlCode; ?>';
		        if(adRatio===1) { adframe.location.href='ad.php?1'; }
		        
		        adRatio = adRatio+1;
		      
		    });

		    $('.flex-prev').click(function(event){
		        if(adRatio===4) { adRatio = 1; }
	
		        if(alt){ alt = ''; }
		        else { alt = '-alt'; }

		        viewframe.location.href='view'+alt+'.php?g=<?php echo $urlCode; ?>';
		        if(adRatio===1) { adframe.location.href='ad.php?2'; }
		        
		        adRatio = adRatio+1;
		               
		    });

		    // show hide vote btn
		    $('.vote-btn').click(function() {
		    	$(this).closest( "li" ).children( ".vote-form-box" ).toggle();
		    	$(this).children('span').text(($(this).children('span').text() == 'Vote For Me') ? 'Vote Below' : 'Vote For Me');
		    	$(this).children('i').toggleClass('fa fa-thumbs-up');
		    	$(this).children('i').toggleClass('fa fa-arrow-down');	
		    });

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