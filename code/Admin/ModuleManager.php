<?php

class ModuleManager extends DataObject {

	public function getCMSFields() {
		
		// what pages is this module active on
		$gridFieldConfig = GridFieldConfig_RecordEditor::create();
		$modulesGridField = GridField::create("Modules_Gridfield", "Modules", Module::get(), $gridFieldConfig);
		$modulePositionsGridField = GridField::create("ModulePositions_Gridfield", "Module Positions", ModulePosition::get(), $gridFieldConfig);
		
		$fields = FieldList::create(TabSet::create('Root',
						Tab::create(
							'Modules',
							$modulesGridField
						),
						Tab::create(
							'ModulePositions',
							$modulePositionsGridField
						),
						Tab::create(
							'Main',
							LiteralField::create('html','thanks')
						)
					));
		
		return $fields;
		
		
		$fields = new FieldList(
			new TabSet("Root",
				$tabMain = new Tab('Main',
					$titleField = new TextField("Title", _t('SiteConfig.SITETITLE', "Site title")),
					$taglineField = new TextField("Tagline", _t('SiteConfig.SITETAGLINE', "Site Tagline/Slogan"))
				),
				$tabAccess = new Tab('Access',
					$viewersOptionsField = new OptionsetField("CanViewType", _t('SiteConfig.VIEWHEADER', "Who can view pages on this site?"))
				)
			),
			new HiddenField('ID')
		);
		
		$tabMain->setTitle(_t('SiteConfig.TABMAIN', "Main"));
		$tabAccess->setTitle(_t('SiteConfig.TABACCESS', "Access"));
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	public function setRequest($req){
		return false;
	}
	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$moduleManager = ModuleManager::get()->First();
		if(!$moduleManager) {
			self::make_module_manager();
			DB::alteration_message("Added default module manager","created");
		}
	}
	
	static public function make_module_manager() {
		$moduleManager = ModuleManager::create();
		$moduleManager->write();
		return $moduleManager;
	}
	
	public static function CurrentModuleManager(){		
		$current = ModuleManager::get()->First();		
		return $current;
	}
	
	public function CMSEditLink() {
		return singleton('CMSSettingsController')->Link();
	}

}
