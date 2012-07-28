<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

/**
 * Checks to see if a particular module is installed and enabled.
 * @param string $slug Slug name of the module.
 * @return boolean True if the module is installed and enabled, false otherwise.
 */
function has_module($slug)
{
	$m = ci()->module_m->get($slug);
	return isset($m->slug);
}


/* End of file teams/helpers/team_helper.php */