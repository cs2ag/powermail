<?php
class Tx_Powermail_Domain_Validator_MandatoryValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * formRepository
	 *
	 * @var Tx_Powermail_Domain_Repository_FormRepository
	 *
	 * @inject
	 */
	protected $formRepository;

	/**
	 * Return variable
	 *
	 * @var bool
	 */
	protected $isValid = TRUE;

	/**
	 * Validation of given Params
	 *
	 * @param array $params Answers
	 * @return bool
	 */
	public function isValid($params) {
		$formUid = $params['__identity'];
		$form = $this->formRepository->findByUid($formUid);
		if (!method_exists($form, 'getPages')) {
			return $this->isValid;
		}

		foreach ($form->getPages() as $page) { // every page in current form
			foreach ($page->getFields() as $field) { // every field in current page

				// if not a mandatory field
				if (!$field->getMandatory()) {
					continue;
				}

				// set error
				if (is_array($params[$field->getUid()])) {
					$empty = 1;
					foreach ($params[$field->getUid()] as $value) {
						if (strlen($value)) {
							$empty = 0;
							break;
						}
					}
					if ($empty) {
						$this->addError('mandatory', $field->getUid());
						$this->isValid = FALSE;
					}
				} else {
					if (!strlen($params[$field->getUid()])) {
						$this->addError('mandatory', $field->getUid());
						$this->isValid = FALSE;
					}
				}
			}
		}

		return $this->isValid;
	}

}