<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * JZipcode Countrylist View
 */
class JZipcodeViewCountries extends JView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $listOrder;
	protected $listDirn;

	/**
	 * JZipcode view display method
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->listOrder	= $this->escape($this->state->get('list.ordering'));
		$this->listDirn		= $this->escape($this->state->get('list.direction'));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Add the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Create the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_JZIPCODE_COUNTRIES_MAINTAIN'));
		JToolBarHelper::addNew('country.add');
		JToolBarHelper::deleteList('COM_JZIPCODE_COUNTRIES_CONFIRM_DELETE', 'countries.delete');
		JToolBarHelper::divider();

		JToolBarHelper::publishList('countries.publish');
		JToolBarHelper::unpublishList('countries.unpublish');
	}
}