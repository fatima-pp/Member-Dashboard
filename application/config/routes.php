<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'BDF';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['airports/(:any)/(:any)']      = 'Airports/customer_form/$1/$2';  
$route['test_airport']                = 'welcome';  
$route['airport']                     = 'BDF';  


// BDF
$route['sign_in']                     = 'BDF/sign_in';  
$route['sign_in/(:any)']              = 'BDF/sign_in_email/$1';  
$route['dont_have_pass']              = 'BDF/dont_have_pass';  
$route['dont_have_pass_2']            = 'BDF/dont_have_pass_2';  
$route['forgot_pass_1']               = 'BDF/forgot_pass_1';  
$route['forgot_pass_2']               = 'BDF/forgot_pass_2';  
$route['forgot_pass_3']               = 'BDF/forgot_pass_3';  

$route['sign_in_sub']                 = 'BDF/sign_in_sub';  
$route['forgot_password']             = 'BDF/forgot_password';  
$route['gen_new_pass/(:any)']         = 'BDF/create_new_pass/$1';  
$route['set_new_pass']                = 'BDF/set_new_pass';  
$route['verify_send_otp']             = 'BDF/verify_send_otp';  
$route['get_token_set_new_pass']      = 'BDF/get_token_set_new_pass';  //check url from here onwards
$route['create_pass']                 = 'BDF/create_pass';  
$route['create_account']              = 'BDF/create_account';  
$route['verify_email/(:any)']         = 'BDF/verify_email/$1';  

$route['renew/(:any)']                = 'BDF/renew/$1';  
$route['get_vacant_parks']            = 'BDF/getVacantParks';  
$route['reviewMembershipDetails']     = 'BDF/reviewBDFOrder';


// Zain
$route['(:any)/zain_sign_in']         = 'Zain/sign_in/$1';
$route['zain_sign_in_otp']            = 'Zain/sign_in_otp';
$route['zain_verification']           = 'Zain/zain_verification';
$route['zain_verify_otp']             = 'Zain/zain_verify_otp';
$route['resend_otp']                  = 'Zain/zain_resend_otp';
$route['zain_activate']               = 'Zain/zain_activate_acc';