<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item): ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<?php echo '<a href="'
				. JRoute::_('index.php?option=com_jzipcode&task=country.edit&id='.(int) $item->id)
				. '">' . $item->country_name . '</a>'; ?>
		</td>
		<td align="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'countries.'); ?>
		</td>
		<td>
			<?php echo $item->zip_count; ?>
		</td>
		<td>
			<?php if ($item->data_date != '0000-00-00') {
				echo JHtml::_('data_date', $item->date, JText::_('DATE_FORMAT_LC'));
			} ?>
		</td>
	</tr>
<?php endforeach; ?>

