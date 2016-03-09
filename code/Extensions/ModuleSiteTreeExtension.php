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
		'InheritModules' => 0
	);
	
	// set object parameters
	public static $many_many = array(
		'Modules' => 'Module'
	);
	
	public static $many_many_extraFields = array(
		'Modules' => array(
			'SortOrder' => 'Int'
		)
	);
   
	/**
	 * Build CMS fields for all pages
	 * @param $fields = FieldList of standard fields
	 * @return FieldList obj
	 **/
	public function updateCMSFields(FieldList $fields){
		
		// inherit field
		$fields->addFieldToTab("Root.Modules", $inheritField = CheckboxField::create('InheritModules','Inherit modules'));
		$inheritField->addExtraClass('buttonify modulemanager-inherit-field');
		$inheritField->setDescription('Inherit <strong>additional</strong> modules from the parent page. If the parent page is also set to inherit, then we go further up the hierarchy.');
		
		// module manager gridfield
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		$gridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
		
		$gridField = GridField::create("Modules", "Modules", $this->owner->Modules(), $gridFieldConfig);
		
		$gridFieldConfig->removeComponentsByType('GridFieldAddNewButton');
		$gridFieldConfig->addComponent(new GridFieldAddNewMultiClass());
		$gridField->addExtraClass('modulemanager-modules-field');
		$fields->addFieldToTab("Root.Modules", $gridField);
		
		return $fields;
		
	}
	
	// get my parent page
	public function MyParentPage($page){
		return $page->parent;
	}
	
	
	/**
	 * Get all the modules attached to this page (across all ModuleAreas)
	 * If set to inherit, we merge the parent's modules with our own
	 * @return ArrayList
	 **/
	public function PageModules(){
	
		$page = $this->owner;
		$modules = array();
		
		// check for inheritance by recursively searching
		while( $page->InheritModules && $page->ParentID > 0 ){
			$page = $this->MyParentPage( $page );
		}
		
		// add the inherited page modules
		if( $this->owner->InheritModules ){
			foreach( $page->getManyManyComponents('Modules')->sort('SortOrder ASC') as $module ){
				$modules[] = $module;
			}
		}
		
		// and merge in our own ones too
		foreach( $this->owner->getManyManyComponents('Modules')->sort('SortOrder ASC') as $module ){
			$modules[] = $module;
		}
		
		return ArrayList::create($modules);
	}	
	
	
	/**
	 * Get all modules for a specific position
	 * @param $alias = string (the alias of the ModulePosition)
	 * @return HTMLText
	 **/
	public function ModulePosition( $alias ){
		
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
	
	
	/**
	 * Detect if this there are any modules on this page for this module area
	 * @return boolean
	 **/
	public function HasModules( $alias ){
		
		// get the module area as an object
		$position = ModulePosition::get()->filter('Alias', $alias)->First();
		
		if( !isset($position->ID) ) user_error("Cannot find a Module Position by that name (".$alias."). Check your template is calling a ModulePosition by an alias that exists!",E_USER_ERROR);
		
		// get this page's module list for specified position
		$modules = $this->PageModules()->Filter('PositionID',$position->ID);
		
		return $modules->Count() > 0;	
	}
}
