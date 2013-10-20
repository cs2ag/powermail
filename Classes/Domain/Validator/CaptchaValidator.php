<?php
class Tx_Powermail_Domain_Validator_CaptchaValidator extends Tx_Powermail_Domain_Validator_AbstractValidator {

	/**
	 * fieldRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FieldRepository
	 *
	 * @inject
	 */
	protected $fieldRepository;

	/**
	 * formRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormRepository
	 *
	 * @inject
	 */
	protected $formRepository;

	/**
	 * Captcha Session clean (only if mail is out)
	 *
	 * @var bool
	 */
	protected $clearSession;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = TRUE;

	/**
	 * Captcha Field found
	 *
	 * @var bool
	 */
	protected $captchaFound = FALSE;

	/**
	 * Validation of given Captcha fields
	 *
	 * @param array $params
	 * @return bool
	 */
	public function isValid($params) {
		if (!$this->formHasCaptcha($params['__identity'])) {
			return $this->isValid;
		}

		foreach ((array) $params as $uid => $value) {
			// get current field values
			$field = $this->fieldRepository->findByUid($uid);
			if (!method_exists($field, 'getUid')) {
				continue;
			}

			// if not a captcha field
			if ($field->getType() != 'captcha') {
				continue;
			}

			// if field wrong code given - set error
			$captcha = $this->objectManager->get('Tx_Powermail_Utility_CalculatingCaptcha');
			if (!$captcha->validCode($value, $this->clearSession)) {
				$this->addError('captcha', $uid);
				$this->isValid = FALSE;
			}

			// Captcha field found
			$this->captchaFound = TRUE;
		}

		if ($this->captchaFound) {
			return $this->isValid;
		} else {
			// if no captcha vars given
			$this->addError('captcha', 0);
			return FALSE;
		}
  	}

	/**
	 * Checks if given form has a captcha
	 *
	 * @param int $formUid
	 * @return int
	 */
	protected function formHasCaptcha($formUid) {
		$form = $this->formRepository->hasCaptcha($formUid);
		return count($form);
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$piVars = t3lib_div::_GET('tx_powermail_pi1');
		$this->clearSession = ($piVars['action'] == 'create' ? TRUE : FALSE); // clear captcha on create action
	}
}