<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * JZipcode Distance plugin.
 */
class plgJZipcodeDistance extends JPlugin
{
	/**
	 * Constructor
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Validate the parameters and convert to an array if necessary.
	 * @param	mixed	$zipcodeFrom	Zipcode or an array with the keys 'zip' and 'country'
	 * @param	mixed	$zipcodeTo		Zipcode or an array with the keys 'zip' and 'country' or null for a tablefield (ignored here)
	 * @return	bool	True on succes
	 */
	private function _validateZipcodeParams(&$zipcodeFrom, &$zipcodeTo = null)
	{
		if (!is_array($zipcodeFrom)) {
			$zipcodeFrom = array('zip' => $zipcodeFrom, 'country' => $this->params->get('default_country'));
		}
		if (!is_array($zipcodeTo) && $zipcodeTo !== null) {
			$zipcodeTo = array('zip' => $zipcodeTo, 'country' => $this->params->get('default_country'));
		}
		if (!array_key_exists('zip', $zipcodeFrom)
					|| !array_key_exists('country', $zipcodeFrom)
					|| (
						$zipcodeTo !== null &&
							(!array_key_exists('zip', $zipcodeTo)
								|| !array_key_exists('country', $zipcodeTo)
						)
					)
				) {
			$this->_subject->setError('PLG_JZIPCODE_DISTANCE_INVALID_PARAM');
			return false;
		}
		$zipcodeFrom['zip'] = strtoupper($zipcodeFrom['zip']);
		$zipcodeFrom['zip'] = preg_replace('/\s+/', '', $zipcodeFrom['zip']);
		if ($zipcodeTo !== null) {
			$zipcodeTo['zip'] = strtoupper($zipcodeTo['zip']);
			$zipcodeTo['zip'] = preg_replace('/\s+/', '', $zipcodeTo['zip']);
		}
		return true;
	}

	/**
	 * Read the GPS coordinates from the database
	 * @param	float	$zip	Zipcode
	 * @param	string	$country	Country code
	 * @return	Array	Indexed array with latitude and longitude
	 */
	private function _getCoordinates ($zip, $country)
	{
		$db = JFactory::getDbo();

		$zip = preg_replace('/\s+/', '', $zip);

		$db->setQuery('SELECT latitude, longitude '
				. 'FROM '.$db->quoteName('#__jzc_zipcode')
				. "WHERE zip_code = '".strtoupper($zip)."' "
				. "AND country_code = '".strtoupper($country)."'"
		);

		$result = $db->loadAssoc();
		if ($db->getErrorNum()) {
			$this->_subject->setError($db->getErrorMsg());
			return false;
		}
		if ($result === null || count($result) == 0) {
			$this->_subject->setError('PLG_JZIPCODE_DISTANCE_NO_RESULTS', $zip, $country);
			return false;
		}
		return $result;
	}

