<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This module allows for the base management of sports teams for a club. The
 * structure is sufficiently generic to allow any sport. It also adds a similar
 * base support for league identification more as a thoughtful design than a
 * smart feature (makes keeping teams in the same leagues easier).
 * I highly recommend you fetch memberships as part of your addon installation.
 * It will allow you to have team rosters with anything from basic players and
 * coaches to whatever your heart desires (I'm having cake bakers).
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package teams
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