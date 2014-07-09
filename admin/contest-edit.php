<?php
include('../lib/config.inc.php');
include('../lib/db.inc.php');
include('../lib/classes/admin.class.php');
include('../lib/classes/utility.class.php');
include('../lib/classes/contest.class.php');


// misc
$x = rand(1,9999);
$step=1;
if($_GET['step']==2){ $step=2; }
$error='';
$message='';


// initialize objects
$a = new Admin();
$c = new Contest();
$contest = $c->getContestDetails($_GET['id']);

// set defaults
if($contest['header_img']==''){ $header_img = 'no-header.jpg'; } else { $header_img = $contest['header_img']; }
if($contest['thumb_img']==''){ $thumb_img = 'no-thumb.jpg'; } else { $thumb_img = $contest['thumb_img']; }
if($contest['rules_form']==''){ $rules_msg = '<i class="fa fa-exclamation-triangle"></i> No Rules Uploaded'; } else { $rules_msg = '<a href="' . FORM_PATH . $contest['rules_form'] .'"><i class="fa fa-check-circle"></i> Rules Uploaded</a>'; }
if($contest['release_form']==''){ $permission_msg = '<i class="fa fa-exclamation-triangle"></i> No Permission Form Uploaded'; } else { $permission_msg = '<a href="' . FORM_PATH.  $contest['release_form'] . '"><i class="fa fa-check-circle"></i> Rules Uploaded</a>'; }

if($contest['sponsor_img_1']==''){ $sponsor_img_1 = 'no-thumb.jpg'; } else { $sponsor_img_1 = $contest['sponsor_img_1']; }
if($contest['sponsor_img_2']==''){ $sponsor_img_2 = 'no-thumb.jpg'; } else { $sponsor_img_2 = $contest['sponsor_img_2']; }
if($contest['sponsor_img_3']==''){ $sponsor_img_3 = 'no-thumb.jpg'; } else { $sponsor_img_3 = $contest['sponsor_img_3']; }

$selectEntry =  Utility::hourSelect('hour_entry', substr($contest['date_entry'],11,5));
$selectVote =   Utility::hourSelect('hour_vote',  substr($contest['date_vote_1'],11,5));
$selectWinner = Utility::hourSelect('hour_winner',substr($contest['date_winner'],11,5));

// logged in?
if(!isset($_COOKIE['adminLogged'])){ header("Location: index.php"); }

// add new contest
if(isset($_POST['editFiles'])){

	if($a->doContest($_POST,'edit-files')){

		$contest = $c->getContestDetails($_GET['id']);

		// set defaults
		if($contest['header_img']==''){ $header_img = 'no-header.jpg'; } else { $header_img = $contest['header_img']; }
		if($contest['thumb_img']==''){ $thumb_img = 'no-thumb.jpg'; } else { $thumb_img = $contest['thumb_img']; }
		if($contest['rules_form']==''){ $rules_msg = '<i class="glyphicon glyphicon-exclamation-sign"></i> No Rules Uploaded'; } else { $rules_msg = '<a href="' . FORM_PATH . $contest['rules_form'] .'"><i class="glyphicon glyphicon-ok"></i> Rules Uploaded</a>'; }
		if($contest['release_form']==''){ $permission_msg = '<i class="glyphicon glyphicon-exclamation-sign"></i> No Permission Form Uploaded'; } else { $permission_msg = '<a href="' . FORM_PATH.  $contest['release_form'] . '"><i class="glyphicon glyphicon-ok"></i> Rules Uploaded</a>'; }

		if($contest['sponsor_img_1']==''){ $sponsor_img_1 = 'no-thumb.jpg'; } else { $sponsor_img_1 = $contest['sponsor_img_1']; }
		if($contest['sponsor_img_2']==''){ $sponsor_img_2 = 'no-thumb.jpg'; } else { $sponsor_img_2 = $contest['sponsor_img_2']; }
		if($contest['sponsor_img_3']==''){ $sponsor_img_3 = 'no-thumb.jpg'; } else { $sponsor_img_3 = $contest['sponsor_img_3']; }

		$selectEntry =  Utility::hourSelect('hour_entry', substr($contest['date_entry'],11,5));
		$selectVote =   Utility::hourSelect('hour_vote',  substr($contest['date_vote_1'],11,5));
		$selectWinner = Utility::hourSelect('hour_winner',substr($contest['date_winner'],11,5));

		$step = 1;
	}
}

