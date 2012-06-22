<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
?>
<form action="<?php
	echo JRoute::_('index.php?option=com_jzipcode&layout=edit&id='.(int) $this->item->id);
?>" method="post" name="adminForm" id="jzipcode-form">

	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_JZIPCODE_MANAGER_COUNTRYFORM_DETAILS' ); ?></legend>
		<ul class="adminformlist">
<?php foreach($this->form->getFieldset() as $field): ?>
			<li><?php
				echo $field->label;
				echo $field->input;?>
			</li>
<?php endforeach; ?>
	</ul>
	</fieldset>
	<div>
		<input type="hidden" name="task" value="country.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>