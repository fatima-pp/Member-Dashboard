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


$route['default_controller'] = 'Activation/landing'; 
$route['get_base_url']       = 'Activation/landing/base_url';
$route['membership_activation'] = 'Activation/landing/old_landing';

//Admin Access routes
$route['portal']         = 'Admin/Access';
$route['adminlogin']     = 'Admin/Access/login';
$route['adminlogout']    = 'Admin/Access/logout';
$route['dashboard']      = 'Admin/Access/dashboard';
$route['viewusage/:any'] = 'Admin/Access/viewusage';
$route['salesReport']    = 'Admin/Reports';
$route['invoice']        = 'Admin/Reports/invoice';

//client new pages
$route['customer_info']  = 'Client/Client/customer_info';
$route['sales_report']   = 'Client/Client/sales_report';

//qr new lost
$route['qr_new_lost']                   = 'Admin/Access/assign_qr_new_lost';

//Customers Activation routes
$route['home']                 = 'Activation/landing';
$route['about']                = 'Activation/landing/about';
$route['contact']              = 'Activation/landing/contactUs';
$route['forgot_password']      = 'Activation/operations/changePassword';

$route['profile']              = 'Activation/landing/viewDetails';
$route['existingProfile']      = 'Activation/landing/viewExistingDetails';

$route['add_info']             = 'Activation/landing/personalDetails';
$route['add_address']          = 'Activation/landing/address';
$route['add_car']              = 'Activation/landing/carDetails';
$route['activate']             = 'Activation/landing/parkPassDetails';
$route['logout']               = 'Activation/landing/logout';
$route['login']                = 'Activation/landing/login';
$route['client']               = 'Activation/landing/clientpage';
$route['apply_for_membership'] = 'Activation/activation/applyForMembership';
$route['submit_membership']    = 'Activation/activation/submitMembership';
$route['valetSites']           = 'Activation/activation/valetServiceSites';
$route['editEmail']            = 'Activation/landing/editEmailAndMobile';

//individuals
$route['individuals']          = 'Activation/landing/individuals';
$route['packageDetails']       = 'Activation/landing/viewPackageDetails';
$route['buyPackage']           = 'Activation/landing/buyPackage';
$route['viewIndividualDetails/:any'] = 'Activation/landing/viewIndividualDetails';

$route['changeEmail']          = 'Activation/landing/submitEmailAndMobileChange';

//BDF Routes
//$route['new']                       = 'BDF/BDFApply/newApplication';
$route['new/(:any)']                = 'BDF/BDFApply/newApplication/$1';
$route['receipt']                   = 'BDF/BDFApply/receipt';
$route['renew/(:any)']              = 'BDF/BDFApply/renewApplication/$1';
$route['renewalForm']               = 'BDF/BDFApply/renewalForm';
$route['paymentSuccess']            = 'BDF/BDFApply/paymentSuccess';
$route['paymentError']              = 'BDF/BDFApply/paymentError';
$route['responsePage']              = 'BDF/BDFApply/responsePage';

$route['myResponse']                = 'BDF/BDFApply/myResponse';
$route['myRequest']                 = 'BDF/BDFApply/myRequest';

$route['paymentFail']               = 'BDF/BDFApply/paymentFail';
$route['paymentCredit']             = 'BDF/BDFApply/paymentProcess';
$route['reviewMembershipDetails']   = 'BDF/BDFApply/reviewBDFOrder';
$route['reviewBDFRenewApplication'] = 'BDF/BDFApply/reviewBDFRenewApplication'; 
$route['cancelPayment']             = 'BDF/BDFApply/cancelPayment';
$route['paymentConfirmCancel']      = 'BDF/BDFApply/paymentConfirmCancel';
$route['test1598']     		        = 'BDF/BDFApply/testSession';

$route['requestFinal']     		    = 'BDF/BDFApply/requestFinal';
$route['finalResponse']     		= 'BDF/BDFApply/myResponse';
$route['myPaymentError']            = 'BDF/BDFApply/myPaymentError';
$route['errResponse']               = 'BDF/BDFApply/errResponse';

$route['declined']                  = 'BDF/BDFApply/declined';
$route['payment_approved']          = 'BDF/BDFApply/payment_approved';
$route['payment_approved_err']      = 'BDF/BDFApply/payment_approved_err';

$route['tick_pay_approved/(:any)']  = 'Payment/Payment/payment_approved/$1';
// $route['tick_pay_approved']          = 'Payment/Payment/tick_payment_approved';
$route['tick_pay_approved_err']      = 'Payment/Payment/tick_payment_approved_err';


//Corporate Accounts
$route['corporateAccounts']         = 'CorporateAccounts/CorporateAccounts/index'; 
$route['addCorporateAccount']       = 'CorporateAccounts/CorporateAccounts/add_corporate_account'; 
$route['setCorporateAccount']       = 'CorporateAccounts/CorporateAccounts/addCorporateAccount'; 
$route['members/:any']              = 'CorporateAccounts/CorporateAccounts/members/$1'; 
$route['viewAccountMembers/:any']   = 'CorporateAccounts/CorporateAccounts/view_CP_members/$1'; 

