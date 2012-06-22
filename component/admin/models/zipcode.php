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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');

/**
 * JZipCode Country Controller
 */
class JZipcodeModelZipcode extends JModelAdmin
{
	/**
	 * Get the reference to a table object
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Jzc_zipcode', $prefix = 'JZipcodeTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the form object for zipcode loading maintenance
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jzipcode.upload'
				, 'upload'
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
	 *
	 * Enter description here ...
	 * @param	string	Reference to a aessage text, will be filled by this method
	 * @return	boolean True on success, false otherwise
	 */
	public function loaddata(&$msg)
	{
		if (($data = $this->_getDataFile()) === false) {
			// Try to cleanup anyway
			$this->_cleanUploads($data);
			return false;
		}

		// Search the install dir for an XML file
		$files = JFolder::files($data['extractdir'], '\.xml$', 1, true);

		if (!count($files)) {
			JError::raiseWarning(1, JText::_('COM_JZIPCODE_NO_XML_FILE'));
			$this->_cleanUploads($data);
			return false;
		}

		$processed = 0;
		$countryModel =& JModel::getInstance('Country', 'JZipcodeModel');

		foreach ($files as $file) {
			if (!$xml = JFactory::getXML($file)) {
				continue;
			}

			if ($xml->getName() == 'jzcdata') {
				$country = (string) $xml->country->name;
				$countryCode = (string) $xml->country->code;
				$dataSource = (string) $xml->country->source;
				$dataPrepared = (string) $xml->country->prepared;
				$dataDate = (string) $xml->country->date;
				if (!$this->_addCountry ($country, $countryCode, $dataSource, $dataPrepared, $dataDate)) {
					$this->_cleanUploads($data);
					unset($xml);
					return false;
				}
				// _addZipcode always returns 1...
				$countBefore = $countryModel->getZipCount($countryCode);
				foreach ($xml->zipcodes->zip as $zipcode) {
					$processed += $this->_addZipcode ($countryCode
						, (string) $zipcode
						, (float) $zipcode->attributes()->lat
						, (float) $zipcode->attributes()->lng
					);
				}
				$countAfter = $countryModel->getZipCount($countryCode);
				$count = ($countAfter - $countBefore);
				$dropped = $processed - $count;
				unset($xml);
			}
		}
		$this->_cleanUploads($data);
		$msg = JText::sprintf('COM_JZIPCODE_MANAGER_COUNTRY_LOADED', $count, $country, $dropped);
		return true;
	}

	/**
	 * Add a new country or update if it already exists
	 * @param string $country Country name
	 * @param string $countryCode 2Char ISO country code
	 * @param string $dataSource Description and URL of the original datasource
	 * @param string $dataPrepared Description and URL of the data preparer
	 * @return Boolean True on success
	 */
	private function _addCountry ($country, $countryCode, $dataSource= '', $dataPrepared = '', $dataDate = '0000-00-00')
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from($db->quoteName('#__jzc_country'));
		$query->where("country_code = '$countryCode'");
		if ($db->loadResult() == 0) {
			$db->setQuery('INSERT INTO #__jzc_country '
				. '(country_name, country_code, data_source, data_prepared) '
				. 'VALUES '
				. "('$country', '$countryCode', '$dataSource', '$dataPrepared')"
			);
		} else {
			$db->setQuery('UPDATE #__jzc_country '
				. "SET country_name   = '$country' "
				. ",   data_source    = '$dataSource' "
				. ",   data_prepared  = '$dataPrepared' "
				. ",   data_date      = '$dataDate' "
				. "WHERE country_code = '$countryCode'"
			);

		}
		try {
			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Add a zipcode
	 * @param string $countryCode Country code
	 * @param string $zipcode Zipcode
	 * @param float $lat Latitude
	 * @param float $lng Longitude
	 * @return int 1 on succes, 0 on failure
	 */
	private function _addZipcode ($countryCode, $zipcode, $lat, $lng)
	{
		$db = $this->getDbo();
		$db->setQuery('INSERT IGNORE INTO #__jzc_zipcode '
			. '(zip_code, country_code, latitude, longitude) '
			. 'VALUES '
			. "('$zipcode', '$countryCode', $lat, $lng)"
		);
		try {
			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return 0;
		}
		return 1;
	}

	/**
	 * Upload and unpack the datafile and check the contents
	 * @return Array with the name of the uploaded file ('packagefile')
	 * and the unpack location ('extractdir')
	 */
	private function _getDataFile()
	{
		// Get the uploaded file information
		$datafile = JRequest::getVar('data_package', null, 'files', 'array');
		if (!(bool) ini_get('file_uploads')) {
			JError::raiseWarning('', JText::_('COM_JZIPCODE_UPLOADS_DISABLED'));
			return false;
		}
		if (!extension_loaded('zlib')) {
			JError::raiseWarning('', JText::_('COM_JZIPCODE_ZLIB_NOT_LOADED'));
			return false;
		}
		if (!is_array($datafile)) {
			JError::raiseWarning('', JText::_('COM_JZIPCODE_NO_UPLOAD'));
			return false;
		}
		if ($datafile['error'] || $datafile['size'] < 1) {
			JError::raiseWarning('', JText::_('COM_JZIPCODE_UPLOAD_FAILED'));
			return false;
		}

		// Build the appropriate paths
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path') . '/' . $datafile['name'];
		$tmp_src	= $datafile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		if (!JFile::upload($tmp_src, $tmp_dest)) {
			return false;
		}

		// Path to the archive
		$archivename = $tmp_dest;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('jzipcode_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($tmp_dest) . '/' . $tmpdir);
		$archivename = JPath::clean($archivename);

		// Do the unpacking of the archive
		if (!JArchive::extract($archivename, $extractdir)) {
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$data['extractdir'] = $extractdir;
		$data['packagefile'] = $archivename;

		return $data;
	}

	/**
	 * Cleanup the upload
	 * @param Array Name of the uploaded file ('packagefile') and the unpack location
	 */
	private function _cleanUploads($data)
	{
		$config = JFactory::getConfig();

		if (is_dir($data['extractdir'])) {
			JFolder::delete($data['extractdir']);
		}

		if (is_file($data['packagefile'])) {
			JFile::delete($data['packagefile']);
		}
	}
}