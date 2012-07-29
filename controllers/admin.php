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
            'users/profile_m',
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
            // Pass a colour value to the view.
            // Green: active.
            // Yellow: active, but ending.
            // Red. inactive, ended.
            if (!($m->end_date))
            {
                $m->status_color = 'green';
            }
            elseif ($m->end_date < now())
            {
                $m->status_color = 'yellow';
            }
            else
            {
                $m->status_color = 'red';
            }
        }

        $this->do_template('memberships:memberships', 'memberships');
	}

    public function create()
    {
        $this->item_validation_rules = array(
            array(
                'field' => 'profile_id',
                'label' => lang('memberships:profile'),
                'rules' => 'numeric|required'),
            array(
                'field' => 'role_id',
                'label' => 'roles:id',
                'rules' => 'numeric|required'),
            array(
                'field' => 'start_date',
                'label' => 'memberships:start_date', 
                'rules' => 'required'),
        );

        // Secure values.
        $this->form_validation->set_rules($this->item_validation_rules);

        // Did the form submit correctly?
        if ($this->form_validation->run())
        {
            unset($_POST['btnAction']);
            if ($team = $this->membership_m->create($this->input->post()))
            {
                // Add members. Schedules are only possible after creation.
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/memberships');
            }
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/memberships/create');
            }
        }

        // Assuming validation failed, 
        foreach ($this->item_validation_rules as $rule)
        {
            $this->data->{$rule['field']} = $this->input->post($rule['field']);
        }

        // Build, or rebuild, view.
        $this->data->profiles = $this->get_all_profiles();
        $this->data->roles = $this->get_all_roles();
        $this->do_template('global:create', 'memberships_create');
    }

public function edit($id)
    {
        $this->data = $this->membership_m->get($id);

        $this->item_validation_rules = array(
            array(
                'field' => 'start_date',
                'label' => 'memberships:start_date', 
                'rules' => 'required'),
        );

        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);

            $post = $this->input->post();
            if (empty($post['end_date'])) $post['end_date'] = null;

            // See if the model can update the record
            if($this->membership_m->update($id, $post))
            {
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/memberships');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/memberships/create');
            }
        }

        $this->data = $m = $this->membership_m->get($id);

        $this->data->profiles = $this->get_all_profiles();
        $this->data->roles = $this->get_all_roles();

        $this->data->profile = $this->profile_m->get($m->profile_id);
        $this->data->role = $this->role_m->get($m->role_id);

        $this->data->groups = $this->get_groups($this->data->role->model, 'id', 'name');

        $this->do_template('global:edit', 'memberships_create');
    }

    public function delete($id = 0)
    {
        // Check current user's permissions.
        if (!isset($this->permissions['memberships']['edit_membership']))
        {
            $this->session->set_flashdata('error', lang('memberships:edit_not_allowed'));
            redirect('admnin/memberships');
        }

        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
        {
            $ids = $this->input->post('action_to');
            
            // Delete all teams.
            $this->membership_m->delete_many($ids);
        }
        elseif (is_numeric($id))
        {
            // they just clicked the link so we'll delete that one
            $this->membership_m->delete($id);
        }
        redirect('admin/memberships');
    }

    protected function get_all_profiles()
    {
        $ps = $this->profile_m->get_all();
        $profiles = array();
        foreach ($ps as $prof)
        {
            $profiles[$prof->id] = $prof->display_name;
        }
        return $profiles;
    }

    protected function get_all_roles()
    {
        $rs = $this->role_m->get_all();
        $roles = array();
        foreach($rs as $role)
        {
            $roles[$role->id] = $role->name;
        }
        return $roles;
    }

    /**
     * Attempts to retrieve all viable groups from the specified
     * model.
     * @param string $model Must be a valid model_m form. If _m is
     * not affixed, it will be done automatically.
     * @return Array of key/value pairs - id/name.
     */
    protected function get_groups($model, $keyfield = 'id', $namefield = 'name')
    {
        if (substr($model, -2) != '_m') $model .= '_m';
        
        // Code will fail here if the model was not available.
        $this->load->model(substr($model, 0, -2).'s/'.$model);

        $raw = $this->$model->get_all();

        $groups = array();

        foreach ($raw as $g)
        {
            $groups[$g->$keyfield] = $g->$namefield;
        }

        return $groups;
    }
}
