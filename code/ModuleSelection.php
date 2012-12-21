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
		
		// open container div
        $fields->addFieldToTab("Root.Modules", new LiteralField('html', '<div id="module-selection-table">'));   
		
		// heading
        $fields->addFieldToTab("Root.Modules", new LiteralField('html', '<h2>Select the modules you want to appear on this page</h2>'));   
		
		$headingsHTML = '
				<div id="module-selection-headings">
					<span class="input-spacer"></span>
					<span class="title col">Title</span>
					<span class="position col">Position</span>
					<span class="preview col">Preview</span>
				</div>
			';
		
		// column headings
        $fields->addFieldToTab("Root.Modules", new LiteralField('headings', $headingsHTML));   
		
		// module selection
		$fields->addFieldToTab('Root.Modules', new CheckboxSetField( 'Modules', '', $this->AvailableModules() ));
		
		// close container div
        $fields->addFieldToTab("Root.Modules", new LiteralField('html', '<div id="module-selection-footer"></div></div>'));   

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