//testing
$route['response_redirect']        = 'BDF/BDFApply/response_redirect';
$route['hello_check']              = 'BDF/BDFApply/hello_check';
$route['show_app_path']            = 'BDF/BDFApply/show_app_path';
$route['make_test_request']        = 'BDF/BDFApply/make_test_request';
$route['mlr']                      = 'BDF/BDFApplyYasir/mlr_ys';
$route['mpe']                      = 'BDF/BDFApplyYasir/mpe_ys';
$route['mps']                      = 'BDF/BDFApplyYasir/mps_ys';
$route['ap']                       = 'BDF/BDFApplyYasir/ap_ys';

//Redirect to play store or app store Routes

$route['store'] = 'Activation/deviceDetect';
$route['gotostore'] = 'Activation/deviceStore';

$route['404_override']         = '';
$route['translate_uri_dashes'] = FALSE;

//brochure creation
$route['brochure']                      = 'Brochure/Brochure/brochure';
$route['createBrochure']                = 'Brochure/Brochure/create_new_brochure';
$route['receiveBrochure']               = 'Brochure/Brochure/enter_received_brochure';
$route['assign_qr']                     = 'Brochure/Brochure/assign_qr';
$route['transfer_package']              = 'Brochure/Brochure/transfer_package';

//php version
$route['my_php_ver']                    = 'Brochure/Brochure/php_ver';


//test route
$route['tezt_route']                    = 'BDF/BDFApply/tezt_route';


$route['buyPackage']                    = 'Activation/landing/buy_mem';
$route['mem_form']                      = 'Activation/landing/mem_form';
$route['page_under_const']              = 'Activation/landing/page_under_const';
$route['valet_locations']               = 'Activation/landing/valet_locations';

$route['payment_page_1589']             = 'Payment/Payment/payment_page';
$route['parkingquickpay/(:any)']        = 'Payment/Payment/payment_page/$1';
$route['parkingquickpay']               = 'Payment/Payment/payment_page';
$route['ticket_amount']                 = 'Payment/Payment/get_amount';
$route['get_ticket']                    = 'Payment/Payment/get_ticket';
$route['pay_ticket']                    = 'Payment/Payment/pay_ticket';
$route['show_receipt/(:any)/(:any)']    = 'Payment/Payment/show_receipt/$1/$2';
$route['ticket_success_paid/(:any)']    = 'Payment/Payment/success_page/$1';
$route['ticketPaymentSuccess/(:any)']   = 'Payment/Payment/ticketPaymentSuccess/$1';//credit card payment success
$route['ticket_pay_fail']               = 'Payment/Payment/fail_page';
$route['ticket_not_found']              = 'Payment/Payment/ticket_not_found';
$route['cancelTicketPayment/(:any)']    = 'Payment/Payment/cancelTicketPayment/$1';


// BDF
$route['sign_in']                     = 'BDF/sign_in';  
$route['sign_in/(:any)']              = 'BDF/sign_in_email/$1';  
$route['dont_have_pass']              = 'BDF/dont_have_pass';  
$route['dont_have_pass_2']            = 'BDF/dont_have_pass_2';  
$route['forgot_pass_1']               = 'BDF/forgot_pass_1';  
$route['forgot_pass_2']               = 'BDF/forgot_pass_2';  
$route['forgot_pass_3']               = 'BDF/forgot_pass_3';  

$route['sign_in_sub']                 = 'BDF/sign_in_sub';  
$route['forgot_pass']                 = 'BDF/forgot_password';  //changed name
$route['gen_new_pass/(:any)']         = 'BDF/create_new_pass/$1';  
$route['set_new_pass']                = 'BDF/set_new_pass';  
$route['verify_send_otp']             = 'BDF/verify_send_otp';  
$route['get_token_set_new_pass']      = 'BDF/get_token_set_new_pass';  //check url from here onwards
$route['create_pass']                 = 'BDF/create_pass';  
$route['create_account']              = 'BDF/create_account';  
$route['verify_email/(:any)']         = 'BDF/verify_email/$1';  

$route['new_bdf_renew/(:any)']        = 'BDF/renew/$1';   //change name
$route['get_vacant_parks']            = 'BDF/getVacantParks';  
$route['review_membership_dtls']      = 'BDF/reviewBDFOrder'; //change name
$route['dashboard_logout']            = 'BDF/sign_out'; //change name
$route['submit_renew_bdf']            = 'BDF/renew_bdf'; //change name



// Zain -- cannot go to any links unless mobile &/ otp is set
$route['(:any)/zain_sign_in']         = 'Zain/sign_in/$1';
$route['zain_sign_in_otp']            = 'Zain/sign_in_otp';
$route['zain_verification']           = 'Zain/zain_verification';
$route['zain_verify_otp']             = 'Zain/zain_verify_otp';
$route['resend_otp']                  = 'Zain/zain_resend_otp';
$route['zain_activate']               = 'Zain/zain_activate_acc';
$route['zain_activate_1']             = 'Zain/zain_activate_acc_1';
$route['zain_activate_2']             = 'Zain/zain_activate_acc_2';
$route['get_session_info']            = 'Zain/get_session_info';
$route['php_info']                    = 'Zain/php_info';
$route['view_mail']                   = 'Zain/view_mail';
$route['send_mail']                   = 'Zain/send_mail';