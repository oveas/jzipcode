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
 * HTML View class for the J!Zipcode Component
 */
class JZipcodeViewJZipcode extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$data = JRequest::getVar('jform');
		$model = $this->getModel();
		$distance = $model->getDistance($data);
		$this->assignRef('distance', $distance['distance']);
		$this->assignRef('unit', $distance['unit']);
		$this->assignRef('precision', $distance['precision']);
		$this->assignRef('zip1', $data['zipcode1']);
		$this->assignRef('zip2', $data['zipcode2']);
		$this->assignRef('zip2', $data['zipcode2']);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		parent::display($tpl);
	}

	private function _getDistance($data)
	{
		if (!$data['zipcode1'] || !$data['country1'] || !$data['zipcode2'] || !$data['country2']) {
			return -1;
		}
		$dispatcher = JDispatcher::getInstance();
//		JPluginHelper::importPlugin('content', 'jzipcode');
		$results = $dispatcher->trigger('onZipcodeGetDistance'
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
		if (count($results) === 0) {
			return -1;
		}
	//print_r($results); exit;
		return $results[0];
	}

}

