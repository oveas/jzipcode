<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

//$user		= JFactory::getUser();
//$userId		= $user->get('id');
//$listOrder	= $this->listOrder;
//$listDirn	= $this->escape($this->state->get('list.direction'));
//$canOrder	= $user->authorise('core.edit.state', 'com_contact.category');
//$saveOrder	= $listOrder == 'a.ordering';
?>

<tr>
	<th width="5">
		<?php echo JText::_('COM_JZIPCODE_JZIPCODE_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JZIPCODE_JZIPCODE_HEADING_COUNTRY', 'c.country_name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="15">
		<?php echo JHtml::_('grid.sort', 'JSTATUS', 'c.published', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="150">
		<?php echo JHtml::_('grid.sort', 'COM_JZIPCODE_JZIPCODE_HEADING_ZIPCOUNT', 'zip_count', $this->listDirn, $this->listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'COM_JZIPCODE_JZIPCODE_HEADING_DATE', 'date', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>
