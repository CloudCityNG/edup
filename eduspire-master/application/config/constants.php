<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);


/*
|--------------------------------------------------------------------------
| Application Constants 
|--------------------------------------------------------------------------
|
| These are defined by developers 
|
*/

$tm='';
$tmp = substr(BASEPATH, 0, strrpos(BASEPATH, '/'));
$tmp = substr($tmp, 0, strrpos($tmp, '/')); 
define('UPLOADS', $tmp.'/uploads');

define('STATUS_PUBLISH' , 1);
define('STATUS_UNPUBLISH' , 0);

define('TESTIMONIAL_INSTRUCTOR_APPROVED' , 3);
define('TESTIMONOAL_APPROVED' , 2);
define('TESTIMONOAL_REJECTED' , 1);
define('TESTIMONOAL_NOT_DEFINED' , 0);

define('STATUS_YES' , 1);
define('STATUS_NO' , 0);

define('ADDED_BY_USER' , 1);
define('ADDED_BY_AMDIN' , 0);

define('STATUS_REGISTERED' , 0);
define('STATUS_ENROLLED' , 1);
define('STATUS_UNREGISTERED' , 2);
define('STATUS_UNENROLLED' , 3);

define('COURSE_OFFLINE' , 0);
define('COURSE_ONLINE' , 1);

/*FAQ Intended audience */
define('VISITORS', 0);
define('MEMBERS' , 1);

define('PER_PAGE',30);

 
define('EDUSPIRE_ADDRESS','P.O. Box 933, Devon, PA 19333 -855-EDUSPIRE ');
define('EDUSPIRE_EMAIL','info@eduspire.org ');
define('ADMIN_EMAIL','admin@eduspire.org');
define('SENDER_EMAIL','do_not_reply@eduspire.org');
define('SITE_NAME' ,'Eduspire');
define('SITE_DESCRIPTION' ,'Eduspire is an innovative provider of graduate level courses for teacher professional development whose goal is to transform teaching & learning through practical integration');

define('CURRENCY' ,'$');
define('DATE_FORMAT' ,'M d, Y');
define('TIME_FORMAT' ,'g:i a');

/*
	USER ACCESS LEVEL CONSTANT
*/
//database driven user access levels
require_once( BASEPATH .'database/DB'. EXT );
$db =& DB();
$db->select('groupKey,groupID');
$query = $db->get( 'permission_groups');
$result = $query->result();
foreach( $result as $row )
{
 define(strtoupper($row->groupKey),$row->groupID);
}
/*
define('SUPER_ADMIN', 99);
define('ADMIN', 91);
define('MANAGER' , 90);
define('INSTRUCTOR' , 40);
define('INSTRUCTOR_ASSISTANT',30);

define('MEMBER' , 10);
define('USER' , 1);
*/

/*
	USER ACCOUNT ACTIVATION CONSTANT 
*/

define('ACCOUNT_ACTIVE',-1);
define('ACCOUNT_INACTIVE',0);
define('ACCOUNT_PENDING',1);


/**
	REPORT TYPE FILTERS
*/
define('RPT_CSV', 'csv');
define('RPT_PDF', 'pdf');
/**
 PAYPAL TRANSACTION STATUS CONSTANTS  
*/
define('PAYMENT_ENROLLED','Enrolled');
define('PAYMENT_REFUNDED','Refunded');
define('PAYMENT_COMPLETED','Completed');
define('PAYMENT_REVERSED','Reversed');


/**
	PAYPAL TRANSACTION PRODUCT TYPE  CONSTANTS 	
*/
define('PRODUCT_TYPE_IPAD',0);
define('PRODUCT_TYPE_COURSE',1);
define('PRODUCT_TYPE_EVENT',2);