	/**
	 * Calculate the distance between 2 zipcodes in a given or the default country
	 * @param	mixed	$zipcodeFrom	Zipcode or an array with the keys 'zip' and 'country'
	 * @param	mixed	$zipcodeTo		Zipcode or an array with the keys 'zip' and 'country'
	 * @return	float	Rounded distance, false on errors
	 */
	public function onJZipcodeGetDistance($zipcodeFrom, $zipcodeTo)
	{
		if ($this->_validateZipcodeParams($zipcodeFrom, $zipcodeTo) === false) {
			return false;
		}

		if (($coordFrom = $this->_getCoordinates($zipcodeFrom['zip'], $zipcodeFrom['country'])) === false) {
			return false;
		}
		if (($coordTo   = $this->_getCoordinates($zipcodeTo['zip'],   $zipcodeTo['country']))   === false) {
			return false;
		}

		$lat1 = $coordFrom['latitude']  * (pi() / 180);
		$lng1 = $coordFrom['longitude'] * (pi() / 180);
		$lat2 = $coordTo['latitude']    * (pi() / 180);
		$lng2 = $coordTo['longitude']   * (pi() / 180);

		$dLat = $lat2 - $lat1;
		$dLng = $lng2 - $lng1;
		$a = sin($dLat / 2) * sin($dLat / 2) + cos($lat1) * cos($lat2) * sin($dLng / 2) * sin($dLng / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

		$distance = round(
				 ((2 * atan2(sqrt($a), sqrt(1 - $a))) * $this->params->get('earth_radius'))
				,$this->params->get('precision'));

		return array(
			 'distance' => $distance
			,'unit' => $this->params->get('distance_unit')
			,'precision' => $this->params->get('precision')
		);
	}

	/**
	 * Create the SQL query to calculate the distance between 2 zipcodes.
	 * This plugin works only within the default country!
	 * @param	mixed	$zipcodeFrom	Zipcode or an array with the keys 'zip' and 'country'
	 * @param	mixed	$zipcodeField	Field to compare with in the format 'table(alias).field.key',
	 * where 'table' is the table name from which the zipcode is taken, 'field' is the zipcode field
	 * and 'key' is the primary key of 'table'. 'Table' must be in the callers query.
	 * @return	string	SQL query that can be used as a subselect, false on errors
	 */
	public function onJZipcodeGetQuery($zipcodeFrom, $zipcodeField)
	{
		if ($this->_validateZipcodeParams($zipcodeFrom) === false) {
			return false;
		}
		$zipTo = explode('.', $zipcodeField);
		if (count($zipTo) != 3) {
			$this->_subject->setError('PLG_JZIPCODE_DISTANCE_INVALID_PARAM');
			return false;
		}
		return '(SELECT ROUND( '
			. ' (2 * ATAN2( '
			. '   SQRT( '
			. '      SIN(((jzc_zipcode_b.latitude * (PI() / 180))-(jzc_zipcode_a.latitude * (PI() / 180))) / 2) '
			. '    * SIN(((jzc_zipcode_b.latitude * (PI() / 180))-(jzc_zipcode_a.latitude * (PI() / 180))) / 2) '
			. '    + COS((jzc_zipcode_a.latitude * (PI() / 180))) '
			. '    * COS((jzc_zipcode_b.latitude * (PI() / 180))) '
			. '    * SIN(((jzc_zipcode_b.longitude * (PI() / 180))-(jzc_zipcode_a.longitude * (PI() / 180))) / 2) '
			. '    * SIN(((jzc_zipcode_b.longitude * (PI() / 180))-(jzc_zipcode_a.longitude * (PI() / 180))) / 2)) '
			. ' , SQRT( '
			. '      1-SIN(((jzc_zipcode_b.latitude * (PI() / 180))-(jzc_zipcode_a.latitude * (PI() / 180))) / 2) '
			. '      * SIN(((jzc_zipcode_b.latitude * (PI() / 180))-(jzc_zipcode_a.latitude * (PI() / 180))) / 2) '
			. '      + COS((jzc_zipcode_a.latitude * (PI() / 180))) '
			. '      * COS((jzc_zipcode_b.latitude * (PI() / 180))) '
			. '      * SIN(((jzc_zipcode_b.longitude * (PI() / 180))-(jzc_zipcode_a.longitude * (PI() / 180))) / 2) '
			. '      * SIN(((jzc_zipcode_b.longitude * (PI() / 180))-(jzc_zipcode_a.longitude * (PI() / 180))) / 2)) '
			. '   ) * '.$this->params->get('earth_radius').' '
			. '  ), '.$this->params->get('precision').') '
			. 'FROM #__jzc_zipcode AS jzc_zipcode_a '
			. ',    #__jzc_zipcode AS jzc_zipcode_b '
			. "WHERE jzc_zipcode_a.zip_code = '".$zipcodeFrom['zip']."' "
			. "  AND jzc_zipcode_a.country_code = '".$zipcodeFrom['country']."' "
			. '  AND jzc_zipcode_b.zip_code = ( '
				. "SELECT UPPER(REPLACE($zipTo[1], ' ', '')) "
				. "FROM $zipTo[0] AS jzc_jointable "
				. "WHERE $zipTo[2] = $zipTo[0].$zipTo[2]) "
			. "  AND jzc_zipcode_b.country_code = '".$this->params->get('default_country')."' "
			. ') ';

	}

	/**
	 * Show the distance unit for display
	 * @return	string	Unit text
	 */
	public function onJZipcodeShowUnit()
	{
		return $this->params->get('distance_unit');
	}

}