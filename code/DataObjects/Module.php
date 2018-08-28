<?php

namespace Jaedb\ModuleManager;

use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;

class Module extends DataObject {
	
	// set module names
	private static $singular_name = 'Default';
	private static $plural_name = 'Default';
	private static $description = 'Default module';
	
	// set object parameters
	private static $db = array(
		'Title' => 'Varchar(255)',
		'Content' => 'HTMLText',
		'Position' => 'Varchar(255)'
	);
	
	private static $belongs_many_many = array(
		'Pages' => 'SiteTree'
	);
	
	private static $summary_fields = array(
		'Type' => 'Type',
		'Title' => 'Title',
		'Position' => 'Position',
		'Pages.Count' => 'Number of pages'
	);
	
	private static $searchable_fields = array(
		'Title',
		'PositionID'
	);
	
	/**
	 * Build the CMS fields for editing 
	 * @return FieldList
	 **/
	public function getCMSFields() {		
		$fields = parent::getCMSFields();
		
		$fields->removeByName('Pages');
		
		// required information
		$fields->addFieldToTab('Root.Main', HiddenField::create('ModuleID', 'ModuleID', $this->ID));		
		$fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));

		$positions = Config::inst()->get('Jaedb\ModuleManger\ModulePageController', 'positions');

		$fields->addFieldToTab('Root.Main', DropdownField::create(
				'Position',
				'Position',
				$positions
			)->setEmptyString('Please select'));
		$fields->addFieldToTab("Root.Main", TreeMultiselectField::create("Pages", "Shown on pages", "SiteTree"));
		
		return $fields;
	}
	
	/**
	 * Render the module-wrapper template
	 * @return HTMLText
	 **/
	public function ModuleLayout(){
		return $this->renderWith(['Modules/'.$this->ClassName, 'Modules/Module']);
	}	
}
