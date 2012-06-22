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
<form enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_jzipcode'); ?>" method="post" name="uploadForm" id="uploadForm">
	<div class="width-50">
		<fieldset class="uploadform">
			<?php echo JText::_('COM_JZIPCODE_LOAD_DATA_DESC'); ?>
			<div class="clr"> </div>
			<legend><?php echo JText::_('COM_JZIPCODE_LOAD_ZIPCODES'); ?></legend>
			<label for="install_package"><?php echo JText::_('COM_JZIPCODE_DATA_FILE'); ?></label>
			<input class="input_box" id="data_package" name="data_package" type="file" size="57" />
			<input class="button" type="submit" value="<?php echo JText::_('COM_JZIPCODE_LOAD_DATA'); ?>" />
		</fieldset>
	</div>
	<div>
		<input type="hidden" name="task" value="zipcode.loaddata" />
	</div>
</form>
