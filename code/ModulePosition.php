<?php
/**
 * @package modulemanager
 */
class ModulePosition extends DataObject {

	public static $db = array(
		'Name'       	=> 'Text',
		'Alias'       	=> 'Text'
	);

	public static $icon = 'modulemanager/images/definition.png';
	
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
		$fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (Unique reference)'));

		return $fields;
	}
	
}
