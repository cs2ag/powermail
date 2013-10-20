<?php

/**
 * Get Captcha
 *
 * @package TYPO3
 * @subpackage Fluid
 */
class Tx_Powermail_ViewHelpers_Misc_CaptchaViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Configuration
	 */
	protected $settings;

	/**
	 * Returns Captcha-Image String
	 *
	 * @return 	string		HTML-Tag for Captcha image
	 */
	public function render() {
		$captcha = t3lib_div::makeInstance('Tx_Powermail_Utility_CalculatingCaptcha');
		return $captcha->render($this->settings);
	}

	/**
	 * Object initialization
	 */
	public function initializeObject() {
		$typoScriptSetup = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		$this->settings = $typoScriptSetup['plugin.']['tx_powermail.']['settings.']['setup.'];
	}
}