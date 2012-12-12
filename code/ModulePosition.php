<?php
/**
 * @package modulemanager
 */
class ModulePosition extends DataObject {

	public static $db = array(
		'Title'       	=> 'Varchar(128)'
	);

	public static $icon = 'modulemanager/images/definition.png';
	
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Title', 'Title'));

		return $fields;
	}
	
}
