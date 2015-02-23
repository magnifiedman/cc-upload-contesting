<?php
/**
 * CC Upload Contesting Utility Class
 * Original Creation Date 05.2014
 * Wherein we create and manipulate the utility object
 */
class Utility {
	

	/**
	 * Gets users IP address
	 * @return string ip address
	 */
	function getUserIP() {
		$ip = '';
		
		if (isset($_SERVER)){
			if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
			elseif (isset($_SERVER["HTTP_CLIENT_IP"])) { $ip = $_SERVER["HTTP_CLIENT_IP"]; }
			else { $ip = $_SERVER["REMOTE_ADDR"]; }
		}
		
		else {
			if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) { $ip = getenv( 'HTTP_X_FORWARDED_FOR' ); }
			elseif ( getenv( 'HTTP_CLIENT_IP' ) ) { $ip = getenv( 'HTTP_CLIENT_IP' ); }
			else { $ip = getenv( 'REMOTE_ADDR' ); }
		}
		
		return $ip;
	}


	/**
	 * Generates URL friendly code
	 * @param  string $title
	 * @return string URL friendly code
	 */
	function urlCode($title){
			$replace="-";
			$title1 = strtolower(preg_replace("/[^a-zA-Z0-9\.]/",$replace,$title));
			$title2 = str_replace('.','',$title1);
			$title3 = str_replace('!','',$title2);
			$title4 = str_replace('?','',$title3);
			$title5 = str_replace('--','-',$title4);
			return $title5;
	}


	/**
	 * Generates a clean filename for user uploaded file
	 * @param  string $filename
	 * @return string adjusted filename
	 */
	function cleanFilename($filename){
			$replace="-";
			$filename = strtolower(date("mdyHis").'-'.preg_replace("/[^a-zA-Z0-9\.]/",$replace,$filename));
			return $filename;
	}


	/**
	 * Checks for valid email address
	 * @param  string  $email email address
	 * @return boolean        
	 */
	function isValidEmail($email) {
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
        	return false;
		}

        // Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}

		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}

            for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
            }
        }

        return true;
    }


	/**
	 * [getEmbed description]
	 * @param  string $link
	 * @return string full embed code
	 */
	function getEmbed($link,$size){
		if($size=='s'){
			$w = 570;
			$yheight = 428;
			$iheight = 661;
			$vheight = 320;
		}

		if($size=='l'){
			$w = 610;
			$yheight = 428;
			$iheight = 661;
			$vheight = 320;
		}

		// youtube
		if(substr_count($link,'youtu')==1){
			$id = substr($link,-11);
			$embedCode = '<iframe width="' . $w . '" height="' . $yheight . '" src="http://www.youtube.com/embed/' . $id . '?hd=1&rel=0" frameborder="0" allowfullscreen></iframe>';
		}

		// instagram
		if(substr_count($link,'insta')==1){
			$id = substr($link,-11);
			$embedCode = '<iframe src="//instagram.com/p/' . $id . 'embed/" width="' . $w . '" height="' . $iheight . '" frameborder="0" scrolling="no" allowtransparency="true"></iframe>';
		}
		
		// vimeo
		if(substr_count($link,'vimeo')==1){
			$id = substr($link,-8);
			$embedCode = '<iframe src="http://player.vimeo.com/video/' . $id . '?title=0&amp;byline=0&amp;portrait=0" width="' . $w . '" height="' . $vheight . '" frameborder="0"></iframe>';
		}

		// soundcloud 
		if(substr_count($link,'soundcloud')>=1){
			$embedCode = '<div style="margin:20px;">'.stripslashes($link).'</div>';
		}

		return $embedCode;
	}


	/**
	 * Form select for 24 hrs.
	 * @param  string $fieldName name of form field to create
	 * @param  string $curHour   preset value
	 * @return [type]            [description]
	 */
	function hourSelect($fieldName, $curHour=''){
		$html .= '<select name="' . $fieldName . '" class="halfcol">';
		$html .= '<option value=""></option>';
		$html .= '<option value="00:00"';
		if($curHour=='00:00'){ $html .= ' selected="selected"'; }
		$html .= '>12:00 Midnight</option>';
		$html .= '<option value="01:00"';
		if($curHour=='01:00'){ $html .= ' selected="selected"'; }
		$html .= '>1:00 am</option>';
		$html .= '<option value="02:00"';
		if($curHour=='02:00'){ $html .= ' selected="selected"'; }
		$html .= '>2:00 am</option>';
		$html .= '<option value="03:00"';
		if($curHour=='03:00'){ $html .= ' selected="selected"'; }
		$html .= '>3:00 am</option>';
		$html .= '<option value="04:00"';
		if($curHour=='04:00'){ $html .= ' selected="selected"'; }
		$html .= '>4:00 am</option>';
		$html .= '<option value="05:00"';
		if($curHour=='05:00'){ $html .= ' selected="selected"'; }
		$html .= '>5:00 am</option>';
		$html .= '<option value="06:00"';
		if($curHour=='06:00'){ $html .= ' selected="selected"'; }
		$html .= '>6:00 am</option>';
		$html .= '<option value="07:00"';
		if($curHour=='07:00'){ $html .= ' selected="selected"'; }
		$html .= '>7:00 am</option>';
		$html .= '<option value="08:00"';
		if($curHour=='08:00'){ $html .= ' selected="selected"'; }
		$html .= '>8:00 am</option>';
		$html .= '<option value="09:00"';
		if($curHour=='09:00'){ $html .= ' selected="selected"'; }
		$html .= '>9:00 am</option>';
		$html .= '<option value="10:00"';
		if($curHour=='10:00'){ $html .= ' selected="selected"'; }
		$html .= '>10:00 am</option>';
		$html .= '<option value="11:00"';
		if($curHour=='11:00'){ $html .= ' selected="selected"'; }
		$html .= '>11:00 am</option>';
		$html .= '<option value="12:00"';
		if($curHour=='12:00'){ $html .= ' selected="selected"'; }
		$html .= '>12:00 Noon</option>';
		$html .= '<option value="13:00"';
		if($curHour=='13:00'){ $html .= ' selected="selected"'; }
		$html .= '>1:00 pm</option>';
		$html .= '<option value="14:00"';
		if($curHour=='14:00'){ $html .= ' selected="selected"'; }
		$html .= '>2:00 pm</option>';
		$html .= '<option value="15:00"';
		if($curHour=='15:00'){ $html .= ' selected="selected"'; }
		$html .= '>3:00 pm</option>';
		$html .= '<option value="16:00"';
		if($curHour=='16:00'){ $html .= ' selected="selected"'; }
		$html .= '>4:00 pm</option>';
		$html .= '<option value="17:00"';
		if($curHour=='17:00'){ $html .= ' selected="selected"'; }
		$html .= '>5:00 pm</option>';
		$html .= '<option value="18:00"';
		if($curHour=='18:00'){ $html .= ' selected="selected"'; }
		$html .= '>6:00 pm</option>';
		$html .= '<option value="19:00"';
		if($curHour=='19:00'){ $html .= ' selected="selected"'; }
		$html .= '>7:00 pm</option>';
		$html .= '<option value="20:00"';
		if($curHour=='20:00'){ $html .= ' selected="selected"'; }
		$html .= '>8:00 pm</option>';
		$html .= '<option value="21:00"';
		if($curHour=='21:00'){ $html .= ' selected="selected"'; }
		$html .= '>9:00 pm</option>';
		$html .= '<option value="22:00"';
		if($curHour=='22:00'){ $html .= ' selected="selected"'; }
		$html .= '>10:00 pm</option>';
		$html .= '<option value="23:00"';
		if($curHour=='23:00'){ $html .= ' selected="selected"'; }
		$html .= '>11:00 pm</option>';  
		$html .= '</select>';
		return $html;
	}


	/**
	 * Form select for contest type
	 * @param  integer $contestType current contest type
	 * @return string              html
	 */
	function contestTypeSelect($contestType){
		$html='';

		$html .= '<select name="contest_type" class="required">';
		$html .= '<option value="1" ';
		if($contestType==1){ $html .= 'selected="selected"'; }
		$html .= '>Photo Upload</option>';
		$html .= '<option value="2" ';
		if($contestType==2){ $html .= 'selected="selected"'; }
		$html .= '>Social Embed</option>';
		$html .= '<option value="3" ';
		if($contestType==3){ $html .= 'selected="selected"'; }
		$html .= '>Battle of the Bands (Image and embed)</option>';
		$html .= '</select>';
		return $html;
	}

}
