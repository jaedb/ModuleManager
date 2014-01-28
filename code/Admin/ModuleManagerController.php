<?php
class ModuleManagerController extends LeftAndMain {

	static $url_segment = 'module-manager';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $menu_priority = -1;
	static $url_priority = 40;
	static $menu_title = 'Module Manager';
	static $tree_class = 'ModuleManager';
	static $menu_icon = 'module-manager/images/cms-icon.png';
	
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
		$modulesGridField = GridField::create(
				"Modules_Gridfield", 
				"Modules", 
				Module::get(), 
				$modulesGridFieldConfig = GridFieldConfig_RecordEditor::create()
			);
		$modulePositionsGridField = GridField::create(
				"ModulePositions_Gridfield", 
				"Module Positions", 
				ModulePosition::get(), 
				GridFieldConfig_RecordEditor::create()
			);
		
		// add multiclass dropdown for modules
		$modulesGridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
		$modulesGridFieldConfig->addComponent(new GridFieldAddNewMultiClass());
		
		// add the fields
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
	
	// builds a list of all the module types (classes that extend Module)
	public function ModuleTypes() {
		$moduleTypes = array();
		$modules = Module::get();
		foreach( $modules as $module ){
			$moduleTypes[$module->ClassName] = array(
					'ClassName' => $module->ClassName,
					'Name' => $module->ModuleName(),
					'Description' => $module->ModuleDescription()
				);
		}
		return $moduleTypes;
	}
	
}
