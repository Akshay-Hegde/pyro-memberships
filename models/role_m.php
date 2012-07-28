<?php

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

require_once('sports_base.php');

/**
 * The Coaches model maps users to teams with metadata in between.
 * Since one user could be the coach of several teams (or co-coach),
 * I can see that it's necessary to add another mapping layer.
 * This is simply sports_coach_team handled internally by Coach_m
 * and Team_m - no reason to add a whole extra model for that stuff.
 * @package memberships
 * @subpackage roles
 */
class Coach_m extends sports_base
{
	/**
	 * Bare constructor. As with all sports models, we prefix the table
	 * name using something form internal.
	 * @return type
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = $this->tables['coaches'];
	}

	/**
	 * Gets all coaches. The wrapper is because a coach is a pretty boring
	 * piece of data. We add a complete profile on each for good measure.
	 * @return ActiveRecord result object.
	 */
	public function get_all()
	{
		$t = $this->_table;
		$p = 'profiles';
		$tm = 'teams';
		return $this->db->select("$t.*, profiles.display_name AS name")
						->join($p, "$t.profile_id = $p.id")
						->order_by("$t.id", 'ASC')
						->get($t)
						->result();
	}

	/**
	 * Retrieves all user profiles that aren't tied to a coach role.
	 * @return ActiveRecord result object based wholely on the profiles
	 * table.
	 */
	public function get_all_non_coaches()
	{
		// Retrieve all used profile_ids.
		$used = $this->get_all();
		$used_list = array();
		foreach ($used as $use)
		{
			$used_list[] = $use->profile_id;
		}

		return $this->db->from('profiles')
						->where_not_in('profile_id', $used_list)
						->get()
						->result();
	}

	/**
	 * Wraps the regular get, retrieving the attached profile as well. Makes
	 * for more interesting results.
	 * @param int $id Coach id.
	 * @return ActiveRecord object comprised of a coach and their profile.
	 */
	public function get($id)
	{
		$t = $this->_table;
		$p = 'profiles';
		return $this->db->select("$t.*, profiles.display_name AS display_name")
						->join($p, "$t.profile_id = $p.id")
						->where("$t.id", $id)
						->get($t)
						->row();
	}

	/**
	 * Retrieves the number of coaches attached to a particular team.
	 * @param int $id Team id. 
	 * @return int Number of coaches on the team, excluding head coach.
	 */
	public function get_team_count($id)
	{
		$t = $this->tables['coach_teams'];
		return $this->db->where('coach_id', $id)
						->from($t)
						->count_all_results();
	}

	/**
	 * Retrieves a simple associative array of (coach)id->(profile)name mappings.
	 * Very useful for dropdowns where WE want a numeral id and the user wants to
	 * pick from human-readable values.
	 * @return Associative array. Keys are id, values are display names.
	 */
	public function get_for_dropdown()
	{
		$t = $this->tables['coaches'];
		$u = 'profiles';
		return $this->db->select("$t.id, profiles.display_name AS coach_name")
			 		->join($u, "$t.profile_id = $u.id")
			 		->order_by('coach_name', 'asc')
			 		->get($t)
			 		->result();
	}

	/**
	 * Retrieves COACHES that are connected to a team. The profile/user
	 * data must be handled separately, dudes.
	 * @param int $id Id of the team.
	 * @return object Result collection of coaches for the team.
	 */
	public function get_from_team($id)
	{
		$t = $this->tables['coaches'];
		$m = $this->tables['coach_teams'];

		return $this->db->where('team_id', $id)
					->get($m)
					->result();
	}

	/**
	 * Since the creation of coaches *might* also contain som team references
	 * we need to customise the creation to fit.
	 * @param type $data 
	 * @return type
	 */
	public function create($data)
	{
		// Split off the teams array and run a standard create.
		$teams = $data['teams'];
		unset($data['teams']);
		
		parent::insert($data);

		$id = $this->db->insert_id();

		// Insert new teams.
		$to_insert = array();
		foreach ($teams as $key=>$team)
		{
			$to_insert[] = array(
					'coach_id' => $id,
					'team_id' => $team,
				);
		}
		$this->db->insert_batch($this->tables['coach_teams'], $to_insert);

		return $id;
	}

	/**
	 * Since a coach update also updates the list of connected teams, we can't
	 * simply defer to the standard MY_Model code.
	 * @param type $id Id of the coach.
	 * @param type $data An array of data that *must* contain *only* keys matching
	 * those in sports_coaches *and optionally* a key, "teams", that contains an
	 * array of team ids that should replace those currently active for the coach.
	 * @return type
	 */
	public function update($id, $data)
	{
		// Defer to standard if teams list shouldn't be changed.
		if (!isset($data['teams'])) return parent::update($id, $data);

		// Remove all current teams.
		$this->db->delete($this->tables['coach_teams'], array('coach_id' => $id));

		// Insert new one.
		$to_insert = array();
		foreach ($data['teams'] as $key=>$team)
		{
			$to_insert[] = array(
					'coach_id' => $id,
					'team_id' => $team,
				);
		}
		$this->db->insert_batch($this->tables['coach_teams'], $to_insert);

		// Update standard values.
		unset($data['teams']);
		return parent::update($id, $data);
	}

	/**
	 * Deletes a coach. This will also delete all that coach's team relations,
	 * although the teams are left intact.
	 * @param int $id Coach id.
	 * @return ... affected rows? What does delete return?
	 */
	public function delete($id)
	{
		// Primary coach entry.
		$this->db->delete($this->_table, array('id' => $id));

		// All team links.
		$this->db->delete($this->tables['coach_teams'], array('coach_id' => $id));
	}
}
