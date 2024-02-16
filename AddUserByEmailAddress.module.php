<?php namespace ProcessWire;

class AddUserByEmailAddress extends WireData implements Module, ConfigurableModule {

	/**
	 * Construct
	 */
	public function __construct() {
		parent::__construct();
		$this->deriveName = 'always';
	}

	/**
	 * Ready
	 */
	public function ready() {
		$this->addHookAfter('ProcessPageAdd::buildForm', $this, 'afterBuildAddForm');
		$this->addHookBefore('ProcessPageAdd::processInput', $this, 'beforeProcessInput');
		if($this->deriveName === 'always') {
			$this->addHookAfter('Users::saveReady', $this, 'afterUserSaveReady');
		} elseif($this->deriveName === 'add') {
			$this->addHookAfter('Users::added', $this, 'afterUserAdded');
		}
	}

	/**
	 * After ProcessPageAdd::buildForm
	 *
	 * @param HookEvent $event
	 */
	protected function afterBuildAddForm(HookEvent $event) {
		/** @var InputfieldForm $form */
		$form = $event->return;
		$modules = $this->wire()->modules;

		// Only if Process is ProcessUser
		if($this->wire()->process != 'ProcessUser') return;
		
		// Return if module should not be activated
		if(!$this->activateModule()) return;

		// Set a temporary random value to the name field and hide the field
		$name = $form->getChildByName('_pw_page_name');
		$name->value = uniqid();
		$name->wrapAttr('style', 'display:none;');

		// Add email field to form
		$email = $this->wire()->fields->get('email');
		$dummy_page = $this->wire()->pages(1);
		$inputfield = $email->getInputfield($dummy_page);
		$inputfield->required = true;
		$form->prepend($inputfield);
	}

	/**
	 * Before ProcessPageAdd::processInput
	 *
	 * @param HookEvent $event
	 */
	protected function beforeProcessInput(HookEvent $event) {
		/** @var InputfieldForm $form */
		$form = $event->arguments(0);

		// Only if Process is ProcessUser
		if($this->wire()->process != 'ProcessUser') return;

		// Return if module should not be activated
		if(!$this->activateModule()) return;
		
		// Check for existing user and show error if match
		$email = $form->getChildByName('email');
		$email->processInput($this->wire()->input->post);
		if($email->getErrors()) {
			$this->wire()->session->location('./');
		}
	}

	/**
	 * After Users::added
	 *
	 * @param HookEvent $event
	 */
	protected function afterUserSaveReady(HookEvent $event) {
		/** @var User $u */
		$u = $event->arguments(0);

		// Return if module should not be activated
		if(!$this->activateModule()) return;

		// Derive user name from email address
		$u->name = $this->deriveNameFromEmail($u);
	}

	/**
	 * After Users::added
	 *
	 * @param HookEvent $event
	 */
	protected function afterUserAdded(HookEvent $event) {
		/** @var User $u */
		$u = $event->arguments(0);

		// Return if module should not be activated
		if(!$this->activateModule()) return;

		// Derive user name from email address
		$name = $this->deriveNameFromEmail($u);
		$u->setAndSave('name', $name);
	}

	/**
	 * Are the conditions met for activating the module?
	 *
	 * @return bool
	 */
	protected function activateModule() {

		// Email field must have the unique flag set
		$email = $this->wire()->fields->get('email');
		if(!$email || !$email->flagUnique) return false;

		// ProcessLogin must be configured to use email login
		$login = $this->wire()->modules->get('ProcessLogin');
		if(!$login->useEmailLogin()) return false;

		return true;
	}

	/**
	 * Derive a user name from the user's email address
	 *
	 * @param User $u
	 * @return string
	 */
	protected function ___deriveNameFromEmail($u) {
		$email = str_replace('@', '-at-', $u->email);
		return $this->wire()->pages->names()->uniquePageName($email, $u);
	}

	/**
	 * Config inputfields
	 *
	 * @param InputfieldWrapper $inputfields
	 */
	public function getModuleConfigInputfields($inputfields) {
		$modules = $this->wire()->modules;

		/** @var InputfieldRadios $f */
		$f = $modules->get('InputfieldRadios');
		$f_name = 'deriveName';
		$f->name = $f_name;
		$f->label = $this->_('Derive user name from email address');
		$f->notes = $this->_('If you select the "Never" option then after the user is added the name will be a random string.');
		$f->addOption('always', $this->_('Always'));
		$f->addOption('add', $this->_('Only when a user is first added'));
		$f->addOption('never', $this->_('Never'));
		$f->optionColumns = 1;
		$f->value = $this->$f_name;
		$inputfields->add($f);
	}

}
