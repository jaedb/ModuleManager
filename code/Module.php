<?php
/**
 * @package modulemanager
 */
class Module extends DataObject {

	public static $db = array(
		'Title'       	=> 'Varchar(128)',
		'Description' 	=> 'Text',
		'Content' 		=> 'HTMLText',
		'Position' 		=> 'Text'
	);

	public static $icon = 'modulemanager/images/cms-icon.png';
	
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Title', 'Title'));
		$fields->addFieldToTab('Root.Main', new TextareaField('Description', 'Description'));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content', 'Content'));

		return $fields;
	}
	
}
