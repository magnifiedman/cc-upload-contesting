<?php
/**
 * Ajax to return contest detail values to use with cross site forms
 */
include('config.inc.php');
include('db.inc.php');
include('classes/contest.class.php');

$c = new Contest();
$contest = $c->getContestDetails($_REQUEST['id']);

switch($_REQUEST['s']){
	
	// stage 1 - images and sponsor info
	case 1:
	
	/*echo 'name=' . $contest['name'] . '&';
	echo 'contest_type=' . $contest['contest_type'] . '&';
	echo 'header_img=' . $contest['header_img'] . '&';
	echo 'thumb_img=' . $contest['thumb_img'] . '&';
	echo 'contest_rules=' . $contest['contest_rules'] . '&';
	echo 'parental_permission=' . $contest['parental_permission'] . '&';
	
	echo 'sponsor_name_1=' . $contest['sponsor_name_1'] . '&';
	echo 'sponsor_img_1=' . $contest['sponsor_img_1'] . '&';
	echo 'sponsor_url_1=' . $contest['sponsor_url_1'] . '&';
	echo 'sponsor_text_1=' . $contest['sponsor_text_1'] . '&';
	
	echo 'sponsor_name_2=' . $contest['sponsor_name_2'] . '&';
	echo 'sponsor_img_2=' . $contest['sponsor_img_2'] . '&';
	echo 'sponsor_url_2=' . $contest['sponsor_url_2'] . '&';
	echo 'sponsor_text_2=' . $contest['sponsor_text_2'] . '&';
	
	echo 'sponsor_name_3=' . $contest['sponsor_name_3'] . '&';
	echo 'sponsor_img_3=' . $contest['sponsor_img_3'] . '&';
	echo 'sponsor_url_3=' . $contest['sponsor_url_3'] . '&';
	echo 'sponsor_text_3=' . $contest['sponsor_text_3'] . '&';*/
	echo "HELLO";

	break;

	// stage 2 - contest details
	case 2:
	break;
}
