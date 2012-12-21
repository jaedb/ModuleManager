<?php
/**
 * @package modulemanager
 */
class ModuleSelection extends DataExtension {
	
	// set object parameters
	public static $db = array();
	
	// create relationship with modules
	public static $many_many = array(
		'Modules' => 'Module'
	);
   
	// create cms fields
	public function updateCMSFields(FieldList $fields) {	
		
		// create gridfield management for the many_many relationship
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		
		// create the gridfield itself
		$gridField = new GridField("Modules", "Modules", $this->owner->Modules(), $gridFieldConfig);
		
		// add to fields
		$fields->addFieldToTab("Root.Modules", $gridField);
		
		return $fields;
		
	}
	
	public function AvailableModules(){
		
		if($modules = Module::get()) {
			
			$moduleList = array();
			
			foreach($modules as $module){
				$moduleList[$module->ID] = '<span class="title col">'. $module->Title .'</span><span class="position col">'.$module->ModulePositionName().'</span><span class="preview col">Preview</span>';
			}
			
			return $moduleList;
			
		} else {
			return array('No modules available');
		}
	
	}
	
	public function ModulesSelected(){
		return '----- HI ---- ';
	}
	
}
