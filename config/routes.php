<?

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

if (!defined('BASEPATH')) exit('No direct script access allowed');

// $route['teams(/:num)'] = 'teams/view$1';
$route['memberships/admin/roles']				= 'admin_roles';
$route['memberships/admin/roles/(:any)']	= 'admin_roles/$1';
$route['memberships/admin/roles/(:any)/(:num)']	= 'admin_roles/$1/$2';
// $route['memberships/admin/roles/(:any)/(:num)']	= 'admin_roles/edit/$1';