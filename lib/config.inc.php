<?php
/* Embeddable Photo Galleries Configuration File
 * Original Creation Date 05.2014
 * Wherein we define some constants for the system
 */

	error_reporting(E_ERROR);
	ini_set('display_errors', 1);

	// site paths
		define('BASE_URL',''); // use for cc
		define('ROOT_PATH',''); // use for cc


	// database connection - local development
		/*define('DB_HOST','localhost');
		define('DB_USER','root');
		define('DB_PASS','root');
		define('DB_NAME','devdb');*/


	// database connection - production
		define('DB_HOST','ccomr-common-user.ccrd.clearchannel.com');
		define('DB_USER','phoenix');
		define('DB_PASS','q(xTB5l3');
		define('DB_NAME','phoenix_projects');


	// database tables
		define('CONTEST_TABLE','cc_upload_contests');
		define('ENTRANT_TABLE','cc_upload_entrants');
		define('VOTER_TABLE','cc_upload_voters');
		define('ADMIN_USERS_TABLE','cc_upload_users_admin');


	// data
		define('RESULTS_PERPAGE','25');


	// ads
		define('AD_MARKET','PHOENIX-AZ');

		switch($_SERVER['HTTP_HOST']){

			// Localhost
			case 'localhost':
			define('AD_STATION','localhost');
			define('AD_FORMAT','LOCALHOST');
			break;

			// 104.7 KISSFM
			case 'www.1047kissfm.com':
			define('AD_STATION','kzzp-fm');
			define('AD_FORMAT','CHRPOP');
			break;

			// KNIX 102.5
			case 'www.knixcountry.com':
			define('AD_STATION','knix-fm');
			define('AD_FORMAT','COUNTRY');
			break;

			// MIX 96.9
			case 'www.mix969.com':
			define('AD_STATION','kmxp-fm');
			define('AD_FORMAT','ACHOTMODERN');
			break;

			// 99.9 KEZ
			case 'www.kez999.com':
			define('AD_STATION','kesz-fm');
			define('AD_FORMAT','ACMAINSTREAM');
			break;

			// 95.5 Mountain
			case 'www.955themountain.com':
			define('AD_STATION','kyot-fm');
			define('AD_FORMAT','CLASSICHITS');
			break;

			// Fox Sports 910
			case 'www.foxsports910':
			define('AD_STATION','kgme-am');
			define('AD_FORMAT','SPORTS');
			break;

			// 550 KFYI
			case 'www.kfyi.com':
			define('AD_STATION','kfyi-am');
			define('AD_FORMAT','NEWSTALK');
			break;

			// 550 KFYI
			case 'www.kfyi.biz':
			define('AD_STATION','koy-am');
			define('AD_FORMAT','NEWSTALK');
			break;
		}




	