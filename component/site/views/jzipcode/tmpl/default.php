<?php
/**
 * This file is part of J!Zipcode
 * @copyright	Copyright (C) 2012 Oveas Functionality Provider and VergelijkVerhuur.nl. All rights reserved.
 * @author		Oscar van Eijk, Oveas Functionality Provider (http://oveas.com)
 * @license		GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

if ($this->distance >= 0) {
	echo '<h2>' . JText::_('COM_JZIPCODE_CALCULATED_DISTANCE') . '</h2>';
	echo '<p>' . JText::sprintf('COM_JZIPCODE_CALCULATED_DISTANCE_RESULT'
		, $this->zip1, $this->zip2, $this->distance, $this->unit) . '</p>';
}
?>

<h2><?php echo JText::_('COM_JZIPCODE_CALCULATE_DISTANCE'); ?></h2>

<form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="jzipcode" name="jzipcode">
	<fieldset>
		<dl>
			<dt><?php JText::_('COM_JZIPCODE_CALCULATE_ZIP1'); ?></dt>
			<dd>
				<dl>
					<dt><?php echo $this->form->getLabel('zipcode1'); ?></dt>
					<dd><?php echo $this->form->getInput('zipcode1'); ?></dd>
					<dt><?php echo $this->form->getLabel('country1'); ?></dt>
					<dd><?php echo $this->form->getInput('country1'); ?></dd>
				</dl>
			</dd>
			<dt><?php JText::_('COM_JZIPCODE_CALCULATE_ZIP2'); ?></dt>
			<dd>
				<dl>
					<dt><?php echo $this->form->getLabel('zipcode2'); ?></dt>
					<dd><?php echo $this->form->getInput('zipcode2'); ?></dd>
					<dt><?php echo $this->form->getLabel('country2'); ?></dt>
					<dd><?php echo $this->form->getInput('country2'); ?></dd>
				</dl>
			</dd>
			<dt></dt>
			<dd><button type="submit" class="button"><?php echo JText::_('COM_JZIPCODE_CALCULATE'); ?></button></dd>
		</dl>
	</fieldset>
	<input type="hidden" name="option" value="com_jzipcode" />
	<input type="hidden" name="task" value="display" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div class="clr"></div>
