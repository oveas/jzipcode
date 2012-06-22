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
jimport('joomla.application.component.controlleradmin');

/**
 * J!Zipcode Countries Controller
 */
class JZipcodeControllerCountries extends JControllerAdmin
{
	/**
	 * Load the model
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel object
	 */
	public function getModel($name = 'Country', $prefix = 'JZipcodeModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
