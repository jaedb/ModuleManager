<?php

use SilverStripe\Forms\TextField;
use Jaedb\ModuleManager\Module;

class Example extends Module {
	
	private static $type = 'Example';
	private static $singular_name = 'Example module';
	private static $plural_name = 'Example modules';
	private static $description = 'Example custom module';
	
	// set object parameters
	private static $db = [
		'Blurb' => 'Text'
	];
	
	public function getCMSFields() {		
		$fields = parent::getCMSFields();		

		$fields->addFieldToTab('Root.Main', TextField::create('Blurb', 'Blurb'));
		
		return $fields;
	}
}
