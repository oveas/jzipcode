<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * JZipcode Countries Model
 */
class JZipcodeModelCountries extends JModelList
{
	/**
	 * Method to auto-populate the model state
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$filter_order = JRequest::getCmd('filter_order', 'c.country_name');
		$filter_order_Dir = JRequest::getCmd('filter_order_Dir', 'ASC');
		$this->setState('list.ordering', $filter_order);
		$this->setState('list.direction', $filter_order_Dir);

		$start = JRequest::getVar('limitstart', 0, '', 'int');
		$limit = $this->getUserStateFromRequest('global.list.limit', 'limit', JFactory::getApplication()->getCfg('list_limit'), 'int');
		$start = ($limit != 0 ? (floor($start / $limit) * $limit) : 0);
		$this->setState('list.start', $start);
		$this->setState('list.limit', $limit);
	}


	/**
	 * Method to build an SQL query to load the list data.
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->select('(SELECT COUNT(z.zip_code) '
						.'FROM #__jzc_zipcode AS z '
						.'WHERE z.country_code=c.country_code) '
						.'AS zip_count');
		$query->from('#__jzc_country AS c');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.published = ' . (int) $published);
		} elseif ($published === '') {
			$query->where('(c.published = 0 OR c.published = 1)');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('c.country_id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(c.country_name LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->getState('list.ordering');
		$orderDirn	= $this->getState('list.direction');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
}
