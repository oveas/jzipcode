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
 * JZipcode Country Edit View
 */
class JZipcodeViewCountry extends JView
{
	/**
	 * JZipcode view display method
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$form = $this->get('Form');
		$item = $this->get('Item');
		$model = $this->getModel();
		$item->zipcount = $model->getZipcount($item->country_code);
		$form->setValue('zipcount',null,$item->zipcount);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->form = $form;
		$this->item = $item;
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
		JRequest::setVar('hidemainmenu', true);
		$_new = ($this->item->id == 0);
		JToolBarHelper::title(
			$_new
				? JText::_('COM_JZIPCODE_MANAGER_COUNTRY_NEW')
				: JText::_('COM_JZIPCODE_MANAGER_COUNYRY_EDIT')
		);
		JToolBarHelper::save('country.save');
		JToolBarHelper::cancel('country.cancel'
			, $_new
				? 'JTOOLBAR_CLOSE'
				: 'JTOOLBAR_CANCEL'
		);
		if ($this->getModel()->getZipcount($this->item->country_code) > 0) {
			JToolBarHelper::divider();
			JToolBar::getInstance('toolbar')->appendButton('Confirm'
				, 'COM_JZIPCODE_MANAGER_COUNTRY_CLEAR_CONFIRM'
				,'delete'
				, 'COM_JZIPCODE_MANAGER_COUNTRY_CLEAR'
				, 'country.clear'
				, false
			);
		}
	}
}