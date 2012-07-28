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
 * Admin controller for coaches.
 * @package memberships
 * @subpackage coaches
 */
class Admin_Coaches extends Admin_SportsBase
{
    /**
     * Used by the PyroCMS admin code to match shortcuts in details.php to
     * controllers' admin views.
     */
    protected $section = 'coaches';

    /**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     */
	public function __construct()
	{
		parent::__construct();

        // Load the required classes
        $this->load->model(array(
            'coach_m',
            'team_m',
        ));
		
		// Set the validation rules.
        // For a coach, we only place the restriction that a user can't be two different coaches.
        $this->item_validation_rules = array(
            array(
                'field' => 'profile_id',
                'label' => lang('sports:coach:name'),
                'rules' => 'trim|required'),
        );

		// We'll set the partials and metadata here since they're used everywhere
		$this->template->append_js('module::admin.js')
						->append_css('module::admin.css');
	}

	/**
     * Displays all coaches in a nice abbreviated list.
     * @param int $offset Pagination offset.
     * @todo Implement pagination.
     */
	public function index($offset = 0)
	{
		$coaches = $this->coach_m->get_all();
		
        foreach ($coaches as $coach)
        {
            $coach->team_count = $this->coach_m->get_team_count($coach->id);
        }

        $this->data->coaches =& $coaches;

		$this->template->title($this->module_details['name'])
						->build('admin/coaches', $this->data);
	}

    /**
     * Renders the view for creating coaches and handles submissions of the
     * create form.
     */
    public function create()
    {
        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);
            if (isset($_POST['user_name'])) unset($_POST['user_name']);

            // See if the model can create the record
            if($this->coach_m->create($this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/sports/coaches');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/sports/coaches/create');
            }
        }

        $users = $this->coach_m->get_all_non_coaches();
        $this->data->user_list = array();
        foreach ($users as $user)
        {
            $this->data->user_list[$user->id] = $user->display_name;
        }
        $this->data->all_teams = $this->get_teams_dropdown_array();
        
        // Build the view using sports/views/admin/team_create
        $this->template->title($this->module_details['name'], lang('global:add'))
                       ->build('admin/coaches_create', $this->data);
    }

    /**
     * Renders the view for editing coaches and handles submissions of the edit
     * form.
     * @param int $id Coach id to edit.
     */
    public function edit($id)
    {
        $this->data = $this->coach_m->get($id);

        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);
            if ($_POST['user_name']) unset($_POST['user_name']);

            // See if the model can create the record
            if($this->coach_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/sports/coaches');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/sports/coaches/create');
            }
        }

        $this->data->all_teams = $this->get_teams_dropdown_array();
        
        $cur_teams = $this->team_m->get_for_coach($id);
        // Distill
        $this->data->cur_teams = array();
        foreach ($cur_teams as $team)
        {
            $this->data->cur_teams[$team->id] = $team->id;
        }

        // Build the view using sports/views/admin/team_create
        $this->template->title($this->module_details['name'], lang('global:add'))
                       ->build('admin/coaches_create', $this->data);
    }

    /**
     * Deletes a coach from the database. Should also cascade and handle team
     * relationships.
     * @param int $id Coach to delete.
     * @todo Implement support for deletion of multiple coaches.
     */
    public function delete($id)
    {
        // Delete coach in db. coach_m should handle the links to teams.
        $this->coach_m->delete($id);
        redirect('admin/sports/coaches');
    }

    /**
     * Preps an array of all teams in an HTML5 select-compatible array.
     * @return Array key=team_id, value=team_name
     */
    protected function get_teams_dropdown_array()
    {
        $teams = $this->team_m->get_all();

        // Distill
        $ret = array();
        foreach ($teams as $team)
        {
            $ret[$team->id] = $team->name;
        }

        return $ret;
    }
}
