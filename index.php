<?php
include('lib/config.inc.php');
include('lib/db.inc.php');
include('lib/classes/utility.class.php');
include('lib/classes/contest.class.php');

// misc
$x = rand(1,9999);
$shareLink = urlencode($_SERVER['SCRIPT_URI']. '?' . $_SERVER['QUERY_STRING']);


// url code
$url = array();
foreach($_GET as $key=>$value){
	$url[] = $key;
}
$urlCode = $url[0];


// initialize contest object
$c = new Contest($urlCode);
$contest = $c->getContestDetails();


// get calendar for display
$calendarHTML = $c->getCalendar();


// get status
$nowTime = date("Y-m-d h:i:s");
$winnerMessage='';
if(($nowTime >= $contest['date_entry']) && ($nowTime < $contest['date_vote_1'])){ $status=1; }
if(($nowTime >= $contest['date_vote_1']) && ($nowTime < $contest['date_winner'])){ $status=2; }
if($nowTime >= $contest['date_winner']){ $status=3; }
if($status==3){ $winnerMessage= $c->getWinnerMessage($contest['id']); }


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
    
    <?php switch($status){
      case 1: // entry period
        echo '<a href="enter.php?' . $urlCode . '" class="button big entervote">Enter Now</a>'."\n";
        echo '<a href="view.php?' . $urlCode . '" class="button big view">View Entrants</a>'."\n";
        $caption = 'Enter Now!';
        break;
        
      case 2: // voting
        echo '<a href="view.php?' . $urlCode . '" class="button big entervote">Vote Now</a>'."\n";
        $caption = 'Vote Now!';
        break;
        
      case 3: // awaiting winner
        echo '<a href="view.php?' . $urlCode . '" class="button big view">View Entrants</a>'."\n";
        $caption = 'Winner(s) Announced Soon!';
        break;

      case 4: // winner
        echo '<a href="view.php?' . $urlCode . '" class="button big view">View Entrants</a>'."\n";
        $caption = 'We Have Our Winner(s)!';
        break;
    }
    
    ?>

    </div>
    <div class="clear"></div>
  </div>

  <!-- content column -->
  <div class="lCol">

    <h2><?php echo $contest['heading']; ?></h2>
    <?php echo $winnerMessage; ?>

    <?php echo $contest['body']; ?>

    <?php
    if($contest['sponsor_text_1']!=''){
    	echo '<p><em><strong>' . stripslashes($contest['name']) . ' is brought to you by:</strong></em></p>';      
    	?>
    
		<!-- sponsorbox 1 -->
		<div class="sponsorbox rounded">
			<?php if($contest['sponsor_img_1']!=''){ ?><a href="<?php echo $contest['sponsor_url_1']; ?>" target="_blank"><img src="<?php echo SPONSOR_PATH.$contest['sponsor_img_1'];?>" border="0" alt="<?php echo $contest['sponsor_name_1']; ?>" /></a> <?php } ?>
			<p class="pullup"><strong><a href="<?php echo $contest['sponsor_url_1']; ?>" target="_blank"><?php echo $contest['sponsor_name_1']; ?></a></strong></p>
			<?php echo $contest['sponsor_text_1']; ?>
			<div class="clear"></div>
		</div>
    
    <?php }

    if($contest['sponsor_text_2']!=''){
    	?>
    
		<!-- sponsorbox 2 -->
		<div class="sponsorbox rounded">
			<?php if($contest['sponsor_img_2']!=''){ ?><a href="<?php echo $contest['sponsor_url_2']; ?>" target="_blank"><img src="<?php echo SPONSOR_PATH.$contest['sponsor_img_2'];?>" border="0" alt="<?php echo $contest['sponsor_name_2']; ?>" /></a> <?php } ?>
			<p class="pullup"><strong><a href="<?php echo $contest['sponsor_url_2']; ?>" target="_blank"><?php echo $contest['sponsor_name_2']; ?></a></strong></p>
			<?php echo $contest['sponsor_text_2']; ?>
			<div class="clear"></div>
		</div>
    
    <?php }

    if($contest['sponsor_text_3']!=''){
    	?>
    
		<!-- sponsorbox 3 -->
		<div class="sponsorbox rounded">
			<?php if($contest['sponsor_img_3']!=''){ ?><a href="<?php echo $contest['sponsor_url_3']; ?>" target="_blank"><img src="<?php echo SPONSOR_PATH.$contest['sponsor_img_3'];?>" border="0" alt="<?php echo $contest['sponsor_name_3']; ?>" /></a> <?php } ?>
			<p class="pullup"><strong><a href="<?php echo $contest['sponsor_url_3']; ?>" target="_blank"><?php echo $contest['sponsor_name_3']; ?></a></strong></p>
			<?php echo $contest['sponsor_text_3']; ?>
			<div class="clear"></div>
		</div>
    
    <?php } ?>
	  
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
    
    
	    <?php if($contest['release_form']!=''){ ?>
		    <!-- release form -->
		    <div class="rulesbox rounded colored">
		    <p><a href="<?php echo FORM_PATH.$contest['release_form']; ?>" target="_blank"><i class="fa fa-pencil-square-o"></i> RELEASE FORM</a></p>
		    </div>
	    <?php } ?>

	    <?php if($contest['rules_form']!=''){ ?>
		    <!-- rules form -->
		    <div class="rulesbox rounded">
		    <p><a href="<?php echo FORM_PATH.$contest['rules_form']; ?>" target="_blank"><i class="fa fa-pencil-square-o"></i> CONTEST RULES</a></p>
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