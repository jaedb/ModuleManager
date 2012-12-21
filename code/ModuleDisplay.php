<?php

class ModuleDisplay extends DataObjectDecorator {

	// return all modules of the specified module area
	// called in template with $ModuleArea(module-alias)
	function ModuleArea($alias){
		
		// create container for output code
		$output = '';
		
		// get the module area as an object
		$position = ModulePosition::get()->filter(array('Alias' => $alias))->First();
		
		// get this page's module list
		$pageModules = $this->owner->Modules();		
		
		// loop through our modules
		foreach( $pageModules as $module ){
		
			// if this module matches the specified module area
			if( $module->ModulePosition == $position->ID ){
				$output .= '<div class="module '.$module->Alias.'">';
				$output .= $module->Content;
				$output .= '</div>';
			}
		}
		
		return $output;
	}
	
}