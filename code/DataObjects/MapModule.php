<?php
/**
 * @package modulemanager
 */
class MapModule extends Module {
	
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
