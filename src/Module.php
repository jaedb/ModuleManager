<?php

namespace PlasticStudio\ModuleManager;

use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Config\Config;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TreeMultiselectField;

class Module extends DataObject {
	
	private static $singular_name = 'Module';
	private static $plural_name = 'Modules';
	private static $description = 'Base module class';
	private static $table_name = 'Module';
	
	private static $db = [
		'Title' => 'Varchar(255)',
		'Position' => 'Varchar(255)'
	];
	
	private static $belongs_many_many = [
		'Pages' => SiteTree::class
	];
	
	private static $summary_fields = [
		'ClassName' => 'Type',
		'Title' => 'Title',
		'Position' => 'Position',
		'Pages.Count' => 'Number of pages'
	];
	
	private static $searchable_fields = [
		'Title',
		'Position'
	];
	
	public function getCMSFields() {		
		$fields = parent::getCMSFields();		
		$fields->removeByName('Pages');
		$fields->removeByName('LinkTracking');
		$fields->removeByName('FileTracking');

		$fields->addFieldToTab('Root.Main', HiddenField::create('ModuleID', 'ModuleID', $this->ID));		
		$fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));

		$fields->addFieldToTab('Root.Main', DropdownField::create(
				'Position',
				'Position',
				Config::inst()->get(ModuleManager::class, 'positions')
			)->setEmptyString('Please select'));
		$fields->addFieldToTab("Root.Main", TreeMultiselectField::create("Pages", "Pages", SiteTree::class));
		
		return $fields;
	}

	/**
	 * Type
	 * Based on class name, but the simplified version (ie not FQCN)
	 *
	 * @return string
	 **/
	public function Type(){
        return $this->ClassName;
	}

	
	/**
	 * Render this module
	 * This is auto-overwritten by creating a Modules/{ClassName} template in your templates directory
	 *
	 * @return HTMLText
	 **/
	public function Layout(){
		return $this->renderWith(['Modules/'.$this->Type(), 'Module']);
	}	
}
