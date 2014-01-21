<?php
class MapModule extends Module {
	
	// set name for this module
	static $singular_name = 'Map Module';
	static $plural_name = 'Map Modules';
	
	// set object parameters
	public static $db = array(
		'URL'       		=> 'Varchar(128)'
	);
   
	// create cms fields
	public function getCMSFields() {
		
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Content', new TextField('URL', 'URL'));
		return $fields;
	}
	
}