/**
	 PAYPAL TRANSACTION PAYMENT MODE   CONSTANTS 	
*/
define('PAYMENT_MODE_MANUAL',0);
define('PAYMENT_MODE_PAYPAL',1);
define('PAYMENT_MODE_PERSONAL_CHECK',2);
define('PAYMENT_MODE_BRANDNAN_DIRECT_PAY',3);
define('PAYMENT_MODE_DISTRICT_CHECK',4);


/**
	PAYPAL TRANSACTION TXN TYPE  CONSTANTS 	
*/
define('TXN_TYPE_CART','cart');
define('TXN_TYPE_INVOICE','invoice_payment');
define('TXN_TYPE_WEB','web_accept');
define('TXN_TYPE_MONEY','send_money');

/**
 ORDER STATUS CONSTANTS  
*/
define('ORDER_PENDING','pending');
define('ORDER_PAYPAL_PENDING','paypal pending');
define('ORDER_COMPLETED','completed');


//forgot password code and account activation link expiration time in days 
define('FWPWD_EXPIRE_TIME',10);



/**ASSIGNMENT TYPE CONSTANT*/
define('ASGN_PRE_KEYNOTE',1);
define('ASGN_POST_KEYNOTE',2);
define('ASGN_TARGETED_DISCUSSION',3);
define('ASGN_LESSON_PLAN',5);
define('ASGN_QUESTIONNAIRE',6);
define('ASGN_IPAD_CONFIGURATION',7);
define('ASGN_TYPE_COURSE',10);


/**Build your own course id /One credit course   */
define('BYOC_ID',2);
define('STANDARD_IPAD','Standard (16GB + WiFi)');
define('STANDARD_IPAD_PRICE','0');
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');



/*Janet Rajani | for email*/
/*
define('SMTP_PASS','Rubi@123');
define('SMTP_USER','ithands.smm@gmail.com');
define('SMTP_HOST','ssl://smtp.googlemail.com');
define('SMTP_PORT',465);
define('SMTP_SAFE',1);
*/
/*End email*/

/*Ben Binesh | for email*/
define('SMTP_PASS','9z1tHLKAlGPYgBE8OwB2tw');
define('SMTP_USER','nathan@eduspire.org');
define('SMTP_HOST','smtp.mandrillapp.com');
define('SMTP_PORT',587);


/**
	PAYPAL INTEGRATION CONSTANT 
**/
//make paypaal mode blank on live 
//define('PAYPAL_MODE','sandbox');
define('PAYPAL_MODE','');
/*define('PAYPAL_USERNAME','martin.mahtab_api1.ithands.net');
define('PAYPAL_PASSWORD','1366949877');
define('PAYPAL_SIGNATURE','AFcWxV21C7fd0v3bYYYRCpSSRl31A6xIDOA4hHKnHBFZfZUVHIUs0y2F');*/
define('PAYPAL_USERNAME','nathan_api1.eduspire.org');
define('PAYPAL_PASSWORD','ETXS9SDAUKJN7L5S');
define('PAYPAL_SIGNATURE','AnAHkCS00as6kFNIcjd8NfWsXmrLAep.DfHz.II5uu4uxCxWxjUfcP0X');
define('PAYPAL_ENDPOINT_LIVE','https://api-3t.paypal.com/nvp');
define('PAYPAL_ENDPOINT_TEST','https://api-3t.sandbox.paypal.com/nvp');
define('PAYPAL_VERSION','51.0');

define('SURVEY_PARENT', 0);
/*Questionnaire constants*/
define('SECTION', 'section');
define('TEXT', 'text');
define('TEXT_AREA', 'textarea');
define('CHECKBOX_GROUP', 'checkboxGroup');
define('SELECT', 'select');
define('RADIO_SECTION', 'radioSection');
define('STAR_RATING', 'starRating');
/*End Questionnaire constants*/

define('FEATURED', 1);

define('COURSE_CURRENT', 1);
define('COURSE_ARCHIVED', 2);

define('IPAD_CAT', 10);
/* End of file constants.php */
/* Location: ./application/config/constants.php */