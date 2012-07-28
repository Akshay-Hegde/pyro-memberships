<?

/**
 * This module was built specifically with Danish volleyball leagues in mind,
 * but the abstraction should be distant enough to allow for practically any
 * sport that has a generic tournament structure, different leagues (or series,
 * or .... you know), players, coaches and a team description.
 *
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package sports
 **/

if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['teams(/:num)'] = 'teams/view$1';