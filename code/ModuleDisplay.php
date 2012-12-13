<?php

class ModuleDisplay extends DataObjectDecorator {

	// return all modules of the specified module area
	// called in template with $ModuleArea(module-alias)
	function ModuleArea($alias){
		
		// get the module area as an object
		$position = ModulePosition::get()->filter(array('Alias' => $alias))->First();
		
		// get all modules of this module area
		$modules = Module::get()->filter(array('ModulePosition' => $position->ID));
		
		// create container for output code
		$output = '';
		
		// loop through each module
		foreach( $modules as $module ){
			$output .= '<div class="module '.$module->Alias.'">';
			$output .= $module->Content;
			$output .= '</div>';
		}
		
		return $output;
	}
	
}