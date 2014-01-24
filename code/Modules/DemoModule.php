<?php
class DemoModule extends Module {
	
	// set name for this module
	static $singular_name = 'Demo Custom Module';
	static $plural_name = 'Demo Custom Modules';
	static $description = 'A module to be used as a demo for building your own modules';
	
	// set object parameters
	public static $db = array(
		'MyCustomField' => 'HTMLText'
	);
   
	// create cms fields
	public function getCMSFields() {
		
		$fields = parent::getCMSFields();

		$fields->addFieldToTab('Root.Content', TextareaField::create('MyCustomField', 'My Custom Field'));
		return $fields;
	}
	
}
