<?php 

/**
 * Memberships introduction
 * @todo Fill out this description.
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package memberships
 **/

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Primary module code. Version, table installations, sample data, and such.
 * @package memberships
 */
class Module_Memberships extends Module {

    /**
     * Version of the module. Follows semantic versioning when beyond major 1.
     */
    public $version = '0.2.0';

    /**
     * Const reminder of the module's name. Can thus be referenced later.
     */
    const MODULE_NAME='memberships';

    /**
     * We load up the settings model to have insert_many for easy, efficient
     * insertion of settings. 
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('settings_m');
    }

    /**
     * Produces the information that is visible throughout PyroCMS about the
     * module. 
     * @return Array of information. The code is almost self-explanatory.
     */
    public function info()
    {
        $shortcuts = array(
                        array(
                            'name'  => 'memberships:create',
                            'uri'   => 'admin/memberships/create',
                            'class' => 'add',
                        ),
                        array(
                            'name'  => 'roles:create',
                            'uri'   => 'admin/memberships/roles/create',
                            'class' => 'add',
                        ),
                    );

		return array(
			'name' => array(
				'en' => 'Memberships',
				'da' => 'Medlemskaber',
			),
			'description' => array(
				'en' => 'A generic group and membership module. Recommendation for: Teams',
				'da' => 'Et modul til gruppe- og medlemsskabsadministration. Anbefalet til  modulet Hold',
			),
			'frontend' => true,
			'backend' => true,
			'menu' => 'content',
            'roles' => array('create_role', 'create_membership', 'edit_role', 'edit_membership', 'delete_role', 'delete_membership'),
			'sections' => array(
                'memberships' => array(
                    'name'  => 'memberships:memberships', // These are translated from your language file
                    'uri'   => 'admin/memberships',
                    'shortcuts' => $shortcuts,
                ),
                'roles' => array(
                    'name'  => 'roles:roles', // These are translated from your language file
                    'uri'   => 'admin/memberships/roles',
                    'shortcuts' => $shortcuts,
                ),
            ),
		);
    }

    /**
     * Code re-use. Drops the named table and creates a new one
     * with the passed fields and expecting an 'id' field for
     * key.
     * @param string $name Name of the table. Needn't exist.
     * @param array $fields dbforge-compatible array of field arrays.
     * @return boolean True if succesful, false otherwise.
     */
    protected function install_table($name, $fields)
    {
        $this->dbforge->drop_table($name);

        $this->dbforge->add_field($fields);

        $this->dbforge->add_key('id', true);

        if ( ! $this->dbforge->create_table($name)) return false;
        else return true;
    }

    /**
     * Installs the memberships table.
     * Primary key  : id(int)
     * start_date   : starting date of the membership.
     * end_date     : last date of the membership.
     * group_id     : foreign key to the group the profile is tied to.
     * profile_id   : foreign key to the profile tied to a group.
     * role_id      : foreign key to the role that the membership represents.
     * @return boolean True on success. False otherwise.
     * */
    public function install_memberships()
    {
        $table = array(
            'id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
                'auto_increment'=> true,
            ),
            'start_date' => array(
                'type' => 'DATE',
            ),
            'end_date' => array(
                'type' => 'DATE',
                'null' => true,
            ),
            'profile_id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
            ),
            'group_id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
                'null'          => 'true',
            ),
            'role_id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
                'null'          => 'true'
            ),
        );

        return $this->install_table('memberships', $table);
    }

    /**
     * Installs the roles table.
     * Primary key  : id(int)
     * name         : name of the role.
     * model        : name of the model that the role ties memberships to.
     * @return boolean True on success. False otherwise.
     * */
    public function install_roles()
    {
        $table = array(
            'id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
                'auto_increment'=> true,
            ),
            'name' => array(
                'type' => 'TEXT',
            ),
            'slug' => array(
                'type' => 'TEXT',
            ),
            'model' => array(
                'type' => 'TEXT',
            ),
            'key_field' => array(
                'type' => 'TEXT',
            ),
            'value_field' => array(
                'type' => 'TEXT',
            ),
        );

        return $this->install_table('roles', $table);
    }

    /**
     * Quick insert of a new location.
     * @param string $name Name of the location, recognisable.
     * @param string $model Slug of the model the role belongs to.
     * @return int New role id.
     */
    private function insert_role($name, $slug, $model, $key_field = 'id', $value_field = 'name')
    {
        $this->db->insert('roles', array(
            'name' => $name,
            'slug' => $slug,
            'model' => $model,
            'key_field' => $key_field,
            'value_field' => $value_field,
        ));
        return $this->db->insert_id();
    }

    /**
     * Quick insert of a new location.
     * @param int $profile Foreign key to the profiles table.
     * @param int $group Foreign key to the relevant model's table.
     * @param int $role Foreign key the role of this membership.
     * @param date $start Starting date. If omitted, today is used.
     * @param date $end Ending date.
     * @return int New membership id.
     */
    private function insert_membership($profile, $group, $role, $start = null, $end = null)
    {
        if (!isset($start)) $start = date('Y-m-d');

        $this->db->insert('memberships', array(
            'profile_id' => $profile,
            'group_id'   => $group,
            'role_id'    => $role,
            'start_date' => $start,
            'end_date'   => $end,
        ));
        return $this->db->insert_id();
    }

    /**
     * Installs a bunch of sample data to work with.
     * @return boolean True on success, false otherwise.
     */
    private function insert_sample_data()
    {
        $devs = $this->insert_role('Developer', 'developer', 'module');

        $this->insert_membership(1, 3, $devs);

        return true;
    }

    /**
     * Complete module installation function. Installs all tables, some sample
     * data to play around with and module settings.
     * @return boolean True on success, false otherwise.
     */
    public function install()
    {
        if (! $this->install_roles()) die("Role table install failed.");
        if (! $this->install_memberships()) die("Membership table install failed.");
        if (! $this->insert_sample_data()) die ("Sample data could not be inserted.");
        
        return true;
    }

    /**
     * Uninstalls the module. This is basically a question of removing tables,
     * and that's exactly what this thing does.
     * @return boolean True on success. False otherwise.
     */
    public function uninstall()
    {
        $this->dbforge->drop_table('roles');
        $this->dbforge->drop_table('memberships');

        return true;
    }

    /**
     * Upgrade procedure. Should handle *any* upgrade after 1.0.0 up to, but not
     * including, 2.0.0.
     * @param type $old_version The previous version installed. Allows us to
     * diff our way into a proper upgrade mechanism.
     * @return boolean True on success. False otherwise.
     */
    public function upgrade($old_version)
    {
        // Your Upgrade Logic
        $version_a = explode('.', $this->version);
        if ($version_a[0] >= 1 and ($version_a[1] > 0 or $version_a[2] > 0))
            throw new Exception("Big explosion error! We haven't set up any upgrade procedures yet!");
        return true;
    }

    /**
     * The help function determines what should pop up when requesting help from
     * the module.
     * @todo Write some useful documentation.
     * @return string The help text, either as a link, or as pure text.
     */
    public function help()
    {
        // Return a string containing help info
        return "See the Github repository at http://www.github.com/Tellus/pyro-memberships for usage information.";
    }
}
