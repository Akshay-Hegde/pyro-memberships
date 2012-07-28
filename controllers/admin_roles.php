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
 * Admin controller for roles.
 * @package memberships
 * @subpackage roles
 */
class Admin_Roles extends Admin_BaseController
{
    /**
     * Used by the PyroCMS admin code to match shortcuts in details.php to
     * controllers' admin views.
     */
    protected $section = 'memberships';

    /**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     */
	public function __construct()
	{
		parent::__construct();

        // Load the required classes
        $this->load->model(array(
            'role_m',
            'membership_m',
        ));
		
		// Set the validation rules.
        // For a coach, we only place the restriction that a user can't be two different coaches.
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => lang('roles:name'),
                'rules' => 'trim|required'),
            ),
            array(
                'field' => 'slug',
                'label' => lang('roles:slug'),
                'rules' => 'trim|required|is_unique[roles.slug]'),
            ),
            array(
                'field' => 'model',
                'label' => lang('roles:model'),
                'rules' => 'trim|required'),
            ),
        );
	}

	/**
     * Displays all roles for quick-access modification.
     * @param int $offset Pagination offset.
     * @todo Implement pagination.
     */
	public function index($offset = 0)
	{
        $roles = $this->role_m->get_all();

		$this->do_template('roles:roles', 'roles');
	}

    /**
     * Role creation.
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

            // See if the model can create the record
            if($this->role_m->create($this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/memberships/roles');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/memberships/roles/create');
            }
        }

        $this->do_template('roles:create', 'roles_create');
    }

    /**
     * Renders the view for editing coaches and handles submissions of the edit
     * form.
     * @param int $id Coach id to edit.
     */
    public function edit($id)
    {
        $this->data = $this->role_m->get($id);

        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);

            // See if the model can create the record
            if($this->role_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/memberships/roles');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/memberships/roles/create');
            }
        }

        $this->do_template('roles:edit', 'roles_create');
    }

    /**
     * Deletes a coach from the database. Should also cascade and handle team
     * relationships.
     * @param int $id Coach to delete.
     * @todo Implement support for deletion of multiple coaches.
     */
    public function delete($id)
    {
        // Delete coach in db. role_m should handle the links to teams.
        $this->role_m->delete($id);
        redirect('admin/memberships/roles');
    }
}
