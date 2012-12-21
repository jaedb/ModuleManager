<?php
/**
 * @package modulemanager
 */
class ModulePosition extends DataObject {
	
	// object paramaters
	public static $db = array(
		'Name'       	=> 'Text',
		'Alias'       	=> 'Text'
	);

	// set object icon
	public static $icon = 'modulemanager/images/definition.png';
	
	// set gridfield columns
	public static $summary_fields = array(
		'Title',
		'Alias'
	);
	
	// create cms fields
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
		$fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (Unique reference)'));

		return $fields;
	}
	
}
