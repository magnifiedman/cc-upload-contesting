<?php
/**
 * CC Upload Contesting Admin Class
 * Original Creation Date 05.2014
 * Wherein we create and manipulate the admin object
 */
class Admin{


	/**
	 * checks admin login credentials
	 * @param  array $vars email and password
	 * @return boolean            
	 */
	function doLogin($vars){
		$q = sprintf("SELECT count(id) 
			FROM ". ADMIN_USERS_TABLE . "
			WHERE email = '%s'
			AND pword = '%s'",
			mysql_real_escape_string(trim($vars['email'])),
			mysql_real_escape_string(trim($vars['pword']))
			);
		$r = mysql_query($q);
		if(mysql_result($r,0,'count(id)')>0){
			setcookie('adminLogged','y');
			return true;
		}
		else { return false; }
	}


	### contests ###
	

	/**
	 * gets total number of contests
	 * @return integer total contests in system
	 */
	function getTotalContests(){
		$r = mysql_query("SELECT count(id)
			FROM " . CONTEST_TABLE . "
			ORDER BY date_entry DESC");
		return mysql_result($r,0,'count(id)');
	}


	/**
     * Get's all contest details from the db
     * @return array contest details
     */
    function getContestDetails($id){
    	$r = mysql_query("
    		SELECT * 
    		FROM " . CONTEST_TABLE . "
    		WHERE id= '" . $id . "'
    		");
    	if(mysql_num_rows($r)>0){ return mysql_fetch_assoc($r); }
    	else { header("Location:index.php"); }
    }


	/**
	 * [getContestOverview description]
	 * @param  integer $page    page number
	 * @param  integer $perpage resultes per page
	 * @return string html code 
	 */
	function getContestOverview($page='',$perpage=''){
		if(!isset($page)){ $page=1; }
		$offset = ($page-1)*$perpage;

		$r = mysql_query("SELECT *
			FROM " . CONTEST_TABLE . "
			ORDER BY date_winner DESC 
			LIMIT " . $offset . ", " . $perpage);

		$hiddenHTML = '';

		if($r){
			// pagination
			$class='';
			$html = '<table class="adminTable">';
			$html .= '<tr>';
			$html .= '<th>Start Date</th><th>End Date</th><th>Contest</th><th>Entrants</th><th class="tcenter">Manage</th><th class="tcenter">Preview</th><th class="tcenter">Edit</th><th class="tcenter"></th>';
			$html .= '</tr>';

			while($contest = mysql_fetch_assoc($r)){
				$totalEntrants = $this->getTotalEntrantsContest($contest['id']);
				if($contest['date_winner']<date("Y-m-d")){ $class='class="expired"'; }
				$html .= '<tr ' . $class . '>';
				$html .= '<td>' . date("m.d.Y",strtotime($contest['date_entry'])) . '</td>';
				$html .= '<td>' . date("m.d.Y",strtotime($contest['date_winner'])) . '</td>';
				$html .= '<td>' . stripslashes($contest['name']) . '</td>';
				$html .= '<td>' . $totalEntrants . '</td>';
				$html .= '<td class="tcenter"><a href="contest-manage.php?id=' . $contest['id'] . '"><i class="fa fa-user"></i> Manage</a></td>';
				$html .= '<td class="tcenter"><a href="/common/contest/?' . $contest['url_code'] . '" target="_blank"><i class="fa fa-eye"></i> Preview</a></td>';
				$html .= '<td class="tcenter"><a href="contest-edit.php?id=' . $contest['id'] . '"><i class="fa fa-edit"></i> Edit</a></td>';
				$html .= '<td class="tcenter"><a href="#delete' . $contest['id'] . '" class="fancybox"><i class="fa fa-trash-o"></i></a></td>';
				$html .= '</tr>';
				$hiddenHTML .= '<div id="delete' . $contest['id'] . '" style="width:400px;display:none;"><h3>Are you sure you want to delete:</h3><h2>'.$contest['name'].'?</h2><form action="" method="post"><input type="hidden" name="deleteForm" value="y" /><input type="hidden" name="id" value="' . $contest['id'] . '" /><input type="submit" class="button blue" value="Delete" /></form></div>'."\n";
				$class='';

			}

			$html .= '</table>';
			return $html.$hiddenHTML;
		}
		else {
			return '<p class="tcenter error"><i class="fa fa-exclamation-triangle"></i> No current contests in the system.</p>';
		}

	}


	/**
	 * gets search of contests
	 * @param  string $searchStr  search query
	 * @param  integer $page    page number
	 * @param  integer $perpage results per page
	 * @return string html code 
	 */
	function getContestSearch($searchStr,$page='',$perpage=''){
		if(!isset($page)){ $page=1; }
		$offset = ($page-1)*$perpage;

		$r = mysql_query("SELECT * 
			FROM " . CONTEST_TABLE . "
			WHERE name like '%" . $searchStr . "%' 
			");


		if(mysql_num_rows($r)>0){
			// pagination
			
			$html = '<table class="adminTable">';
			$html .= '<tr>';
			$html .= '<th>Start Date</th><th>Contest</th><th>Entrants</th><th class="tcenter">Manage</th><th class="tcenter">Preview</th><th class="tcenter">Edit</th><th class="tcenter"></th>';
			$html .= '</tr>';

			while($contest = mysql_fetch_assoc($r)){
				$totalEntrants = $this->getTotalEntrantsContest($contest['id']);
				$html .= '<tr>';
				$html .= '<td>' . date("m.d.Y",strtotime($contest['date_entry'])) . '</td>';
				$html .= '<td>' . $contest['name'] . '</td>';
				$html .= '<td>' . $totalEntrants . '</td>';
				$html .= '<td class="tcenter"><a href=""><i class="fa fa-user"></i> Manage</a></td>';
				$html .= '<td class="tcenter"><a href="/common/contest/?' . $contest['url_code'] . '" target="_blank"><i class="fa fa-eye"></i> Preview</a></td>';
				$html .= '<td class="tcenter"><a href=""><i class="fa fa-edit"></i> Edit</a></td>';
				$html .= '<td class="tcenter"><a href=""><i class="fa fa-trash-o"></i> Delete</a></td>';
				$html .= '</tr>';
				$hiddenHTML .= '<div id="delete' . $contest['id'] . '" style="width:400px;display:none;"><h3>Are you sure you want to delete:</h3><h2>'.$contest['name'].'?</h2><form action="" method="post"><input type="hidden" name="deleteForm" value="y" /><input type="hidden" name="id" value="' . $contest['id'] . '" /><input type="submit" class="button blue" value="Delete" /></form></div>'."\n";
			}

			$html .= '</table>';
			return $html.$hiddenHTML;
		}
		else {
			return '<p class="tcenter error"><i class="fa fa-exclamation-triangle"></i> No search results.</p>';
		}

	}


	/**
	 * Generates URL friendly code
	 * @param  string $title
	 * @return string URL friendly code
	 */
	function urlCode($title){
			$replace="-";
			$title = strtolower(preg_replace("/[^a-zA-Z0-9\.]/",$replace,$title));
			$title = str_replace('.','',$title);
			$title = str_replace('!','',$title);
			$title = str_replace('?','',$title);
			$title = str_replace('--','-',$title);
			return $title;
	}


	/**
	 * Adds and updates contest
	 * @param  array $vars   contest details
	 * @param  string $method add or edit
	 * @return boolean        
	 */
	function doContest($vars,$method){
		switch($method){
			case 'add':
			$urlCode = $this->urlCode($vars['name']);
			$q = sprintf("INSERT into " . CONTEST_TABLE . "
				(status,contest_type,name,description,keywords,heading,body,url_code,date_entry,date_vote_1,date_vote_2,date_vote_3,date_winner,header_img,thumb_img,rules_form, release_form, sponsor_name_1,sponsor_img_1,sponsor_url_1,sponsor_text_1,sponsor_name_2,sponsor_img_2,sponsor_url_2,sponsor_text_2,sponsor_name_3,sponsor_img_3,sponsor_url_3,sponsor_text_3)
				VALUES 
				(1,'" . $vars['contest_type'] . "', '%s', '%s', '%s', '%s', '%s', '%s', '" . $urlCode ."', '" . $vars['date_entry']." ".$vars['hour_entry'] . "', '" . $vars['date_vote_1']." ".$vars['hour_vote_1'] . "', '" . $vars['date_vote_2']." ".$vars['hour_vote_2'] . "', '" . $vars['date_vote_3']." ".$vars['hour_vote_3'] . "', '" . $vars['date_winner']." ".$vars['hour_winner'] . "', '" . $_POST['header_img'] . "', '" . $_POST['thumb_img'] . "', '" . $_POST['rules_form'] . "', '" . $_POST['release_form'] . "', '%s', '" . $_POST['sponsor_img_1'] . "', '%s', '%s', '%s', '" . $_POST['sponsor_img_2'] . "', '%s', '%s', '%s', '" . $_POST['sponsor_img_1'] . "', '%s', '%s')",
				mysql_real_escape_string(htmlspecialchars($vars['name'],ENT_QUOTES)),
				mysql_real_escape_string(htmlspecialchars($vars['description'],ENT_QUOTES)),
				mysql_real_escape_string($vars['keywords']),
				mysql_real_escape_string($vars['esid']),
				mysql_real_escape_string(htmlspecialchars($vars['heading'],ENT_QUOTES)),
				mysql_real_escape_string($vars['body_text']),
				mysql_real_escape_string($vars['sponsor_name_1']),
				mysql_real_escape_string($vars['sponsor_url_1']),
				mysql_real_escape_string($vars['sponsor_text_1']),
				mysql_real_escape_string($vars['sponsor_name_2']),
				mysql_real_escape_string($vars['sponsor_url_2']),
				mysql_real_escape_string($vars['sponsor_text_2']),
				mysql_real_escape_string($vars['sponsor_name_3']),
				mysql_real_escape_string($vars['sponsor_url_3']),
				mysql_real_escape_string($vars['sponsor_text_3'])
				);

			mysql_query($q);
			return true;
			break;

			case 'edit':
				$urlCode = $this->urlCode($vars['name']);
				$q = sprintf("UPDATE " . CONTEST_TABLE . "
				set contest_type = '%s',
				name = '%s',
				suspend_voting = '" . $vars['suspend_voting'] . "',
				url_code = '" . $urlCode . "',
				description = '%s',
				keywords = '%s',
				esid = '%s',
				heading = '%s',
				body = '%s',
				date_entry = '" . $vars['date_entry']." ".$vars['hour_entry'] . "',
				date_vote_1 = '" . $vars['date_vote_1']." ".$vars['hour_vote_1'] . "',
				date_vote_2 = '" . $vars['date_vote_2']." ".$vars['hour_vote_2'] . "',
				date_vote_3 = '" . $vars['date_vote_3']." ".$vars['hour_vote_3'] . "',
				date_winner = '" . $vars['date_winner']." ".$vars['hour_winner'] . "',
				sponsor_name_1 = '%s', 
				sponsor_url_1 = '%s',
				sponsor_text_1 = '%s',
				sponsor_name_2 = '%s', 
				sponsor_url_2 = '%s',
				sponsor_text_2 = '%s',
				sponsor_name_3 = '%s', 
				sponsor_url_3 = '%s',
				sponsor_text_3 = '%s'
				WHERE id = '" . $vars['id'] . "'",
				mysql_real_escape_string($vars['contest_type']),
				mysql_real_escape_string(htmlspecialchars($vars['name'],ENT_QUOTES)),
				mysql_real_escape_string(htmlspecialchars($vars['description'],ENT_QUOTES)),
				mysql_real_escape_string($vars['keywords']),
				mysql_real_escape_string($vars['esid']),
				mysql_real_escape_string(htmlspecialchars($vars['heading'],ENT_QUOTES)),
				mysql_real_escape_string($vars['body_text']),
				mysql_real_escape_string($vars['sponsor_name_1']),
				mysql_real_escape_string($vars['sponsor_url_1']),
				mysql_real_escape_string($vars['sponsor_text_1']),
				mysql_real_escape_string($vars['sponsor_name_2']),
				mysql_real_escape_string($vars['sponsor_url_2']),
				mysql_real_escape_string($vars['sponsor_text_2']),
				mysql_real_escape_string($vars['sponsor_name_3']),
				mysql_real_escape_string($vars['sponsor_url_3']),
				mysql_real_escape_string($vars['sponsor_text_3'])
				);
				mysql_query($q);
				//echo $q;
                return true;
			break;

			case 'edit-files':
                $r = mysql_query("UPDATE " . CONTEST_TABLE . "
                	set header_img = '" . $vars['header_img'] . "',
                	thumb_img = '" . $vars['thumb_img'] . "',
					rules_form = '" . $vars['rules_form'] . "',
					release_form = '" . $vars['release_form'] . "',
					sponsor_img_1 = '" . $vars['sponsor_img_1'] . "',
					sponsor_img_2 = '" . $vars['sponsor_img_2'] . "',
					sponsor_img_3 = '" . $vars['sponsor_img_3'] . "'
					WHERE id = '" . $_GET['id'] . "'
                	");
                return true;
            break;


		}
	}


	/**
	 * deletes contest from system
	 * @param  integer $contestID id of contest
	 * @return none
	 */
	function deleteContest($contestID){
		$r = mysql_query("DELETE from
			" . CONTEST_TABLE . "
			WHERE id = '" . $contestID . "'
			");
	}


	### entrants ###
	

	/**
	 * gets total number of entrants in system
	 * @return integer total entrants in system
	 */
	function getTotalEntrants(){
		$r = mysql_query("SELECT count(id)
			FROM " . ENTRANT_TABLE . "
			GROUP BY email 
			ORDER BY lname DESC");
		return mysql_result($r,0,'count(id)');
	}


	/**
	 * gets total number of entrants in contest
	 * @return integer total entrants in contest
	 */
	function getTotalEntrantsContest($contestID){
		$r = mysql_query("SELECT count(id)
			FROM " . ENTRANT_TABLE . "
			WHERE contest_id = '" . $contestID . "'
			");
		return mysql_result($r,0,'count(id)');
	}


	/**
	 * get total number of contests entrant has entered
	 * @param  string email entrant email address
	 * @return integer     number of contests
	 */
	function getTotalEntries($email){
		$r = mysql_query("SELECT count(id)
			FROM " . ENTRANT_TABLE . "
			WHERE email= '". $email . "'
			");
		return mysql_result($r,0,'count(id)');
	}


	/**
	 * get total number of contests entrant has entered
	 * @param  string email entrant email address
	 * @return integer     number of contests
	 */
	function getTotalWins($email){
		$r = mysql_query("SELECT count(id)
			FROM " . ENTRANT_TABLE . "
			WHERE email= '". $email . "' 
			AND status = 3
			");
		return mysql_result($r,0,'count(id)');
	}


	/**
	 * cets overview of contest entrants
	 * @param  integer $page    page number
	 * @param  integer $perpage results per page
	 * @return string html code 
	 */
	function getEntrantOverview($page='',$perpage=''){
		if(!isset($page)){ $page=1; }
		$offset = ($page-1)*$perpage;

		$r = mysql_query("SELECT *, MAX(date_entered) as last_entry
			FROM " . ENTRANT_TABLE . "
			GROUP BY email 
			ORDER BY fname ASC, date_entered DESC 
			LIMIT " . $offset . ", " . $perpage);


		if($r){
			// pagination
			
			$html = '<table class="adminTable">';
			$html .= '<tr>';
			$html .= '<th>Latest Entry</th><th>Entrant Name</th><th>Email</th><th>Phone</th><th class="tcenter">Entries</th><th class="tcenter">Wins</th>';
			$html .= '</tr>';

			while($entrant = mysql_fetch_assoc($r)){
				$totalEntries = $this->getTotalEntries($entrant['email']);
				$totalWins = $this->getTotalWins($entrant['email']);
				$html .= '<tr>';
				$html .= '<td>' . date("m.d.Y",strtotime($entrant['last_entry'])) . '</td>';
				$html .= '<td>' . stripslashes($entrant['fname']) . ' ' . $entrant['lname'] . '</td>';
				$html .= '<td class="tcenter"><a href="mailto:' . $entrant['email'] . '"><i class="fa fa-send"></i> ' . $entrant['email'] . '</a></td>';
				$html .= '<td class="tcenter">' . $entrant['phone'] . '</td>';
				$html .= '<td class="tcenter">' . $totalEntries . '</a></td>';
				$html .= '<td class="tcenter">' . $totalWins . '</a></td>';
				$html .= '</tr>';

			}

			$html .= '</table>';
			return $html;
		}
		else {
			return '<p class="tcenter"><i class="fa fa-exclamation-triangle"></i> No current entrants in the system.</p>';
		}

	}


	/**
	 * gets search of contest entrants
	 * @param  string $searchStr  search query
	 * @param  integer $page    page number
	 * @param  integer $perpage results per page
	 * @return string html code 
	 */
	function getEntrantSearch($searchStr,$page='',$perpage=''){
		if(!isset($page)){ $page=1; }
		$offset = ($page-1)*$perpage;

		if(substr_count($searchStr,'@')>0){ $whereSQL = "WHERE email like '%" . $searchStr . "%'"; }
		else { $whereSQL = "WHERE lname like '%" . $searchStr . "%' or fname like '%" . $searchStr . "%' or email like '%" . $searchStr . "%'"; }

		$r = mysql_query("SELECT *, MAX(date_entered) as last_entry
			FROM " . ENTRANT_TABLE . "
			" . $whereSQL ." 
			GROUP BY email
			ORDER BY lname ASC, date_entered DESC 
			");


		if(mysql_num_rows($r)>0){
			// pagination
			
			$html = '<table class="adminTable">';
			$html .= '<tr>';
			$html .= '<th>Latest Entry</th><th>Entrant Name</th><th>Email</th><th>Phone</th><th class="tcenter">Entries</th><th class="tcenter">Wins</th>';
			$html .= '</tr>';

			while($entrant = mysql_fetch_assoc($r)){
				$totalEntries = $this->getTotalEntries($entrant['email']);
				$totalWins = $this->getTotalWins($entrant['email']);
				$html .= '<tr>';
				$html .= '<td>' . date("m.d.Y",strtotime($entrant['last_entry'])) . '</td>';
				$html .= '<td>' . $entrant['fname'] . ' ' . $entrant['lname'] . '</td>';
				$html .= '<td class="tcenter"><a href="mailto:' . $entrant['email'] . '"><i class="fa fa-send"></i> ' . $entrant['email'] . '</a></td>';
				$html .= '<td class="tcenter">' . $entrant['phone'] . '</td>';
				$html .= '<td class="tcenter">' . $totalEntries . '</a></td>';
				$html .= '<td class="tcenter">' . $totalWins . '</a></td>';
				$html .= '</tr>';

			}

			$html .= '</table>';
			return $html;
		}
		else {
			return '<p class="tcenter"><i class="fa fa-exclamation-triangle"></i> No search results.</p>';
		}

	}


	/**
	 * cets overview of contest entrants
	 * @param  integer $page    page number
	 * @param  integer $perpage results per page
	 * @return string html code 
	 */
	function getContestEntrants($contestID, $status, $page='',$perpage=''){
		if(!isset($page)){ $page=1; }
		$offset = ($page-1)*$perpage;

		if($status==1){ $statusName = 'pending'; $orderBy='fname'; $sort='asc'; }
		if($status==2){ $statusName = 'active'; $orderBy='votes'; $sort='desc'; }

		$return = array();

		if($status==2){ $r = mysql_query("SELECT e.*, COUNT(v.entrant_id) as votes
			FROM " . ENTRANT_TABLE . " e LEFT JOIN cc_upload_votes v on v.entrant_id=e.id
			WHERE e.contest_id = '" . $contestID . "'
			AND e.status = '" . $status . "'
			GROUP BY v.entrant_id, e.id
			ORDER BY " . $orderBy ." ". $sort .", fname ASC
			LIMIT " . $offset . ", " . $perpage); }

		if($status==1){ $r = mysql_query("SELECT *
			FROM " . ENTRANT_TABLE . "
			WHERE contest_id = '" . $contestID . "'
			AND status = '" . $status . "'
			ORDER BY fname ASC, fname ASC
			LIMIT " . $offset . ", " . $perpage); }

		$rtot = mysql_query("SELECT count(id)
			FROM " . ENTRANT_TABLE . "
			WHERE contest_id = '" . $contestID . "'
			AND status = '" . $status . "'
			ORDER BY lname ASC");
		
		$return['total'] = mysql_result($rtot,0,'count(id)');

		$return['html'] = '<form action="" method="post"><input type="hidden" name="statusForm" value="y" />';
		$return['html'] .= '<input type="submit" class="button blue status-button" value="Update Statuses" />';
		$return['html'] .= '<a class="button nobm" href="index.php"><i class="fa fa-long-arrow-left"></i> Back to Main</a>';
		$return['html'] .= '<div class="clear"></div>';
		$return['html'] .= $this->getStatusTabs($status,$contestID);

		if(mysql_num_rows($r)>0){
			// pagination
			
			$return['html'] .= '<table class="adminTable">';
			$return['html'] .= '<tr>';
			$return['html'] .= '<th>Entrant Name</th><th>Email</th><th>Phone</th><th class="tcenter">Votes</th><th class="tcenter">Preview</th><th class="tcenter">Status</th><th class="tcenter"></th>';
			$return['html'] .= '</tr>';

			while($entrant = mysql_fetch_assoc($r)){
				$votes = $this->getEntrantVotes($entrant['id'],$contestID);
				if($entrant['status']==1){ $status = 'Pending'; }
				if($entrant['status']==2){ $status = 'Active'; }
				if($entrant['status']==3){ $status = 'Winner'; }
				$return['html'] .= '<tr>';
				$return['html'] .= '<td>' . stripslashes($entrant['fname']) . ' ' . $entrant['lname'] . '</td>';
				$return['html'] .= '<td><a href="mailto:' . $entrant['email'] . '"><i class="fa fa-send"></i> ' . $entrant['email'] . '</a></td>';
				$return['html'] .= '<td>' . $entrant['phone'] . '</td>';
				$return['html'] .= '<td class="tcenter">' . $votes . '</td>';
				$return['html'] .= '<td class="tcenter"><a href="#preview' . $entrant['id'] . '" class="fancybox"><i class="fa fa-eye"></i> Preview</a></td>';
				$return['html'] .= '<td class="tcenter"><input type="checkbox" name="entrants[]" value="' . $entrant['id'] . '" /> ' . $status . '</td>';
				$return['html'] .= '<td class="tcenter"><a href="#delete' . $entrant['id'] . '" class="fancybox"><i class="fa fa-trash-o"></i></a></td>';
				$return['html'] .= '</tr>';
				if($entrant['social_link']!=''){ $embed = Utility::getEmbed($entrant['social_link'],'s'); }
				else { $embed = '<img src="'.CONTEST_IMG_PATH.$entrant['userfile'].'" />'; }
				$return['hiddenHTML'] .= '<div id="preview' . $entrant['id'] . '" style="width:640px;display:none;"><h2>'.$entrant['fname'].' ' . $entrant['lname'] . '</h2><h3>Submission Below</h3>' . $embed . '</div>'."\n";
				$return['hiddenHTML'] .= '<div id="delete' . $entrant['id'] . '" style="width:400px;display:none;"><h3>Are you sure you want to delete:</h3><h2>'.$entrant['fname'].' ' . $entrant['lname'] . '?</h2><form action="" method="post"><input type="hidden" name="deleteForm" value="y" /><input type="hidden" name="id" value="' . $entrant['id'] . '" /><input type="submit" class="button blue" value="Delete" /></form></div>'."\n";
			
			}

			$return['html'] .= '</table>';
			$return['html'] .= '</form>';
			$return['html'] = $return['html'].$return['hiddenHTML'];
			return $return;
		}
		else {
			$return['html'] .=  '<p class="tcenter error adminTable"><i class="fa fa-exclamation-triangle"></i> There are no current ' . $statusName . ' entrants in this contest.</p>';
			return $return;
		}

	}


	/**
	 * Get entrants total votes
	 * @param  integer entrantID id of entrant
	 * @param  integer $contestID id of contest
	 * @return integer           number of votes
	 */
	function getEntrantVotes($entrantID,$contestID){
		$r = mysql_query("SELECT count(entrant_id)
			FROM " . VOTE_TABLE . "
			WHERE entrant_id = '" . $entrantID . "'
			AND contest_id = '". $contestID . "'
			");
		return mysql_result($r,0,'count(entrant_id)');
	}


	/**
	 * deletes contest entrant
	 * @param  integer $entrantID id of entrant
	 * @return none
	 */
	function deleteEntrant($entrantID){
		$r = mysql_query("DELETE from
			" . ENTRANT_TABLE . "
			WHERE id = '" . $entrantID . "'
			");
	}


	/**
	 * update entrant status
	 * @param  array $entrantArray  id's of entrants to be updated
	 * @param  integer $currentStatus current status of entrants
	 * @return none
	 */
	function updateStatuses($entrantArray, $currentStatus){
		if($currentStatus==1){ $newStatus = 2; }
		if($currentStatus==2){ $newStatus = 1; }

		foreach($entrantArray as $entrantID){
			$r = mysql_query("UPDATE " . ENTRANT_TABLE . "
				set status = '" . $newStatus . "'
				WHERE id = '" . $entrantID ."'
				");
		}
	}

	
	/**
	 * Status tabs for overview table
	 * @param  integer $status    current status
	 * @param  integer $contestID current contest id
	 * @return string          html
	 */
	function getStatusTabs($status,$contestID){
		$html = '<div class="status-tabs">';
		if($status==1){ 
			$html .= '<span><i class="fa fa-flag"></i> Pending Entrants</span>';
			$html .= '<a href="contest-manage.php?id=' . $contestID . '&s=2"><i class="fa fa-thumbs-up"></i> Active Entrants</a>';
		}
		if($status==2){
			$html .= '<a href="contest-manage.php?id=' . $contestID . '&s=1"><i class="fa fa-flag"></i> Pending Entrants</a>';
			$html .= '<span><i class="fa fa-thumbs-up"></i> Active Entrants</span>';
		}
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
		return $html;
	}

}
