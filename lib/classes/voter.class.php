<?php

/**
 * CC Upload Contesting Voter Class
 * Original Creation Date 05.2014
 * Wherein we create and manipulate the voter object
 */
class Voter{


	/**
	 * checks to see if voter is registered in the system or not
	 * @param  string $emailAddress voters email
	 * @return boolean            
	 */
	function checkForRegistered($emailAddress){
		$q = sprintf("SELECT id,status 
			FROM " . VOTER_TABLE . " 
			WHERE email = '%s'
			",
			mysql_real_escape_string(trim($emailAddress))
			);

		$r = mysql_query($q);
        
        if(mysql_num_rows($r)!=0){ return mysql_fetch_assoc($r); }
        else {
        	$voter = array();
        	$voter['status']=false;
        	return $voter;
        }
		
	}


	/**
	 * sends user their confirmation email with link to activate their account
	 * @param  string $emailAddress new voters email address
	 * @param  string $urlCode      contest url code
	 * @param  integer $entrantID   entrant their are placing their vote for
	 */
	function sendConfirmation($emailAddress,$urlCode,$entrantID,$voter){
		if(Utility::isValidEmail(trim($emailAddress))){

			if($voter['status']===false){
				// insert user into table - pending
				$ipAddress = Utility::getUserIP();
				$q = sprintf("INSERT into
					" . VOTER_TABLE ." 
					(email,date_registered,ip_address,status) 
					VALUES 
					('%s',NOW(),'". $ipAddress ."',1)",
					mysql_real_escape_string(trim($emailAddress)));
				$r = mysql_query($q);
				$voterID = mysql_insert_id();
			}

			else {
				$voterID = $voter['id'];
			}

			// send email
			$subject = 'Voter registration and confirmation - Clear Channel Contesting';
			$header  ="MIME-Version: 1.0\n";
			$header .= "Content-type: text/html; charset=iso-8859-1\n";
			$header .= "From: Clear Channel Contesting <noreply@clearchannel.com>\r\n";
			$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				  <html xmlns="http://www.w3.org/1999/xhtml">
				  <head>
				  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				  <title>Untitled Document</title>
				  </head>
				  
				  <body>
				  <center>
				  <table cellpadding="0" cellspacing="0" border="0" bgcolor="#d1d1d1">
				  <tr>
				  <td>
				  <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#e6e6e6">
				  <tr>
				  <td>
				  <p style="margin:30px;">Thank you for registering to vote in <strong>Clear Channel Web Site Contesting</strong>.</p>
				  <p style="margin:30px;">Since this is your very first time voting, we just need to confirm your email address.</p>
				  <p style="margin:30px;">Please click the below link to confirm your email address:<br />
				  <a href="http://'.$_SERVER['HTTP_HOST'].'/common/contest/confirm.php?'.$urlCode.'&vid='.$voterID.'&eid='.$entrantID.'">CLICK HERE TO CONFIRM</a></p>
				  </td>
				  </tr>
				  </table>
				  </td>
				  </tr>
				  </table>
				  <table width="600" cellpadding="8" cellspacing="0" border="0" bgcolor="#ffffff">
				  <tr>
				  <td align="center">&copy; '.date("Y").' Clear Channel Media &amp; Entertainment</td>
				  </tr>
				  </table>
				  </center>
				  </body>
				  </html>';

			// send email		
			mail(trim($emailAddress), $subject, $message, $header);
		}

		else { echo "NOPE"; }
		

	}


	/**
	 * updates voter from pending to active and places first vote
	 * @param  array $vars      voter and entrant ids
	 * @param  integer $contestID contest id
	 * @return boolean        
	 */
	function activateUser($vars,$contestID){
		$r = mysql_query("SELECT 
			status from " . VOTER_TABLE . "
			WHERE id = '" . $vars['vid'] . "'");

		// user is still pending, we activate them
		if(mysql_result($r,0,'status')==1){
			
			$vars['cid'] = $contestID;
			$r1 = mysql_query("UPDATE " . VOTER_TABLE . "
				SET 
				status=2
				WHERE id = '" . $vars['vid'] . "'
				");

			// place first vote
			if($this->doVote($vars)){ return true; }
		}

		// user is already active we ignore and return false
		else {
			return false;
		}

	}


	/**
	 * Return voter id when given email address
	 * @param  string $email voters email address
	 * @return integer        voter id
	 */
	function getVoterID($email){
		$q = sprintf("SELECT id 
			FROM " . VOTER_TABLE . "
			WHERE email= '%s'",
			mysql_real_escape_string(trim($email)));
		$r = mysql_query($q);
		return mysql_result($r,0,'id');
	}


	/**
	 * Place a vote
	 * @param  array $vars user, contest and entrant ids
	 * @return boolean     
	 */
	function doVote($vars){
		
		// get todays date
		$today = date("Y-m-d 00:00:00");

		// see if there's already a vote in for today
		$q = sprintf("SELECT voter_id  
			FROM " . VOTE_TABLE . " 
			WHERE voter_id = '%s' 
			AND vote_date >= '" . $today . "'",
			mysql_real_escape_string($vars['vid']));

		$r = mysql_query($q);
		
		// no vote in the sytem for toda
		if(mysql_num_rows($r)==0){
			
			/* add vote to entrant record
			$q1 = sprintf("UPDATE " . ENTRANT_TABLE . "
				SET votes = votes+1 
				WHERE id = '%s'",
				mysql_real_escape_string($vars['cid'])
				);
			$r1 = mysql_query($q1); */

			// add vote record to vote table
			$ipAddress = Utility::getUserIP();
			$q2 = sprintf("INSERT into " . VOTE_TABLE . "
				(contest_id, voter_id, ip_address, vote_date, entrant_id) 
				VALUES 
				('%s','%s','" . $ipAddress . "', NOW(), '%s')",
				mysql_real_escape_string($vars['cid']),
				mysql_real_escape_string($vars['vid']),
				mysql_real_escape_string($vars['eid'])
				);

			$r2 = mysql_query($q2);
			return true;
		}

		// already voted today
		else {
			return false;
		}
	}
	

}