// add new contest
if(isset($_POST['updateForm'])){
	
	// logged in
	if($a->doContest($_POST,'edit')){
		$message = '<p class="success"><i class="fa fa-thumbs-up"></i> Good job captain. Contest details updated.</p>'; 
	
	}

	// bad username / email combo
	else { $message = '<p class="error"><i class="fa fa-exclamation-triangle"></i> Unable to edit contest. Please contact the <a href="mailto:traviswachenorf@clearchannel.com?Subject=Upload+Contesting+Systen+Error">developer</a> or try again.</p>'; }
	
	$contest = $c->getContestDetails($_GET['id']);
}


// get select dropdown for contest type
$contestTypeSelect = Utility::contestTypeSelect($contest['contest_type']);


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
<link rel="stylesheet" href="../<?php echo BASE_URL; ?>css/jquery-ui.min.css">

<script src="//cdn.ckeditor.com/4.4.2/basic/ckeditor.js"></script>
<link href='http://fonts.googleapis.com/css?family=Nobile:400,700|Medula+One' rel='stylesheet' type='text/css'>
<style>
#masthead_topad { display:none; }
</style>

<!-- pagecontainer -->
<div class="pageContainer">
	<h2>Update Contest: <?php echo stripslashes($contest['name']); ?></h2>
	<a href="/common/contest/admin" class="button blue"><i class="fa fa-reply"></i> Back to Main</a>

	<?php
	// entry form
	if($step==2){
		?>
		
		<iframe src="http://clearchannelphoenix.com/contest/edit.php?r=<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&e=<?php echo $error; ?>&b=<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/common/contest/admin/'; ?>&name=<?php echo urlencode($contest['name']); ?>&header_img=<?php echo urlencode($contest['header_img']); ?>&thumb_img=<?php echo urlencode($contest['thumb_img']); ?>&rules_form=<?php echo urlencode($contest['rules_form']); ?>&release_form=<?php echo urlencode($contest['release_form']); ?>&sponsor_img_1=<?php echo urlencode($contest['sponsor_img_1']); ?>&sponsor_img_2=<?php echo urlencode($contest['sponsor_img_2']); ?>&sponsor_img_3=<?php echo urlencode($contest['sponsor_img_3']); ?>" frameborder="0" marginheight="0" marginwidth="0" width="990" height="1620" scrolling="no"></iframe>

		<?php
	}


	// image updates
	if($step==1){ ?>
            
            
            <!-- main form -->
            
             <form action="" method="post" id="adminForm" class="theForm">

             	<p><?php echo $message; ?></p>
                <input type="hidden" name="updateForm" value="y" />
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" />

                
				<!-- images and forms -->
                <p>Contest Images and Forms</p>
                <div class="thumb-tile">
                	<label>Header Image</label>
                	<a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><img src="<?php echo IMG_PATH.$header_img; ?>" height="100" /></a>
                </div>
                <div class="thumb-tile">
                	<label>Thumb Image</label>
                	<a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><img src="<?php echo IMG_PATH.$thumb_img; ?>" height="100" /></a>
                </div>
				<div class="thumb-tile">
	                <p><a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><?php echo $rules_msg; ?></a></p>
	                <p><a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><?php echo $permission_msg; ?></a></p>
                </div>

                <div class="clear"></div>
                
                
                <div class="thumb-tile">
                	<label>Sponsor Thumb 1</label>
                	<a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><img src="<?php echo SPONSOR_PATH.$sponsor_img_1; ?>" height="100" /></a>
                </div>
                <div class="thumb-tile">
					<label>Sponsor Thumb 2</label>
                	<a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><img src="<?php echo SPONSOR_PATH.$sponsor_img_2; ?>" height="100" /></a>
                </div>
                <div class="thumb-tile">
                	<label>Sponsor Thumb 3</label>
					<a href="<?php echo $_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING']; ?>&step=2"><img src="<?php echo SPONSOR_PATH.$sponsor_img_3; ?>" height="100" /></a>
				</div>

				<div class="clear"></div>


                <!-- contest details -->
                <p><label>Contest Name *</label><input type="text" name="name" class="required" value="<?php echo stripslashes($contest['name']); ?>" ></p>
                <p><label>Contest Type *</label><?php echo $contestTypeSelect; ?></p>
                <p><label>Contest Description *</label><textarea name="description" class="required"><?php echo htmlentities($contest['description']); ?></textarea></p>
                <p><label>Contest Keywords (Separate with commas)</label><input type="text" name="keywords" class="" value="<?php echo $contest['keywords']; ?>" /></p>
                <p><label>Contest Page Heading Text *</label><input type="text" name="heading" class="required" value="<?php echo $contest['heading']; ?>" /></p>
                <p><label>Contest Page Body Text *</label><textarea class="ckeditor required" cols="80" id="editor1" name="body_text" rows="10"><?php echo htmlentities($contest['body']); ?></textarea></p>
                <p class="dates"><label>Contest Entry Start Date:</label><input type="text" name="date_entry" class="halfcol datepicker required" value="<?php echo substr($contest['date_entry'],0,10); ?>" /> @ <?php echo $selectEntry; ?></p>
                <p class="dates"><label>Contest Voting Start Date:</label><input type="text" name="date_vote_1" class="halfcol datepicker required" value="<?php echo substr($contest['date_vote_1'],0,10); ?>" /> @ <?php echo $selectVote; ?></p>
                <p class="dates"><label>Contest Voting End Date:</label><input type="text" name="date_winner" class="halfcol datepicker required" value="<?php echo substr($contest['date_winner'],0,10); ?>" /> @ <?php echo $selectWinner; ?></p>
            	
            	<a class="button" id="sponsors" style="margin:20px 0;"><i class="fa fa-plus"></i> I Need to Update Sponsor Info</a>
            	<div id="sponsor-div" style="display:none;">
                    <p><label>Sponsor 1</label><input type="text" name="sponsor_name_1" class="halfcol" placeholder="Name" value="<?php echo $contest['sponsor_name_1']; ?>" /><input type="text" name="sponsor_url_1" class="halfcol" placeholder="URL" value="<?php echo $contest['sponsor_url_1']; ?>" /><br />
                    <textarea name="sponsor_text_1" class="ckeditor" rows="3"><?php echo $contest['sponsor_text_1']; ?></textarea></p>
                    <p><label>Sponsor 2</label><input type="text" name="sponsor_name_2" class="halfcol" placeholder="Name" value="<?php echo $contest['sponsor_name_2']; ?>" /><input type="text" name="sponsor_url_2" class="halfcol" placeholder="URL" value="<?php echo $contest['sponsor_url_2']; ?>"  /><br />
                    <textarea name="sponsor_text_2" class="ckeditor" rows="3"><?php echo $contest['sponsor_text_2']; ?></textarea></p>
                    <p><label>Sponsor 3</label><input type="text" name="sponsor_name_3" class="halfcol" placeholder="Name" value="<?php echo $contest['sponsor_name_3']; ?>" /><input type="text" name="sponsor_url_3" class="halfcol" placeholder="URL" value="<?php echo $contest['sponsor_url_3']; ?>" /><br />
                    <textarea name="sponsor_text_3" class="ckeditor" rows="3"><?php echo $contest['sponsor_text_3']; ?></textarea></p>
                </div><p><label></label><input type="submit" class="button blue" value="Update Contest Details" style="margin:20px 0;" /></p>
            </form>

    <?php } ?>

</div>
<!-- end pagecontainer -->

	<!-- <script src="<?php echo BASE_URL; ?>js/jquery-1.10.1.min.js"></script> -->
	<script src="../<?php echo BASE_URL; ?>js/jquery-ui.min.js"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.flexslider-min.js?"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.validate.min.js"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.fancybox.pack.js"></script>
	<script src="../<?php echo BASE_URL; ?>js/jquery.mousewheel-3.0.6.pack.js"></script>
	<script>

		$(document).ready(function() {
				
			$('.fancybox').fancybox();
			$(".theForm").validate();
			$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" });

			$('#sponsors').click(function(){
		      $("#sponsor-div").toggle();
		    });

		});

	
	</script>


<?php include 'CCOMRfooter.template'; ?>