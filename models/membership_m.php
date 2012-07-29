<?php

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

/**
 * The core membership model. Simple CRUD.
 * @package memberships
 */
class Membership_m extends MY_Model
{
    public function create($data)
    {
        return $this->insert($data);
    }

	/**
	 * Retrieves all profiles not currently tied to a specific group.
	 * @param int $group Group id.
	 * @return ActiveRecord result object.
	 */
	public function get_not_in_group($group)
	{
		return $this->db->where('group_id', $team)
						->get()
						->result();
	}

	/**
	 * Retrieves all all 
	 * @param type $role 
	 * @param type $active 
	 * @return type
	 */
	public function get_profiles_in_group($role, $active = false)
	{
		return $this->db->where('profile_id', $profile)
						->where('end_date'.($active ? '' : '!='), null) // Adds the "NOT" if active was false.
						->get($this->_table)
						->result();
	}

	/**
	 * Retrieves all players from a specific team. Joins with profile in the
	 * same way coach does.
	 * @param int $id The team's id.
	 * @return A result object/array with hits.
	 * @todo Deprecate this and force a two-step process for cleaner interfaces.
	 */
	public function get_team($id)
	{
		$t = $this->_table;
		$u = 'profiles';

		// Get id's in team.
		$members = $this->db->from($this->_table)
							->where('team_id', $id)
							->where('end_date', null)
							->get()
							->result();

		// Handle empty result.
		if (empty($members)) return array();

		$profile_ids = array();
		foreach ($members as $m)
		{
			$profile_ids[] = $m->profile_id;
		}

		return $this->db->from('profiles')
						->where_in('id', $profile_ids)
					    ->order_by('display_name', 'ASC')
				 	    ->get()
				 	    ->result();
	}

	/**
	 * Retrieves all current (active) memberships on a team. This means all
	 * memberships that have yet to be given an end_date (and thus termiantion).
	 * @param int $team Team id.
	 * @return ActiveRecord result object.
	 */
	public function get_active_for_team($team)
	{
		return $this->db->from($this->_table)
						->where(array('end_date' => null, 'team_id' => $team))
						->get()
						->result();
    }

    /**
     * Retires all playesr on a team. Apart from national teams, that may need
     * yearly mass retirements, this function is used in the mark-n-sweep method
     * applied when updating team memberships through the admin interface.
     * @param int $team Team id.
     * @return Whatever the hell a call to update returns.
     * @see $this->resume_on_team
     */
    public function retire_all_on_team($team)
    {
    	// Now retire them all. Mark phase.
        return $this->db->where('end_date', null)
               		    ->where('team_id', $team)
                 		->set('end_date', date('Y-m-d'))
                 		->update($this->_table);
    }

    /**
     * Retires a single member by their direct id.
     * @param int $member Member id.
     * @return Affected rows, I think.
     */
    public function retire_one($member)
    {
    	return $this->db->where('id', $member)
    					->set('end_date', date('Y-m-d'))
    					->update($this->_table);
    }

    /**
     * Resumes a single profile on a team. This is done by setting the end_date
     * back to null on the membership that has the latest end_date. This ensures
     * that previous memberships of that profile in that team remain untouched.
     * @param int $team Team id.
     * @param int $profile Profile id.
     * @return Whatever the hell a call to update returns.
     */
    public function resume_on_team($team, $profile)
    {
    	return $this->db->where('team_id', $team)
    					->where('profile_id', $profile)
    					->where('end_date !=', null)
    					->order_by('end_date', 'DESC')
    					->limit(1)
    					->update($this->_table);
    }

    /**
     * Resumes a particular membership, regardless of team or profile. This is
     * the direct, and most efficient, way to resume a membership that does not
     * need to be created anew.
     * @param int $member Membership id.
     * @return Whatever the hell a call to update returns.
     */
    public function resume_one($member)
    {
    	return $this->db->where('id', $member)
    					->set('end_date', null)
    					->update($this->_table);
    }

    /**
     * Checks to see if a particular profile has a membership on a team.
     * @param int $team Team id.
     * @param int $profile Profile id.
     * @return True if a membership exists, false otherwise.
     */
    public function team_has_profile($team, $profile)
    {
    	return $this->db->where('end_date', null)
    					->where('profile_id', $profile)
    					->where('team_id', $team)
    					->count_all_results($this->_table) > 0 ? true : false;
    }

    /**
     * Creates a new membership for a profile on a team.
     * @param int $team Team id.
     * @param int $profile Profile id.
     * @return int The new membership id or null if a membership already exists.
     */
    public function create_on_team($team, $profile)
    {
    	if ($this->team_has_profile($team, $profile)) return null;

    	return $this->db->insert($this->_table, array(
    		'profile_id' => $profile,
    		'team_id' => $team,
    		'start_date' => date('Y-m-d'),
    		'end_date' => null,
		));
    }
}
