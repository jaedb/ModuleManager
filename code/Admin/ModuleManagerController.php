<?php
class ModuleManagerController extends LeftAndMain {

	static $url_segment = 'module-manager';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $menu_priority = -1;
	static $url_priority = 40;
	static $menu_title = 'Module Manager';
	static $tree_class = 'ModuleManager';
	static $menu_icon = '/modulemanager/images/cms-icon.png';
	
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
		$moduleManager = ModuleManager::current_module_manager();
		$fields = $moduleManager->getCMSFields();
		
		// what pages is this module active on
		$modulesGridField = GridField::create(
				"Modules_Gridfield", 
				"Modules", 
				Module::get(), 
				$modulesGridFieldConfig = GridFieldConfig_RecordEditor::create()
			);
		
		// add multiclass dropdown for modules
		$modulesGridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
		$modulesGridFieldConfig->addComponent(new GridFieldAddNewMultiClass());
		
		// add the fields
		$fields->addFieldToTab('Root.Modules', $modulesGridField);
		
		// module positions tab
		$positionsHtml = '<h2>Module positions</h2>';
		$positionsHtml .= '<p class="message info">To change these you need to edit the positions specified in the <code>_config.php</code> file. These are your currently configured positions available:</p>';
		foreach( ModuleManager::config()->positions as $position ){
			$positionsHtml .= '<p>&bull;&nbsp; <strong>'.$position.'</strong><br />&nbsp; &nbsp; Use in your template with <code>$ModulePosition("'.$position.'");</code></p>';
		}		
		$positionsHtml .= '</ul>';
		$fields->addFieldToTab('Root.Positions', LiteralField::create('html',$positionsHtml));
		
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
