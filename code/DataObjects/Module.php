<?php
/**
 * @package modulemanager
 */
class Module extends DataObject {
	
	// set object parameters
	static $db = array(
		'Title'       		=> 'Varchar(128)',
		'Description' 		=> 'Text',
		'Content' 			=> 'HTMLText',
		'Alias' 			=> 'Text'
	);
	
	static $singular_name = 'Module';
	static $plural_name = 'Modules';
	
	static $has_one = array(
		'Position' => 'ModulePosition'
	);
	
	static $many_many = array(
		'Pages' => 'SiteTree'
	);
	
	static $summary_fields = array(
		'Title',
		'Description',
		'Position.Title'
	);
	
	static $searchable_fields = array(
		'Title',
		'PositionID'
	);
	
	static $field_labels = array(
		'Position.Title' => 'Position'
	);
   
	// create cms fields
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));
		
		// required information
		$fields->addFieldToTab('Root.Information', new ReadonlyField('ClassName', 'Module Type', $this->ClassName));		
		$fields->addFieldToTab('Root.Information', new TextField('Title', 'Title'));
		$fields->addFieldToTab('Root.Information', new TextField('Alias', 'Alias (unique identifier)'));
		$fields->addFieldToTab('Root.Information', new TextareaField('Description', 'Description'));
		$fields->addFieldToTab('Root.Information', new DropdownField(
				'PositionID',
				'Position',
				$this->GetModulePositions()
			));
		
		// the module content itself
		$fields->addFieldToTab('Root.Content', new HTMLEditorField('Content', 'Content'));
		
		// what pages is this module active on
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		$gridField = new GridField("Pages", "Pages", $this->Pages(), $gridFieldConfig);
		$fields->addFieldToTab("Root.Pages", $gridField);
		
		return $fields;
	}
	
	// return list of possible module positions for cms dropdown field
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
	
	// return list of possible module positions for cms dropdown field
	function ModulePositionName(){
		
		$position = ModulePosition::get()->byID($this->ModulePosition);
		return $position->Title;
	}
	
}
