<?php

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

require_once('admin_base.php');

/**
 * Admin controller for memberships. This is a *very* resource-intensive
 * controller, owing to its role as a core reporting device.
 * @todo Consider what we can do to improve performance, or at least warn users
 * of overuse.
 * @package sports
 * @subpackage memberships
 */
class Admin_Memberships extends Admin_SportsBase
{
    /**
     * Used by the PyroCMS admin code to match shortcuts in details.php to
     * controllers' admin views.
     */
    protected $section = 'leagues';

    /**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     * @return ?
     */
	public function __construct()
	{
		parent::__construct();

        // Load the required classes
        $this->load->model(array(
            'league_m',
            'fee_m',
            'season_m',
            'team_m',
            'membership_m',
        ));

		$this->load->library(array(
            'calendar',
        ));

		// We'll set the partials and metadata here since they're used everywhere
		$this->template->append_js('module::admin.js')
						->append_css('module::admin.css');
	}

	/**
	 * The membership report is neither created, edited or deleted.
     * It is shown and slightly modified in its sub pieces.
     * @param int $season The season to display a report for. If omitted, the
     * season with the latest ending is shown.
     * @todo Fix to show the most recent ended season if null.
	 */
	public function index($season = null)
	{
        /**
         * Since this is a heavy report-style controller, we need to prepare data.
         * 0. Retrieve the season we are working with.
         * 1. Generate array of teams.
         * 2. Add array of members for each team and add it to the team object.
         * 3. Each member is a profile object
         *      + a percentage of membership (typically 1.0, for new members lower)
         *      + a calculated fee.
         * 4. A fee is created if none are found for a particular season id and membership combination.
         *      - If a fee is found, that fee is attached to the membership.
         *      - A new fee is calculated thus: (for each team the profile is attached to, add their fees)/(percentage active)
         *      - The fee is suggested and can be modified. This action is not tracked currently.
         **/

        if (!isset($season))
            $this->season_m->get_latest()->id;

        $teams = $this->team_m->get_all();


        foreach($teams as $team)
        {
            $team->members = $this->membership_m->get_team($team->id);

            foreach($team->members as $member)
            {
                // Percentage.
                // $member->percentage = $this->membership->get_percentage($season, $member->id);
                $member->percentage = 1.0; // For starters. Later, we should make the proper percentage calculation, or branch depending on cashier recommendations.

                $member->fee = $this->get_or_create_fee($member->id, $season); // again slightly hardcoded. This will retrieve a total single-season fee for a member.
            }
        }

        $this->data->memberships = $teams;

        $this->do_template('sports:memberships', 'memberships');
	}

    /**
     * Retrieves the fee object for a particular profile. If none is found,
     * generate a new one.
     * @param int $profile Profile ID to look with.
     * @param int $season_id The season to couple with the profile. If omitted,
     * season is found in this order: current, latest, last.
     * @return ActiveRecord object the fee object.
     */
    protected function get_or_create_fee($profile, $season_id = null)
    {
        // Get the passed season.
        if (isset($season_id))
            $season = $this->season_m->get($season_id);
        // Get the current season if omitted.
        if (empty($season))
            $season = $this->season_m->get_current();
        // Fall back to latest season if no current.
        if (empty($season))
            $season = $this->season_m->get_latest();
        if (empty($season))
            $season = $this->season_m->get_last();
        // Fail loudly if no season whatsoever could be found.
        if (empty($season))
            throw Exception('Sorry, but there are no seasons to work with.');

        $hit = $this->fee_m->get_for_season_and_profile($profile, $season->id);

        // If already exists, pass pack. Otherwise, create.
        if (!empty($hit)) return $hit;

        // Get all relevant teams.
        $memberships = $this->membership_m->get_for_profile($profile, true);

        $teams = array();
        foreach($memberships as $membership)
            $teams[] = $this->team_m->get($membership->team_id);

        $price = 0;

        // Put all fees together.
        foreach($teams as $team)
            $price += $team->membership_fee;

        $fee_id = $this->fee_m->create(array(
            'season_id' => $season->id,
            'profile_id' => $profile,
            'paid' => 0.0, // Usually, nothing was paid yet.
            'base_fee' => $price,
        ));

        return $this->fee_m->get($fee_id);
    }
}
