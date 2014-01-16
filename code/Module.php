<?php
/**
 * @package modulemanager
 */
class Module extends DataObject {
	
	// set object parameters
	public static $db = array(
		'Title'       		=> 'Varchar(128)',
		'Description' 		=> 'Text',
		'Content' 			=> 'HTMLText',
		//'ModulePosition' 	=> 'Int',
		'Alias' 			=> 'Text'
	);
	
	public static $singular_name = 'Module';
	public static $plural_name = 'Modules';
	
	// create relationship with module positions
	public static $has_one = array(
		'Position' => 'ModulePosition'
	);
	
	// set object icon
	public static $icon = 'modulemanager/images/cms-icon.png';
	
	// set gridfield columns
	public static $summary_fields = array(
		'Title',
		'Description',
		'Position.Title'
	);
	
	// re-name gridfield column titles
	static $field_labels = array(
		'Position.Title' => 'Position'
	);
   
	// create cms fields
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Title', 'Title'));
		$fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (unique identifier)'));
		$fields->addFieldToTab('Root.Main', new TextareaField('Description', 'Description'));
		$fields->addFieldToTab('Root.Main', new DropdownField(
				'PositionID',
				'Position',
				$this->GetModulePositions()
			));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content', 'Content'));
		return $fields;
	}
	
	// return list of possible module positions for cms dropdown field
	function GetModulePositions(){
	
		if($Positions = DataObject::get('ModulePosition')){
			return $Positions->map('ID','Title','Please select');
		}else{
			return array('No positions set');
		}
	}
	
	// return list of possible module positions for cms dropdown field
	function ModulePositionName(){
		
		$position = ModulePosition::get()->byID($this->ModulePosition);
		return $position->Title;
	}
	
}
