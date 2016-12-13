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
$route['default_controller'] = 'Auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*admin
$route['admin'] = 'user/index';
$route['admin/signup'] = 'user/signup';
$route['admin/create_member'] = 'user/create_member';
$route['admin/login'] = 'user/index';
$route['admin/logout'] = 'user/logout';
$route['admin/login/validate_credentials'] = 'user/validate_credentials';
*/

/*admin main*/
$route['admin/main'] = 'admin_main/index';


$route['admin/Mailer'] = 'admin_mailer/index';
$route['admin/Mailer/show'] = 'admin_mailer/index';
$route['admin/Mailer/mailfixedjob'] = 'admin_mailer/run_single_fixed_job';
$route['admin/Mailer/sendmail'] = 'admin_mailer/sendTestEmail';
$route['admin/Mailer/sendmailattach'] = 'admin_mailer/sendEmail_list_with_attachment';
$route['admin/Mailer/autosender'] = 'admin_mailer/auto_resume_mailer';
$route['admin/Mailer/load'] = 'admin_mailer/loaddata';
$route['admin/Mailer/cronm/(:num)/(:any)/(:num)/(:any)/(:any)'] = 'admin_mailer/cron_mail/$1/$2/$3/$4/$5';
$route['admin/Mailer/crontest/(:num)/(:any)/(:num)/(:any)/(:any)'] = 'admin_mailer_new/cron_mail/$1/$2/$3/$4/$5';

$route['admin/QC/summary/(:any)/(:any)'] = 'admin_qc/csv_extraction_report/$1/$2';
$route['admin/main/(:any)'] = 'admin_main/index/$1';

$route['admin/Statistics'] = 'admin_statistics/index';
$route['admin/Statistics/getnames'] = 'admin_statistics/collect_email_names';
$route['admin/Statistics/sendmail'] = 'admin_statistics/';

$route['admin/Emails'] = 'admin_emails/index';
$route['admin/Emails'] = 'admin_emails/index/$1';
$route['admin/Emails/list'] = 'admin_emails/index';
$route['admin/Emails/verfied'] = 'admin_emails/index';
$route['admin/Emails/unverfied'] = 'admin_emails/get_unverfied_emails';
$route['admin/Emails/add'] = 'admin_emails/add';
$route['admin/Emails/export/verfied'] = 'admin_emails/export_emails_from_DB';
$route['admin/Emails/export/unverfied'] = 'admin_emails/export_emails_from_DB';
$route['admin/Emails/clear'] = 'admin_emails/clear';
$route['admin/Emails/cron_backup/(:any)'] = 'admin_emails/cron_db_backup/$1';
$route['admin/Emails/export/verfied/(:any)'] = 'admin_emails/export_emails_from_DB';
$route['admin/Emails/export/unverfied/(:any)'] = 'admin_emails/export_emails_from_DB';
$route['admin/Emails/extract/(:any)'] = 'admin_emails/extract_data_from_file';
$route['admin/Emails/delete/(:any)'] = 'admin_emails/delete/$1';

$route['admin/Companies'] = 'admin_companies/index';
$route['admin/Companies/list'] = 'admin_companies/index';
$route['admin/Companies/add'] = 'admin_companies/add';
$route['admin/Companies/clear'] = 'admin_companies/clear';
$route['admin/Companies/update'] = 'admin_companies/update';
$route['admin/Companies/export'] = 'admin_companies/export_url_from_DB';
$route['admin/Companies/update/(:any)'] = 'admin_companies/update/$1';
$route['admin/Companies/delete/(:any)'] = 'admin_companies/delete/$1';
$route['admin/Companies/(:any)'] = 'admin_companies/index/$1';

$route['admin/Owner'] = 'admin_eowner/index';
$route['admin/Owner/list'] = 'admin_eowner/index';
$route['admin/Owner/add'] = 'admin_eowner/add';
$route['admin/Owner/update'] = 'admin_eowner/update';
$route['admin/Owner/update/(:any)'] = 'admin_eowner/update/$1';
$route['admin/Owner/delete/(:any)'] = 'admin_eowner/delete/$1';
$route['admin/Owner/(:any)'] = 'admin_eowner/index/$1';

$route['admin/export/open'] = 'admin_export/open';
$route['admin/export/delete'] = 'admin_export/delete';
$route['admin/export/export'] = 'admin_export/export';
$route['admin/export/show'] = 'admin_export/index';
$route['admin/export'] = 'admin_export/index';
$route['admin/export/do_exports'] = 'admin_export/do_exports';
$route['admin/export/download/(:any)'] = 'admin_export/download_file';

$route['admin/upload/open'] = 'admin_upload/open';
$route['admin/upload/delete'] = 'admin_upload/delete';
$route['admin/upload'] = 'admin_upload/index';
$route['admin/upload/do_uploads'] = 'admin_upload/do_uploads';
$route['admin/upload/show'] = 'admin_upload/show_files';

$route['admin/create_member'] = 'user/create_member';
$route['admin/login'] = 'user/index';
$route['admin/logout'] = 'user/logout';
$route['admin/login/validate_credentials'] = 'user/validate_credentials';

/*admin products*/
$route['admin/products'] = 'admin_products/index';
$route['admin/products/add'] = 'admin_products/add';
$route['admin/products/update'] = 'admin_products/update';
$route['admin/products/update/(:any)'] = 'admin_products/update/$1';
$route['admin/products/delete/(:any)'] = 'admin_products/delete/$1';
$route['admin/products/(:any)'] = 'admin_products/index/$1'; //$1 = page number

/*admin manufacturers*/
$route['admin/manufacturers'] = 'admin_manufacturers/index';
$route['admin/manufacturers/add'] = 'admin_manufacturers/add';
$route['admin/manufacturers/update'] = 'admin_manufacturers/update';
$route['admin/manufacturers/update/(:any)'] = 'admin_manufacturers/update/$1';
$route['admin/manufacturers/delete/(:any)'] = 'admin_manufacturers/delete/$1';
$route['admin/manufacturers/(:any)'] = 'admin_manufacturers/index/$1'; //$1 = page number