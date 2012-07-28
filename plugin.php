<?php 

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin part. It's very limited in what I could reasonably accomplish, and 
 * I'm thinking if maybe separate modules for each general model/controller
 * is better.
 * @package memberships
 */
class Plugin_Teams extends Plugin
{
	public function __construct()
	{
		$this->load->model(array(
			'settings_m',
			'team_m',
			'league_m',
		));

		$this->load->helper('url');
	}

	/**
	 * Full team list (careful on those db resources, dude).
	 *
	 * Although the usage of tag pairs insinuates looping behaviour, your will
	 * actually only get one hit. This seems like it's such a common occurrence
	 * that we should have something proper for it.
	 * Use the ids to pull from leagues, training times and the internal
	 * PyroCMS users model.
	 *
	 * Usage:
	 * {{ sports:teams order_by="name" order="asc" }}
	 *		{{ id }} {{ name }} {{ slug }} {{ league_id }} {{ description }}
	 * {{ /sports:teams }}
	 **/
	 public function teams()
	 {
	 	return $this->db->order_by($this->attribute('order_by', 'name'), 'ASC')
	 					->get('teams')
	 			        ->result();
	 }
	
	/**
	 * Retrieves a single team's data.
	 *
	 * Usage (partial):
	 * {{ sports:team_single id="3" }}
	 *		Team: {{ name }}
	 *		League: {{ sports:league_name id={league} }}
	 * {{ /sports:team_single }}
	 **/
	public function team()
	{
		$id = $this->attribute('id');
		
		return $this->db
						->get_where('teams', array('id' => $id))
						->result_array();
	}

	/**
	 * Returns the team corresponding to the current URL... I hope.
	 * @return ActiveRecord array result.
	 */
	public function current()
	{
		// It'll blow up in our faces, but for now let's use "last segment is
		// id" assumptions.
		return $this->db
					->get('teams', substr(uri_string(), -1))
					->result_array();
	}

	public function league_name()
	{
		$id = $this->attribute('id', '');
		
		if (empty($id)) return "UNKNOWN";

		$l = $this->league_m->get($id);

		if (!isset($l)) return "UNKNOWN";

		return $l->name;
	}

	/**
	 * Function for the league tag.
	 * Usage:
	 * {{ sports:league id="<id>" [get="<field>"] }}
	 * Given the monolithic structure of the module, I can't simply do
	 * sports:league:field id="" - yet.
	 * @return The value of the requested field in the referenced league.
	 */
	public function league()
	{
		$data = $this->db->from('sports_leagues')
						 ->where('id', $this->attribute('id'))
						 ->get()
						 ->row_array();

		return $data[$this->attribute('get')];
	}
}

/* End of file plugin.php */
