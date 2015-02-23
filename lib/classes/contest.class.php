<?php
/**
 * 
 * CC Upload Contesting Contest Class
 * 
 * Original Creation Date 05.2014
 * 
 * Wherein we create and manipulate the contest object
 * 
 */
class Contest{


	/**
	 * Sets url code
	 * @param string $urlCode
	 * @var string $urlCode
	 */
	function Contest($urlCode) {
        $this->urlCode = $urlCode;
    }


    /**
     * Get's all contest details from the db
     * @return array contest details
     */
    function getContestDetails($id=''){
        if($id!=''){
            
            $r = mysql_query("
            SELECT * 
            FROM " . CONTEST_TABLE . "
            WHERE id = '" . $id . "'
            ");    
        }
        else {
            
            $r = mysql_query("
            SELECT * 
            FROM " . CONTEST_TABLE . "
            WHERE url_code = '" . $this->urlCode . "'
            ");
        }
    	
    	if($r){ return mysql_fetch_assoc($r); }
    }


    /**
     * Get all important contest dates to echo to main contest page.
     * @return string Calendar HTML
     */
    function getCalendar(){
    	$r = mysql_query("
    		SELECT 
    		date_entry as de,
    		date_vote_1 as dv1,
    		date_vote_2 as dv2,
    		ent_vote_2 as ev2,
    		date_vote_3 as dv3,
    		ent_vote_3 as ev3,
    		date_winner as dw
    		FROM " . CONTEST_TABLE . "
    		WHERE url_code = '" . $this->urlCode . "'
    		");
    	if($r){
    		$calendarHTML = '<ul class="calendar">';
    		$row = mysql_fetch_assoc($r);
    		$calendarHTML .= '<li><strong>Contest Entry Starts:</strong><br />'.date("M jS @ h:ia",strtotime($row['de'])).'</li>';
    		if($row['dv1']!='0000-00-00 00:00:00'){ $calendarHTML .= '<li><strong>Voting Starts:</strong><br />'.date("M jS @ h:ia",strtotime($row['dv1'])).'</li>'; }
    		if($row['dv2']!='0000-00-00 00:00:00'){ $calendarHTML .= '<li><strong>Round 2 Voting Starts:</strong><br />'.date("M jS @ h:ia",strtotime($row['dv2'])).'</li>'; }
			if($row['dv3']!='0000-00-00 00:00:00'){ $calendarHTML .= '<li><strong>Round 3 Voting Starts:</strong><br />'.date("M jS @ h:ia",strtotime($row['dv3'])).'</li>'; }
            if($row['dw']!='0000-00-00 00:00:00'){ $calendarHTML .= '<li><strong>Voting Ends / Winner Announced:</strong><br />'.date("M jS @ h:ia",strtotime($row['dw'])).'</li>'; }

    		$calendarHTML .= '</li></ul>';
    		return $calendarHTML;
    	}
    }


    /**
     * Manages individual entrant data manipulation
     * @param  array $vars      Form entry data
     * @param  string $method    Method
     * @param  integer $contestID ID of contest to use
     * @return boolean         
     */
    function doEntry($vars,$method,$contestID=''){
        
        switch($method){
            case 'add':
            
            // see if they have already entered
            $q = mysql_query("SELECT count(id)
                FROM " . ENTRANT_TABLE . "
                WHERE contest_id = '". $contestID . "'
                AND email = '" . trim($vars['email']) . "'
                ");
            
            // not entered yet
            if(mysql_result($q,0,'count(id)')<1){

                $q = sprintf("
                INSERT into " . ENTRANT_TABLE ."
                (contest_id,date_entered, fname,lname,pgname,email,phone,userfile,social_link,misc_1,misc_2,misc_3,status)
                values
                ('%s',NOW(),'%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','1')",
                mysql_real_escape_string($contestID),
                mysql_real_escape_string(trim($vars['fname'])),
                mysql_real_escape_string(trim($vars['lname'])),
                mysql_real_escape_string(trim($vars['pgname'])),
                mysql_real_escape_string(trim($vars['email'])),
                mysql_real_escape_string(trim($vars['phone'])),
                mysql_real_escape_string(trim($vars['userfile'])),
                mysql_real_escape_string(trim($vars['social_link'])),
                mysql_real_escape_string(trim($vars['misc_1'])),
                mysql_real_escape_string(trim($vars['misc_2'])),
                mysql_real_escape_string(trim($vars['misc_3']))
                );
                //echo $r;
                $r = mysql_query($q);
                return true;
            }
            
            // already entered
            else {
                return false;
            }
            break;


        }
    }


    /**
     * Returns html of all active entrants
     * @param  integer $contestID     id of contest
     * @param  integer $contestType   type of contest
     * @param  integer $galleryNumber which gallery is active
     * @return string               html
     */
    function getActiveSlides($contestID, $contestType, $galleryNumber=''){
        // get total active entrants in contest
        $totq = mysql_query("
            SELECT count(id)
            FROM " . ENTRANT_TABLE . "
            WHERE contest_id = '". $contestID ."' 
            AND status=2"
            );
        
        $totEntrants = mysql_result($totq,0,'count(id)');
        
        // get total active and figure out how many galleries to show (1 or 4)
        if($totEntrants > SLIDES_PERGALLERY_MAX){ $perGallery = ceil($totEntrants/4); }
        else { $perGallery = $totEntrants; }

        // figure result subset according to gallery clieked
        $offset = 0;
        if(isset($galleryNumber)){ $offset = ($galleryNumber-1) * $perGallery; }

        // get result subset
        $q = mysql_query("
            SELECT *
            FROM " . ENTRANT_TABLE . "
            WHERE contest_id = '". $contestID ."' 
            AND status=2 
            ORDER BY pgname asc, fname asc, lname asc
            LIMIT $offset,$perGallery");

        if(mysql_num_rows($q)>0){ 
            $slides = array();
            while ($slide = mysql_fetch_assoc($q)){
                $slide['lname'] = substr($slide['lname'],0,1) . '.';
                if($contestType==1){ $slide['embedCode'] = '<img src="'.CONTEST_IMG_PATH.$slide['userfile'].'" />'; }
                if($contestType==2){ $slide['embedCode'] = Utility::getEmbed($slide['social_link'],'l'); }
                if($contestType==3){ 
                    $slide['embedCode'] = Utility::getEmbed($slide['social_link'],'l');
                    $slide['img'] = '<a class="fancybox" href="'.CONTEST_IMG_PATH.$slide['userfile'].'"><img src="'.CONTEST_IMG_PATH.$slide['userfile'].'" style="margin-left:20px; width:100px!important;" /></a>';
                    $slide['fname'] = $slide['pgname'];
                    $slide['lname'] = '';
                }
                $slides[] = $slide;

            }
            return $slides;
        }
    }


    /**
     * Returns HTML to generate gallery thumbnails
     * @param  integer $contestID     id of contest
     * @param  integer $contestType   type of contest
     * @param  string $urlCode       url code of contest
     * @param  integer $galleryNumber which gallery is active
     * @return string               html
     */
    function getGalleryThumbs($contestID, $contestType, $urlCode, $galleryNumber=''){
        $totq = mysql_query("
            SELECT count(id)
            FROM " . ENTRANT_TABLE . "
            WHERE contest_id = '". $contestID ."' 
            AND status=2"
            );
        
        $totEntrants = mysql_result($totq,0,'count(id)');
        
        // generate thumbs
        if($totEntrants > SLIDES_PERGALLERY_MAX){

            $r = mysql_query("
            SELECT id,userfile,social_link,fname,lname
            FROM " . ENTRANT_TABLE . "
            WHERE contest_id = '". $contestID ."' 
            AND status=2 
            ORDER BY fname asc, lname asc
            ");

            // get total number of galleries
            if($totEntrants >= 4){ $perGallery = ceil($totEntrants/4); }
            else { $perGallery = ceil($totEntrants/SLIDES_PERGALLERY_MAX); }
            $totGalleries = ceil($totEntrants/$perGallery);
            //echo $totGalleries;
            $html = '';



            $i=1;

            while($i <= $totGalleries){
                $startRow = ($i-1)*$perGallery;
                $endRow = $startRow+($perGallery-1);
                if($i==$totGalleries){ $endRow = (mysql_num_rows($r)-1); }

                // get some specified variables
                switch($contestType){
                    
                    // photo upload contest
                    case 1:
                    $thumb = mysql_result($r,$startRow,'userfile');
                    $thumbImg = '<img src="'.CONTEST_IMG_PATH.$thumb.'" />';
                    $startName = mysql_result($r,$startRow,'fname') . ' ' . substr(mysql_result($r,$startRow,'lname'),0,1);
                    $endName = mysql_result($r,$endRow,'fname') . ' ' . substr(mysql_result($r,$endRow,'lname'),0,1);
                    break;

                    // social embed
                    case 2:
                    $thumbImg = '';
                    $startName = mysql_result($r,$startRow,'fname') . ' ' . substr(mysql_result($r,$startRow,'lname'),0,1);
                    $endName = mysql_result($r,$endRow,'fname') . ' ' . substr(mysql_result($r,$endRow,'lname'),0,1);
                    break;
                }

                if($i==$galleryNumber){ $class='thumb-active'; }
                else { $class=''; }
                $html .= '<div class="thumb-box ' . $class . '"><a href="view.php?' . $urlCode . '&g=' . $i . '">'.stripslashes($startName).'<br />to<br />'.stripslashes($endName).'.<br />'.$thumbImg.'</a></div>';
                
                $i++;
            }

            return $html;

        }
        else {
            return '';
        }

    }


    /**
     * Gets entrants name
     * @param  integer $entrantID id of entrant
     * @return string            First name and last initial
     */
    function getEntrantName($entrantID){
        $q = mysql_query("SELECT fname,lname 
            FROM " . ENTRANT_TABLE . "
            WHERE id = '" . $entrantID ."'
            ");
        return mysql_result($q,0,'fname').' '.substr(mysql_result($q,0,'lname'),0,1).'.';
    }

    
    /**
     * Returns winner name and image to page
     * @param  integer $contestID id of contest
     * @return string           html
     */
    function getWinnerMessage($contestID){
        $q = mysql_query("SELECT count(v.entrant_id) as votes, e.fname,lname, e.userfile, e.social_link 
            FROM " . ENTRANT_TABLE . " e 
            LEFT JOIN " . VOTE_TABLE . " v on v.entrant_id=e.id
            WHERE v.contest_id = '" . $contestID . "' ORDER BY count(v.entrant_id)
            DESC LIMIT 1
            ");
        $entrant = mysql_fetch_assoc($q);
       
        $html = '<h3>And the winner is...</h3>';
        $html .= '<p style="margin-top:-20px!important;"><strong>' . $entrant['fname'] . ' ' . substr($entrant['lname'],0,1) . '.</strong><br />';
        if($entrant['social_link']!=''){ $html .= Utility::getEmbed($entrant['social_link'],'s'); } 
        else { $html .= '<img src="'. CONTEST_IMG_PATH . $entrant['userfile'] .'" />'; }
        $html .= '</p>';
        return $html;

    }	

}
