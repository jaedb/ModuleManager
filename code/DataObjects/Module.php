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
		'Title' => 'Varchar(128)',
		'Content' => 'HTMLText',
		'Alias' => 'Text'
	);
	
	private static $has_one = array(
		'Position' => 'ModulePosition'
	);
	
	private static $belongs_many_many = array(
		'Pages' => 'SiteTree'
	);
	
	private static $summary_fields = array(
		'Type' => 'Type',
		'Title' => 'Title',
		'PositionNameNice' => 'Position',
		'Pages.Count' => 'Number of pages'
	);
	
	private static $searchable_fields = array(
		'Title',
		'PositionID'
	);
	
	/**
	 * The position name that this module is assigned to
	 * @return string
	 **/
	public function PositionNameNice(){
		if( $this->Position()->ID > 0 )
			return $this->Position()->Title .' ('.$this->Position()->Alias.')';
		return false;
	}
	
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
		$fields->addFieldToTab('Root.Main', TextField::create('Alias', 'Alias (unique identifier)'));
		$fields->addFieldToTab('Root.Main', DropdownField::create(
				'PositionID',
				'Position',
				$this->GetModulePositions()
			));
        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('Content', 'Content') );
		
		$pagesField = TreeMultiselectField::create("Pages", "Shown on pages", "SiteTree");
		$fields->addFieldToTab("Root.Main", $pagesField);
		
		return $fields;
	}
	
	/**
	 * List all ModulePosition objects
	 * @return array
	 **/
	function GetModulePositions(){
	
		if($Positions = DataObject::get('ModulePosition')){
			
			// construct container
			$map = array();
			
			// loop each position and inject into map
			foreach( $Positions as $Position ){
				$map[$Position->ID] = $Position->Title .' ('.$Position->Alias.')';
			}
			
			return $map;
		}else{
			return array('You need to create a Module Position first');
		}
	}
	
	/**
	 * Get this ModulePosition's name
	 * @return string
	 **/
	function ModulePositionName(){
		
		$position = ModulePosition::get()->byID($this->ModulePosition);
		return $position->Title;
	}
	
	/**
	 * Render the module-wrapper template
	 * @return HTMLText
	 **/
	public function ModuleLayout(){
		
		// try rendering with this module's own template
		$output = $this->renderWith($this->ClassName);
		
		// no custom template, so use base template (Model.ss)
		//if( !$output )
			//$output = $this->renderWith('Module');
			// TODO: Make this work. To make it work we actually need to properly check if we have a custom template for this class.
		
		return $output;
	}
	
	
	/**
	 * Convert string into url-friendly string
	 * @param $string = string (ie the title)
	 * @return string
	 **/
	public function URLFriendly( $string ){
	
		// replace non letter or digits by -
		$string = preg_replace('~[^\\pL\d]+~u', '-', $string);
		$string = trim($string, '-');

		// transliterate
		$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

		// lowercase
		$string = strtolower($string);

		// remove unwanted characters
		$string = preg_replace('~[^-\w]+~', '', $string);

		if (empty($string))
			return 'n-a';

		return $string;
	}
	
	// before saving, check alias
	public function onBeforeWrite(){
	
		parent::onBeforeWrite();
		
		// convert name to lowercase, dashed
		$newAlias = $this->URLFriendly($this->Title);
		
		// get positions that already have this alias
		$moduleAliasesThatMatch = ModulePosition::get()->Filter('Alias',$newAlias)->First();
		
		// if we find a match
		if( isset($moduleAliasesThatMatch->ID) ){
			
			// create a new unique alias (based on ID)
			$this->Alias = $newAlias .'-'. $this->ID;
		
		// no match, meaning we're safe to use this as a unique alias
		}else{			
			$this->Alias = $newAlias;
		}
		
	}
	
}
