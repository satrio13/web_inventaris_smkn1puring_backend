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
|	https://codeigniter.com/userguide3/general/routing.html
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
//$route['default_controller'] = 'welcome';
/*
| -------------------------------------------------------------------------
| REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/register']['POST'] = 'api/auth/register'; 
$route['api/login']['POST'] = 'api/auth/login'; 
$route['api/checklist']['GET'] = 'api/checklist'; 
$route['api/checklist']['POST'] = 'api/checklist/create'; 
$route['api/checklist/(:num)']['DELETE'] = 'api/checklist/delete/$1'; 
$route['api/checklist/(:num)/item']['GET'] = 'api/checklist_item/get_item_by_checklist_id/$1'; 
$route['api/checklist/(:num)/item/(:num)']['GET'] = 'api/checklist_item/get_item_by_checklist_id_and_item_id/$1/$2'; 
$route['api/checklist/(:num)/item']['POST'] = 'api/checklist_item/create/$1'; 
$route['api/checklist/(:num)/item/(:num)']['PUT'] = 'api/checklist_item/update_status_item_by_checklist_id_and_item_id/$1/$2'; 
$route['api/checklist/(:num)/item/(:num)']['DELETE'] = 'api/checklist_item/delete_item_by_checklist_id_and_item_id/$1/$2'; 
$route['api/checklist/(:num)/item/rename/(:num)']['PUT'] = 'api/checklist_item/rename_status_item_by_checklist_id_and_item_id/$1/$2'; 
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;