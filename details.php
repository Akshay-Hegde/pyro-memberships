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
    public $version = '0.1.0';

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
		return array(
			'name' => array(
				'en' => 'Teams',
				'da' => 'Hold',
			),
			'description' => array(
				'en' => 'A tiny module for basic sports team management. Dependency for: memberships',
				'da' => 'Et lille modul til administration af sportshold. Afhænighed for: memberships',
			),
			'frontend' => true,
			'backend' => true,
			'menu' => 'content',
            'roles' => array('create_team', 'edit_team', 'delete_team', 'create_league', 'delete_league', 'edit_league'),
			'sections' => array(
                'teams' => array(
                    'name'  => 'teams:teams', // These are translated from your language file
                    'uri'   => 'admin/teams',
                    'shortcuts' => array(
                        array(
                            'name'  => 'teams:create',
                            'uri'   => 'admin/teams/create',
                            'class' => 'add',
                        ),
                        array(
                            'name'  => 'leagues:create',
                            'uri'   => 'admin/leagues/create',
                            'class' => 'add',
                        ),
                    ),
                ),
                'leagues' => array(
                    'name'  => 'leagues:leagues', // These are translated from your language file
                    'uri'   => 'admin/leagues',
                    'shortcuts' => array(
                        array(
                            'name'  => 'teams:create',
                            'uri'   => 'admin/teams/create',
                            'class' => 'add',
                        ),
                        array(
                            'name'  => 'leagues:create',
                            'uri'   => 'admin/leagues/create',
                            'class' => 'add',
                        ),
                    ),
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
     * Installs the teams table.
     * Primary key  : id(int)
     * name         : name of the team. This can be long and pretty.
     * slug         : short name of the team. Short, to the point. Unique.
     * description  : a longer, free-form description of the team. Optional.
     * league_id    : foreign key to the league that the team is in.
     * Note that the league needn't be an actual LEAGUE. It might be a series,
     * or a championship or a whatever they might call it. It's simply a
     * grouping of the levels within the sports, from amateurs to pros.
     * @return boolean True on success. False otherwise.
     * */
    public function install_teams()
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
            'description' => array(
                'type'  => 'TEXT',
                'null'  => true,
            ),
            'league_id' => array(
                'type'          => 'INT',
                'constraint'    => '11',
                'null'          => 'true'
            ),
        );

        return $this->install_table('teams', $table);
    }

    /**
     * Installs the leagues table.
     * Primary key  : id(int)
     * name         : name of the league.
     * @return boolean True on success. False otherwise.
     * */
    public function install_leagues()
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
        );

        return $this->install_table('leagues', $table);
    }

    /**
     * Quick insert of a new location.
     * @param string $name Name of the location, recognisable.
     * @param string $add Address location. Should be unique to a Google maps
     * search.
     * @param string $desc Arbitrary long-winded description of the location.
     * @return int New location id.
     */
    private function insert_league($name)
    {
        $this->db->insert('leagues', array(
            'name' => $name,
        ));
        return $this->db->insert_id();
    }

    /**
     * Quick insert of a new location.
     * @param string $name Name of the location, recognisable.
     * @param string $slug Unique short form of the name.
     * @param string $league Foreign key to the team's league.
     * @param string $desc Team description - freeform, optional.
     * @return int New location id.
     */
    private function insert_team($name, $slug, $league = null, $desc = null)
    {
        $this->db->insert('teams', array(
            'name' => $name,
            'slug' => $slug,
            'league_id' => $league,
            'description' => $desc,
        ));
        return $this->db->insert_id();
    }

    /**
     * Installs a bunch of sample data to work with.
     * @return boolean True on success, false otherwise.
     */
    private function insert_sample_data()
    {
        $ds = $this->insert_league("Danmarksserien");
        $ed = $this->insert_league("Elitedivision");

        $this->insert_team('Herre 3', 'hs3', $ds, 'My team!');
        $this->insert_team('Herre U21', 'hy', $ds, 'My buddy\'s team.');
        $this->insert_team('Herre 1', 'hs1', $ed, 'The best team.');

        return true;
    }

    /**
     * Inserts all the various settings into th CP. If you want to add a new possible
     * parameter to the Google Maps tags, add a setting with a slug prefixed with
     * 'places_tag_' for automatic detection.
     */
    private function install_settings()
    {
        $success  =$this->settings_m->insert_many(array(
            array(
                'slug' => 'teams_use_simple_frontend',
                'title' => 'Demo frontend',
                'description' => 'Basically enables or disables a frontend view that presents a team ',
                '`default`' => '1',
                '`value`' => '1',
                'type' => 'radio',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => '1=Enabled|0=Disabled',
                'module' => 'teams',
            ),
        ));

        if (!$success) return false;
        else return true;
    }

    /**
     * Complete module installation function. Installs all tables, some sample
     * data to play around with and module settings.
     * @return boolean True on success, false otherwise.
     */
    public function install()
    {
    	// Remove any previous settings.
        $this->db->delete('settings', array('module' => self::MODULE_NAME));

        if (! $this->install_teams()) die("Table install failed.");
        if (! $this->install_leagues()) die("Table install failed.");
        if (! $this->insert_sample_data()) die ("Sample data could not be inserted.");
        if (! $this->install_settings()) die ("Failed to install settings.");
        
        return true;
    }

    /**
     * Uninstalls the module. This is basically a question of removing tables,
     * and that's exactly what this thing does.
     * @return boolean True on success. False otherwise.
     */
    public function uninstall()
    {
        $this->dbforge->drop_table('teams');
        $this->dbforge->drop_table('leagues');

        $this->db->delete('settings', array('module' => self::MODULE_NAME));

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
        return "See the Github repository at http://www.github.com/Tellus/pyro-places for usage information.";
    }
}
