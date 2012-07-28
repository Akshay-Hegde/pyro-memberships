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
 * Plugin parts.
 * @package memberships
 */
class Plugin_Teams extends Plugin
{
	public function __construct()
	{
		$this->load->model(array(
			'settings_m',
			'role_m',
			'membership_m',
		));

		$this->load->helper('extra');
	}

	/**
	 * Retrieves role types. Usage:
	 * {{ memberships:roles [model=""] [slug=""] }}
	 * @todo Make it use the model instead of a raw db call.
	 * @return type
	 */
	public function roles()
	{
		return $this->db->from('roles')
						->where($this->attributes())
						->order_by('slug', 'ASC')
						->get()
						->result_array();
	}
}

/* End of file plugin.php */
