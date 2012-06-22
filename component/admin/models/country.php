<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.modeladmin');

/**
 * JZipCode Country Controller
 */
class JZipcodeModelCountry extends JModelAdmin
{
	/**
	 * Get the reference to a table object
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Jzc_country', $prefix = 'JZipcodeTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the form object for country maintenance
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jzipcode.country'
				, 'country'
				, array('control' => 'jform'
					, 'load_data' => $loadData
				)
			);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Get the formdata
	 * @return	mixed	The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jzipcode.edit.country.data', array());
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Get the number of zipcodes currently in the database
	 * @param	string	2character Country Code
	 * @return	mixed	Number of zipcodes or false on errors
	 */
	public function getZipcount ($countryCode)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($db->quoteName('#__jzc_zipcode'));
		$query->where("country_code = '$countryCode'");

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}
		return $count;
	}

	/**
	 * Get the number of zipcodes currently in the database
	 * @param	string	2character Country Code
	 * @return	mixed	Number of zipcodes or false on errors
	 */
	public function clear ($id)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('country_code');
		$query->from($db->quoteName('#__jzc_country'));
		$query->where('id = ' . $id);
		$db->setQuery($query);
		$country_code = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			$this->setError($error);
			return false;
		}

		$query = $db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($db->quoteName('#__jzc_zipcode'));
		$query->where("country_code = '$country_code'");

		$db->setQuery($query);
		$count = $db->loadResult();

		if ($count > 0) {
			try {
				$db->setQuery('DELETE FROM #__jzc_zipcode' . " WHERE country_code = '$country_code'");
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
				$db->setQuery('UPDATE #__jzc_country '
					. "SET data_source    = '' "
					. ",   data_prepared  = '' "
					. ",   data_date      = '0000-00-00' "
					. "WHERE country_code = '$country_code'");
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}

		return $count;
	}
}