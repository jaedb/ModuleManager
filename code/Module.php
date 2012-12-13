<?php
/**
 * @package modulemanager
 */
class Module extends DataObject {

	public static $db = array(
		'Title'       		=> 'Varchar(128)',
		'Description' 		=> 'Text',
		'Content' 			=> 'HTMLText',
		'ModulePosition' 	=> 'Int',
		'Alias' 			=> 'Text'
	);
	
	public static $has_one = array(
		'ModulePosition' => 'ModulePosition'
	);

	public static $icon = 'modulemanager/images/cms-icon.png';
	
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Title', 'Title'));
		$fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (unique identifier)'));
		$fields->addFieldToTab('Root.Main', new TextareaField('Description', 'Description'));
		$fields->addFieldToTab('Root.Main', new DropdownField(
				'ModulePosition',
				'Position',
				$this->GetModulePositions()
			));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content', 'Content'));
		return $fields;
	}
	
	function GetModulePositions(){
	
		if($Positions = DataObject::get('ModulePosition')){
			return $Positions->map('ID', 'Name', 'Please Select');
		}else{
			return array('No positions set');
		}
	}
	
}
