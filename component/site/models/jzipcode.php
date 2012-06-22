<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelform');

/**
 * JZipcode Model
 */
class JZipcodeModelJZipcode extends JModelForm
{
	/**
	 * Get the data for a calculation
	 */
	public function getForm($data = array(), $loadData = true)
	{

		$app = JFactory::getApplication('site');

		$form = $this->loadForm(
			  'com_jzipcode.jzipcode'
			, 'jzipcode'
			, array('control' => 'jform', 'load_data' => true)
		);
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Trigge the JZipcode event to get the distance
	 * @param	Array	$data	Form data
	 * @return	float	Result from the J!Zipcode plugin or -1 when no data
	 */
	public function getDistance($data)
	{
		if (!$data['zipcode1'] || !$data['country1'] || !$data['zipcode2'] || !$data['country2']) {
			return array('distance' => -1);
		}
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('jzipcode');
		$results = $dispatcher->trigger('onJZipcodeGetDistance'
			, array(
				 array(
					 'zip' => $data['zipcode1']
					,'country' => $data['country1']
				)
				,array(
					 'zip' => $data['zipcode2']
					,'country' => $data['country2']
				)
			)
		);
		if (count($results) === 0 || $results[0] === false) {
			return array('distance' => -1);
		}
		return $results[0];
	}
}
