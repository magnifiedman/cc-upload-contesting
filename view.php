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


// get status
$nowTime = date("Y-m-d H:i:s");
if(($nowTime >= $contest['date_entry']) && ($nowTime < $contest['date_vote_1'])){ $status=1; }
if(($nowTime >= $contest['date_vote_1']) && ($nowTime < $contest['date_winner'])){ $status=2; }
if($nowTime >= $contest['date_winner']){ $status=3; }

$entrants = $c->getActiveSlides($contest['id'], $contest['contest_type'], $galleryID);
$galleryThumbHTML = $c->getGalleryThumbs($contest['id'], $contest['contest_type'], $urlCode, $galleryID);
$galleryCount = count($entrants);
$calendarHTML = $c->getCalendar();


// set text for share buttons
switch($status){
	case 1:
	$actionText = 'View Entrants';
	$fbAction = 'Check+out';
	break;

	case 2:
	$actionText = 'Vote Now';
	$fbAction = 'Vote+for';
	break;

	case 3:
	$actionText = 'View Entrants';
	$fbAction = 'Check+out';
	break;
}


$x = rand(1,9999);
$message = '';
$step=1;


// attempt vote
if(isset($_POST['voteForm'])){
	
	// check if email address given is from a registered voter
	$voter = Voter::checkForRegistered($_POST['email']);
	

	// voter not active or not registered, send confirmation email and create voter record if necessary
	if($voter['status']!=2){
		Voter::sendConfirmation($_POST['email'],$urlCode,$_POST['eid'],$voter);
		$message = '<p class="alert error"><i class="fa fa-exclamation-triangle"></i> It appears this email has not yet voted in any of our contests yet or the account has not been confirmed yet.<br /><br />Please check your email for a confirmation link from Clear Channel Contesting which will activate your account. Once activated, you may vote daily.</p>';
	}


	// voter ctive, place vote
	else {
		// do vote
		$_POST['vid'] = Voter::getVoterID($_POST['email']);

		if(Voter::doVote($_POST)){
			$message = '<p class="success"><i class="fa fa-pencil-square-o"></i> Thank You For Your Vote!<br />Remember, you can vote once every day!</p>';	
		}
		else { 
			$message = '<p class="error"><i class="fa fa-exclamation-triangle"></i> Sorry, this email has already voted today.<br />Remember, you can only vote once a day!</p>';
			
		}
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
<link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css" media="screen" />
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

		<h2><?php echo stripslashes($contest['name']).': '.$actionText; ?></h2>

		<!-- gallery thumbs -->
		<?php echo $galleryThumbHTML; ?>
		
		<!-- slider -->
    	<div class="flexslider carousel">
			<ul class="slides">

				<?php
				$i = 1;
				foreach ($entrants as $entrant){
					if($contest['contest_type']==1){ $fbThumb = $entrant['userfile']; }
					else { $fbThumb = $contest['thumb_img']; }
					$shareLink = urlencode($_SERVER['SCRIPT_URI']. '?' . $_SERVER['QUERY_STRING'] . '#' . $i);
					if($status==1 || $status==3){ $buttonHTML = ''; }
					if($status==2){ $buttonHTML = '<a class="button big vote-btn"><i class="fa fa-thumbs-up"></i> <span>Vote For Me</span></a>'; }
					echo '<li><span class="rightbox">' . $buttonHTML;
					echo 'Share This:<br /><a class="fright" href="http://twitter.com/share?text=Vote+For+' . stripslashes($entrant['fname']) . '+' . substr($entrant['lname'],0,1) .'.+on+'.$contest['name'].'&url=' . $shareLink . '" target="_blank"><i class="fa fa-twitter-square fa-3x"></i></a>';
	    			echo '<a class="fright" href="https://www.facebook.com/dialog/feed?app_id=' . FB_APP_ID . '&link='.$shareLink.'&picture='. CONTEST_IMG_PATH . $fbThumb.'&name='.$fbAction.'+' . $entrant['fname'] . '+' . substr($entrant['lname'],0,1) .'.&caption='.$contest['name'].'&description=&redirect_uri=http://' . $_SERVER['HTTP_HOST'] . '/common/contest/?' . $contest['url_code'] . '" target="_blank"><i class="fa fa-facebook-square fa-3x"></i></a></span>';
	    			echo '<p><strong>' . stripslashes($entrant['fname']) . ' ' . $entrant['lname'] . '</strong><br />' . $i . ' of ' . $galleryCount . ' in this gallery</p>';
	    			echo $message;
	    			echo '<div class="vote-form-box" style="display:none;"><form action="" class="theForm vote-form" method="post"><input type="hidden" name="voteForm" value="y" /><input type="hidden" name="eid" value="'.$entrant['id'].'" /><input type="hidden" name="cid" value="'.$contest['id'].'" /><input type="email" name="email" placeholder="you@youremail.com" required/><input type="submit" name="submit" class="button blue" value="Place Vote" /></form></div>';
	    			if($contest['contest_type']==3) { echo $entrant['img'].'<br />';}
	    			echo $entrant['embedCode'] . '<div class="clear"></div></li>';
					$i++;
				}
				?>

			</ul>
		</div>

	</div>
    

	<!-- sidebar column -->
	<div class="rCol">

		<div class="buttonbox">
			<a href="../contest/?<?php echo $urlCode; ?>" class="button big entervote"><i class="fa fa-reply"></i> Back To Main</a>
		</div>
		<div class="clear"></div>

	    <!-- box ad -->
	    <div class="adbox">
	        <div id="DARTad300x250"><script>DFP.pushAd({div:"DARTad300x250",size:"300x250",position:"3307"} );</script></div>
	    </div>
    
    
	    <?php if($contest['release_form']!=''){ ?>
		    <!-- release form -->
		    <div class="rulesbox rounded colored">
		    <p><a href="pdf/<?php echo $contest['release_form']; ?>"><i class="fa fa-pencil-square-o"></i> RELEASE FORM</a></p>
		    </div>
	    <?php } ?>

	    <?php if($contest['rules_form']!=''){ ?>
		    <!-- rules form -->
		    <div class="rulesbox rounded">
		    <p><a href="pdf/<?php echo $contest['rules_form']; ?>"><i class="fa fa-pencil-square-o"></i> CONTEST RULES</a></p>
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