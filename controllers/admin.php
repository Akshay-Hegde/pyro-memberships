<?php

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

require_once('admin_base.php');

/**
 * Admin controller for memberships. It supports simple paginated membership
 * views, categorised memberships and finally some nice reports.
 * @package memberships
 */
class Admin extends Admin_BaseController
{
    /**
     * Used by the PyroCMS admin code to match shortcuts in details.php to
     * controllers' admin views.
     */
    protected $section = 'memberships';

    /**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     * @return ?
     */
	public function __construct()
	{
		parent::__construct();

        // Load the required classes
        $this->load->model(array(
            'settings_m',
            'role_m',
            'membership_m',
        ));

		$this->load->library(array(
            'calendar',
        ));
	}

	/**
     * For performance reasons, the base view is a simple paginated membership
     * view, with shortcuts to the heavier report-style and specialised
     * categorised admin views.
     * @param int $offset Pagination offset.
     * @todo Add the appending of group data.
     */
	public function index($offset = null)
	{
        $this->data->memberships = $this->membership_m->get_all();

        // Append role, profile, group to each one.
        foreach($this->data->memberships as $m)
        {
            $m->role = $this->role_m->get($m->role_id);
            $m->profile = $this->profile_m->get($m->profile_id);

        }

        $this->do_template('memberships:memberships', 'memberships');
	}
}
