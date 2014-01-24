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
		'Title' => 'Title',
		'Alias' => 'Alias',
		'ModulesCount' => '# of Modules'
	);
	
	public function ModulesCount(){
		$modules = Module::get()->Filter('PositionID', $this->ID);
		if( !$modules )
			return 0;
		return $modules->Count();
	}
	
	// create cms fields
	public function getCMSFields() {
		
		$fields = new FieldList(new TabSet('Root'));

		$fields->addFieldToTab('Root.Main', new TextField('Name', 'Name'));
		$fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (Unique reference)'));

		return $fields;
	}
	
	// convert string into url-friendly string
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
		
		// convert name to lowercase, dashed
		$newAlias = $this->URLFriendly($this->Title);
		
		// get positions that already have this alias
		$positionsThatMatch = ModulePosition::get()->Filter('Alias',$newAlias)->First();
		
		// if we find a match
		if( $positionsThatMatch->ID ){
			
			// create a new unique alias (based on ID)
			$this->Alias = $newAlias .'-'. $this->ID;
		
		// no match, meaning we're safe to use this as a unique alias
		}else{			
			$this->Alias = $newAlias;
		}
		
	}
	
}
