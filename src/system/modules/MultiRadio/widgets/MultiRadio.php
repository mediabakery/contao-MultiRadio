<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package   MultiRadio
 * @author    Sebastian Tilch
 * @license   LGPL
 * @copyright Sebastian Tilch 2013
 */

namespace Mediabakery\MultiRadio;


/**
 * Class MultiRadio
 *
 */
class MultiRadio extends \CheckBox
{


	/**
	 * Check for a valid option (see #4383)
	 */
	public function validate()
	{

		$varValue = deserialize($this->getPost($this->strName));
		$varNewValue = array();
		foreach($varValue as $strKey=>$arrValue)
		{
			if (is_array($arrValue))
			{
				if (count($arrValue) == 1)
				{
					$varNewValue[] = $arrValue[0];
				}
				else if (count($arrValue) > 1)
				{
					$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalid'], $strKey));
				}
			}
		}
		$varValue = $varNewValue;
		$this->Input->setPost($this->strName, serialize($varValue));

		if ($varValue != '' && !$this->isValidOption($varValue))
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['invalid'], $varValue));
		}

		parent::validate();
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$GLOBALS['TL_CSS']['MULTIRADIO'] = 'system/modules/MultiRadio/html/styles/multiradio.css';
		$arrOptions = array();

		if (!$this->multiple && count($this->arrOptions) > 1)
		{
			$this->arrOptions = array($this->arrOptions[0]);
		}

		// The "required" attribute only makes sense for single radios
		if (!$this->multiple && $this->mandatory)
		{
			$this->arrAttributes['required'] = 'required';
		}

		$state = $this->Session->get('radio_groups');

		// Toggle the radio group
		if (\Input::get('cbc'))
		{
			$state[\Input::get('cbc')] = (isset($state[\Input::get('cbc')]) && $state[\Input::get('cbc')] == 1) ? 0 : 1;
			$this->Session->set('radio_groups', $state);

			$this->redirect(preg_replace('/(&(amp;)?|\?)cbc=[^& ]*/i', '', \Environment::get('request')));
		}

		$blnFirst = true;
		$blnCheckAll = true;

		foreach ($this->arrOptions as $i=>$arrOption)
		{
			// Single dimension array
			if (is_numeric($i))
			{
				$arrOptions[] = $this->generateRadio($arrOption, $i);
				continue;
			}

			$id = 'cbc_' . $this->strId . '_' . standardize($i);

			$img = 'folPlus';
			$display = 'none';

			if (!isset($state[$id]) || !empty($state[$id]))
			{
				$img = 'folMinus';
				$display = 'block';
			}

			$arrOptions[] = '<div class="radio_toggler' . ($blnFirst ? '_first' : '') . '"><a href="' . $this->addToUrl('cbc=' . $id) . '" onclick="AjaxRequest.toggleCheckboxGroup(this,\'' . $id . '\');Backend.getScrollOffset();return false"><img src="' . TL_FILES_URL . 'system/themes/' . $this->getTheme() . '/images/' . $img . '.gif" width="18" height="18" alt="toggle radio group"></a>' . $i .	'</div><fieldset id="' . $id . '" class="tl_radio_container radio_options" style="display:' . $display . '">';

			// Multidimensional array
			foreach ($arrOption as $k=>$v)
			{
				$arrOptions[] = $this->generateRadio($v, $i, $k);
			}

			$arrOptions[] = '</fieldset>';
			$blnFirst = false;
		}

		// Add a "no entries found" message if there are no options
		if (empty($arrOptions))
		{
			$arrOptions[]= '<p class="tl_noopt">'.$GLOBALS['TL_LANG']['MSC']['noResult'].'</p>';
		}

		if ($this->multiple)
		{
			return sprintf('<fieldset id="ctrl_%s" class="tl_radio_container%s"><legend>%s%s%s%s</legend><input type="hidden" name="%s" value="">%s</fieldset>%s',
							$this->strId,
							(($this->strClass != '') ? ' ' . $this->strClass : ''),
							($this->required ? '<span class="invisible">'.$GLOBALS['TL_LANG']['MSC']['mandatory'].'</span> ' : ''),
							$this->strLabel,
							($this->required ? '<span class="mandatory">*</span>' : ''),
							$this->xlabel,
							$this->strName,
							str_replace('<br></fieldset><br>', '</fieldset>', implode('<br>', $arrOptions)),
							$this->wizard);
		}
		else
		{
	        return sprintf('<div id="ctrl_%s" class="tl_radio_single_container%s"><input type="hidden" name="%s" value="">%s</div>%s',
	        				$this->strId,
							(($this->strClass != '') ? ' ' . $this->strClass : ''),
							$this->strName,
							str_replace('<br></div><br>', '</div>', implode('<br>', $arrOptions)),
							$this->wizard);
		}
	}


	/**
	 * Generate a radio and return it as string
	 * @param array
	 * @param integer
	 * @return string
	 */
	protected function generateRadio($arrOption, $i, $k='0')
	{
		return sprintf('<input type="radio" name="%s" id="opt_%s" class="tl_radio" value="%s"%s%s onfocus="Backend.getScrollOffset()"> <label for="opt_%s">%s</label>',
						$this->strName . ($this->multiple ? '['.$i.'][]' : ''),
						$this->strId.'_'.$i.'_'.$k,
						($this->multiple ? specialchars($arrOption['value']) : 1),
						$this->isChecked($arrOption),
						$this->getAttributes(),
						$this->strId.'_'.$i.'_'.$k,
						$arrOption['label']);
	}
}
