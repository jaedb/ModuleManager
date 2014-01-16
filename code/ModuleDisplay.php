<?php

class ModuleDisplay extends DataExtension {

	// return all modules of the specified module area
	// called in template with $ModuleArea(module-alias)
	function ModuleArea($alias){
		
		// create container for output code
		$output = '';
		
		// get the module area as an object
		$position = ModulePosition::get()->filter('Alias', $alias)->First();
		
		// get this page's module list
		$pageModules = $this->owner->Modules();		
		
		// store them in a template array (for template loop)
		$items = array(
			'Position' => $position,
			'Items' => $pageModules
		);
		
		return $this->owner->customise($items)->renderWith('ModuleHolder');
		
	}
	
}