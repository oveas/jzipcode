<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controlleradmin library
jimport('joomla.application.component.controllerform');

/**
 * J!Zipcode Country Controller
 */
class JZipcodeControllerCountry extends JControllerForm
{
	public function display($cachable = false)
	{
		// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'countries'));
		$layout = JRequest::getCmd('layout', 'default');

		// call parent behavior
		parent::display($cachable);
	}

	/**
	 * Delete all zipcodes for a given country
	 */
	public function clear()
	{
		$id = JRequest::getVar('id');

		$count = $this->getModel()->clear($id);
		$message = JText::sprintf('COM_JZIPCODE_MANAGER_COUNTRY_CLEARED', $count);
		$this->setRedirect('index.php?option=com_jzipcode&view=countries', $message);
		return true;
	}
}
