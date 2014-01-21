<?php

class ModuleManager extends DataObject {

	public function getCMSFields() {
		// construct the field container
		$fields = FieldList::create( $tabSet = TabSet::create('Root'));
		
		return $fields;
	}
	
	/* add a save button (if we decide to use this dataobject
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
	}*/
	
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
		return singleton('ModuleManagerController')->Link();
	}

}
