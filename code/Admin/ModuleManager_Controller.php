<?php
class ModuleManager_Controller extends LeftAndMain {

	static $url_segment = 'module-manager';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $menu_priority = -1;
	static $menu_title = 'Module Manager';
	static $tree_class = 'ModuleManager';
	static $menu_icon = 'modulemanager/images/cms-icon.png';
	
	public function init() {
		parent::init();
		Requirements::javascript(CMS_DIR . '/javascript/CMSMain.EditForm.js');
	}
	
	public function getEditForm($id = null, $fields = null) {
		$moduleManager = ModuleManager::CurrentModuleManager();
		$fields = $moduleManager->getCMSFields();

		// Tell the CMS what URL the preview should show
		$fields->push(new HiddenField('PreviewURL', 'Preview URL', RootURLController::get_homepage_link()));
		// Added in-line to the form, but plucked into different view by LeftAndMain.Preview.js upon load
		$fields->push($navField = new LiteralField('SilverStripeNavigator', $this->getSilverStripeNavigator()));
		$navField->setAllowHTML(true);

		$actions = $moduleManager->getCMSActions();
		$form = CMSForm::create( 
			$this, 'EditForm', $fields, $actions
		)->setHTMLID('Form_EditForm');
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->addExtraClass('cms-content center cms-edit-form');
		// don't add data-pjax-fragment=CurrentForm, its added in the content template instead

		if($form->Fields()->hasTabset()) $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		$form->setHTMLID('Form_EditForm');
		$form->loadDataFrom($moduleManager);
		$form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

		// Use <button> to allow full jQuery UI styling
		$actions = $actions->dataFields();
		if($actions) foreach($actions as $action) $action->setUseButtonTag(true);

		$this->extend('updateEditForm', $form);

		return $form;
	}
	
	public function getSilverStripeNavigator() {
		return $this->renderWith('CMSSettingsController_SilverStripeNavigator');
	}
	
	public function LinkPreview() {
		$record = $this->getRecord($this->currentPageID());
		$baseLink = ($record && $record instanceof Page) ? $record->Link('?stage=Stage') : Director::absoluteBaseURL();
		return $baseLink;
	}

	public function Breadcrumbs($unlinked = false) {
		$defaultTitle = self::menu_title_for_class(get_class($this));
		return new ArrayList(array(
			new ArrayData(array(
				'Title' => _t("{$this->class}.MENUTITLE", $defaultTitle),
				'Link' => false
			))
		));
	}
	
}
