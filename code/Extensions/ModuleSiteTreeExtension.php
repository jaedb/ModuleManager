<?php
/**
 * @package modulemanager
 */
class ModuleSiteTreeExtension extends DataExtension {
	
	// set object parameters
	public static $db = array(
		'InheritModules' => 'Boolean'
	);
	
	// set object parameters
	public static $defaults = array(
		'InheritModules' => 1
	);
	
	// set object parameters
	public static $belongs_many_many = array(
		'Modules' => 'Module'
	);
   
	// create cms fields
	public function updateCMSFields(FieldList $fields) {	
		
		// create gridfield management for the many_many relationship
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		
		// create the gridfield itself
		$gridField = GridField::create("Modules", "Modules", $this->PageModules(), $gridFieldConfig);
		
		// add multiclass dropdown for modules
		$gridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
		$gridFieldConfig->addComponent(new GridFieldAddNewMultiClass());
		
		// add to fields
		$fields->addFieldToTab("Root.Modules", CheckboxField::create('InheritModules','Inherit modules from parent'));
		$fields->addFieldToTab("Root.Modules", $gridField);
		
		return $fields;
		
	}
	
	// get my parent page
	public function MyParentPage($page){
		return $page->parent;
	}
	
	
	// build list of all Modules attached to this page
	public function PageModules(){
		
		$page = $this->owner;
		
		// check for inheritance by recursively searching
		while( $page->InheritModules && $page->ParentID > 0 )
			$page = $this->MyParentPage( $page );
		
		$modules = $page->getManyManyComponents('Modules');
			
		return $modules;
	}	

	// return all modules of the specified module area
	// called in template with $ModuleArea(module-alias)
	function ModulePosition($alias){
		
		// create container for output code
		$output = '';
		
		// get the module area as an object
		$position = ModulePosition::get()->filter('Alias', $alias)->First();
		
		if( !isset($position->ID) ) user_error("Cannot find a Module Position by that name (".$alias."). Check your template is calling a ModulePosition by an alias that exists!",E_USER_ERROR);
		
		// get this page's module list for specified position
		$modules = $this->PageModules()->Filter('PositionID',$position->ID);		
		
		// store them in a template array (for template loop)
		$items = array(
			'Position' => $position,
			'Items' => $modules
		);
		
		return $this->owner->customise($items)->renderWith('ModuleHolder');
	}
	
	// returns switch if position contains any modules for this page
	function ActiveModulePosition($alias){
		
		// get the module area as an object
		$position = ModulePosition::get()->filter('Alias', $alias)->First();
		
		if( !isset($position->ID) ) user_error("Cannot find a Module Position by that name (".$alias."). Check your template is calling a ModulePosition by an alias that exists!",E_USER_ERROR);
		
		// get this page's module list for specified position
		$modules = $this->PageModules()->Filter('PositionID',$position->ID);
		
		// if there are any modules in this area
		if( $modules->Count() > 0 )
			return true;
			
		return false;		
	}
}
