cc-upload-contesting
======================
traviswachendorf@iheartmedia.com

* This app will be programmed in PHP 4.4.9 *

Photo upload contesting app for CC

These files are meant to create:
- Front end
	Index page
	Entry Page
	Vote/View Page
- Admin
	Contest Overview page
	Contest Create page
	Contest Update page
	Contest Entrant Overview page


Server Requirements:
- MySQL Database

Installation Instructions
- 1. Create a database on your MYSQL Server
- 2. Run -cc_upload.sql- in your MYSQL client to create tables
- 3. Create at least 1 user in the -- cc_upload_users_admin -- table using MySQL client of your choice
- 4. Set up database connection credentials and filepath constants in -lib/config.inc.php- file and comment out local settings
- 6. You should be good to go