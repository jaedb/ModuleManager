<?php
class ModuleManagerController extends LeftAndMain {

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

	public function getResponseNegotiator() {
		$neg = parent::getResponseNegotiator();
		$controller = $this;
		$neg->setCallback('CurrentForm', function() use(&$controller) {
			return $controller->renderWith($controller->getTemplatesWithSuffix('_Content'));
		});
		return $neg;
	}
	
	public function getEditForm($id = null, $fields = null) {
		
		// get the cmsfields from ModuleManager DataObject
		$moduleManager = ModuleManager::CurrentModuleManager();
		$fields = $moduleManager->getCMSFields();
		
		// what pages is this module active on
		$gridFieldConfig = GridFieldConfig_RecordEditor::create();
		$modulesGridField = GridField::create("Modules_Gridfield", "Modules", Module::get(), $gridFieldConfig);
		$modulePositionsGridField = GridField::create("ModulePositions_Gridfield", "Module Positions", ModulePosition::get(), $gridFieldConfig);
		
		// add the fields
		$fields->addFieldToTab('Root.Modules', LiteralField::create('html','<a href="'.$this->Link().'add/Module">Add new standard module</a>'));
		$fields->addFieldToTab('Root.Modules', $modulesGridField);
		$fields->addFieldToTab('Root.ModulePositions', $modulePositionsGridField);
		$fields->addFieldToTab('Root.ModulePositions', LiteralField::create('html','<em>To load a position into your template, simply write <code>$ModulePosition(Alias)</code> where <code>Alias</code> is your position alias</em>'));
		
		// actions
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

		return $form;
	}
	
	public function getSilverStripeNavigator() {
		return $this->renderWith('CMSSettingsController_SilverStripeNavigator');
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
	
	
	
	/* ================================================================================= ADDING NEW MODULES ============ */	
	/* ================================================================================================================= */	
	
	
	/* ================================== THE FORM ======= */
	
	public function AddForm() {
		
		$fields = new FieldList(); //new LiteralField('Hints', 'Testing') );
		
		$actions = new FieldList(
			FormAction::create("doAdd", _t('CMSMain.Create',"Create"))
				->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
				->setUseButtonTag(true),
			FormAction::create("doCancel", _t('CMSMain.Cancel',"Cancel"))
				->addExtraClass('ss-ui-action-destructive ss-ui-action-cancel')
				->setUseButtonTag(true)
		);
		
		$form = CMSForm::create( 
			$this, "AddForm", $fields, $actions
		)->setHTMLID('Form_AddForm');
		//$form->setResponseNegotiator($this->getResponseNegotiator());
		//$form->addExtraClass('cms-add-form stacked cms-content center cms-edit-form ' . $this->BaseCSSClasses());
		//$form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

		return $form;
	}
	
	
	
	/* ================================== THE ACTIONS ======= */

	public function doAdd($data, $form) {
	
		$className = isset($data['ModuleType']) ? $data['ModuleType'] : "Module";

		$record = $this->getNewItem("new-$className-$parentID".$suffix, false);
		if(class_exists('Translatable') && $record->hasExtension('Translatable') && isset($data['Locale'])) {
			$record->Locale = $data['Locale'];
		}

		try {
			$record->write();
		} catch(ValidationException $ex) {
			$form->sessionMessage($ex->getResult()->message(), 'bad');
			return $this->getResponseNegotiator()->respond($this->request);
		}

		$editController = singleton('CMSPageEditController');
		$editController->setCurrentPageID($record->ID);

		Session::set(
			"FormInfo.Form_EditForm.formError.message", 
			_t('CMSMain.PageAdded', 'Successfully created page')
		);
		Session::set("FormInfo.Form_EditForm.formError.type", 'good');
		
		return $this->redirect(Controller::join_links(singleton('CMSPageEditController')->Link('show'), $record->ID));
	}

	public function doCancel($data, $form) {
		return $this->redirect(singleton('CMSMain')->Link());
	}
	
}
