<?php

class ModuleManager extends DataObject {
	
	private static $db = array(
		'Positions' => 'Text'
	);
	
	public function getCMSFields() {
		$fields = FieldList::create( $tabSet = TabSet::create('Root'));		
		return $fields;
	}
	
	public function setRequest($req){
		return false;
	}
	
	public function CMSEditLink() {
		return singleton('ModuleManagerController')->Link();
	}

	public function populateDefaults() {
		$this->Positions = 'asdfasdfasfd';//serialize( array('header','footer','sidebar') );
		
		// Allow these defaults to be overridden
		parent::populateDefaults();
	}
	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$moduleManager = DataObject::get_one('ModuleManager');
		if(!$moduleManager) {
			self::make_module_manager();
			DB::alteration_message("Added default module manager","created");
		}
	}
	
	public static function make_module_manager() {
		$moduleManager = ModuleManager::create();
		$moduleManager->write();
		return $moduleManager;
	}
	
	/**
	 * Get the current site's ModuleManager, and creates a new one through 
	 * {@link make_module_manager()} if none is found.
	 *
	 * @return ModuleManager
	 */
	public static function current_module_manager() {
		if( $moduleManager = DataObject::get_one('ModuleManager') ){
			return $moduleManager;
		}
		
		return self::make_module_manager();
	}
	
	
	/**
	 * Get the positions
	 * @return array
	 **/
	public static function get_positions(){
		return ModuleManager::config()->positions;
	}
	
	
	/**
	 * Build positions array into dropdown key => value format for CMS fields
	 * @return array
	 **/
	public static function get_positions_dropdown(){
		$array = [];
		foreach( ModuleManager::config()->positions as $position ){
			$array[$position] = $position;
		}
		return $array;
	}
	
	
	/**
	 * Get the positions in string format (for building Enum values)
	 * @return array
	 **/
	public static function getPositionsAsString(){
		$positions = self::getPositions();
		$string = '';
		foreach( $positions as $position ){
			if( $string != '' ){
				$string .= ',';
			}
			$string .= '"'.$position.'"';
		}
		return $string;
	}

}
