<?php

class ModuleManager extends DataObject {

	public function getCMSFields() {
		
		// what pages is this module active on
		$gridFieldConfig = GridFieldConfig_RecordEditor::create();
		$modulesGridField = GridField::create("Modules_Gridfield", "Modules", Module::get(), $gridFieldConfig);
		$modulePositionsGridField = GridField::create("ModulePositions_Gridfield", "Module Positions", ModulePosition::get(), $gridFieldConfig);
		/*
		$fields->addFieldToTab('Root.Modules',$modulesGridField);
		$fields->addFieldToTab('Root.ModulePositions',$modulePositionsGridField);
			*/		
		$fields = FieldList::create( $tabSet = TabSet::create('Root',
						$modulesTab = Tab::create(
							'Modules',
							$modulesGridField
						),
						$modulePositionsTab = Tab::create(
							'ModulePositions',
							$modulePositionsGridField
						)
					),
					HiddenField::create('ID')
				);
		
		$tabSet->addExtraClass('ui-tabs');
		$modulesTab->addExtraClass('tab ui-tabs-panel');
		$modulePositionsTab->addExtraClass('tab ui-tabs-panel');
		
		$this->extend('updateCMSFields', $fields);
		
		return $fields;
	}
	
	public function getCMSActions() {
		if (Permission::check('ADMIN') || Permission::check('EDIT_SITECONFIG')) {
			$actions = new FieldList(
				FormAction::create('save_siteconfig', _t('CMSMain.SAVE','Save'))
					->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
			);
		} else {
			$actions = new FieldList();
		}
		
		$this->extend('updateCMSActions', $actions);
		
		return $actions;
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
