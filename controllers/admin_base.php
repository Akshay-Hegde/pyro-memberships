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
 * Abstract base clas that supplies some nice-to-haves and always-uses.
 * @package sports
 */
abstract class Admin_BaseController extends Admin_Controller
{
	/**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     */
	public function __construct()
	{
		parent::__construct();

		$this->load->library(array(
            'form_validation',
        ));

        // We use this to get coaches for the teams rather than just numbers.
		$this->lang->load(array(
			'teams',
			'leagues',
		));
	}

	/**
	 * Short-hand for rendering a view with a specific title.
	 * @param string $name Title. Will be run through lang(), so no funny
	 * business.
	 * @param string $view The admin view to render. This string will
	 * automatically be prefixed with "admin/" before being passed on to the
	 * template builder.
	 */
	protected function do_template($name, $view)
	{
		$this->template->title($this->module_details['name'], lang($name))
	                   ->append_metadata($this->load->view('fragments/wysiwyg', $this->data, TRUE))
	                   ->build("admin/$view", $this->data);
	}
}
