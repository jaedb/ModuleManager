<?php
/**
 * @package modulemanager
 */
class Module extends DataObject {
	
	// set module names
	private static $singular_name = 'Generic';
	private static $plural_name = 'Generic';
	private static $description = 'Standard Module';
	
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
	 * Identify this page component type
	 * Used in GridField for type identification
	 * @return array|integer|double|string|boolean
	 **/
	public function Type(){
		return $this->singular_name();
	}
	
	/**
	 * Identify this page component type
	 * Used in GridField for type identification
	 * @return array|integer|double|string|boolean
	 **/
	public function getDescription(){
		return $this->stat('description');
	}
	
	/**
	 * Build the CMS fields for editing 
	 * @return FieldList
	 **/
	public function getCMSFields() {
		
		$fields = parent::getCMSFields();
		
		$fields->removeByName('Pages');
		
		// required information
		$fields->addFieldToTab('Root.Main', HiddenField::create('ModuleID', 'ModuleID', $this->ID));
		
		$fields->addFieldToTab('Root.Main', LiteralField::create('html','<h3 style="margin-bottom: 5px;">'.$this->Type().'</h3>') );
		$fields->addFieldToTab('Root.Main', LiteralField::create('html','<p><em>'.$this->getDescription().'</em></p><br />') );
		
		$fields->addFieldToTab('Root.Main', TextField::create('Title', 'Title'));
		$fields->addFieldToTab('Root.Main', DropdownField::create(
				'Position',
				'Position',
				ModuleManager::get_positions_dropdown()
			)->setEmptyString('Please select'));
		$fields->addFieldToTab("Root.Main", TreeMultiselectField::create("Pages", "Shown on pages", "SiteTree"));
        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Content') );
		
		return $fields;
	}
	
	/**
	 * Render the module-wrapper template
	 * @return HTMLText
	 **/
	public function ModuleLayout(){
		
		// try rendering with this module's own template
		$output = $this->renderWith('Modules/'.$this->ClassName);
		
		// no custom template, so use base template (Model.ss)
		//if( !$output )
			//$output = $this->renderWith('Module');
			// TODO: Make this work. To make it work we actually need to properly check if we have a custom template for this class.
		
		return $output;
	}	
}
