<?php
/**
 * @package modulemanager
 */
class ModuleSiteTreeExtension extends DataExtension {
	
	// set object parameters
	public static $db = array();
	
	// set object parameters
	public static $belongs_many_many = array(
		'Modules' => 'Module'
	);
   
	// create cms fields
	public function updateCMSFields(FieldList $fields) {	
		
		// create gridfield management for the many_many relationship
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		
		// create the gridfield itself
		$gridField = new GridField("Modules", "Modules", $this->PageModules(), $gridFieldConfig);
		
		// add to fields
		$fields->addFieldToTab("Root.Modules", $gridField);
		
		return $fields;
		
	}
	
	// build list of all Modules attached to this page
	public function PageModules(){
		$modules = $this->owner->getManyManyComponents('Modules');
		return $modules;
	}
	

	// return all modules of the specified module area
	// called in template with $ModuleArea(module-alias)
	function ModuleArea($alias){
		
		// create container for output code
		$output = '';
		
		// get the module area as an object
		$position = ModulePosition::get()->filter('Alias', $alias)->First();
		
		// get this page's module list
		$modules = $this->PageModules();		
		
		// store them in a template array (for template loop)
		$items = array(
			'Position' => $position,
			'Items' => $modules
		);
		
		return $this->owner->customise($items)->renderWith('ModuleHolder');
		
	}
	
}
