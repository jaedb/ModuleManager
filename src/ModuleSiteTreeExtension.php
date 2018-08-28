<?php

namespace Jaedb\ModuleManager;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\ORM\DB;
use SilverStripe\Core\Config\Config;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Director;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;

class ModuleSiteTreeExtension extends DataExtension {
	
	private static $db = [
		'InheritModules' => 'Boolean'
	];
	
	private static $defaults = [
		'InheritModules' => 0
	];
	
	private static $many_many = [
		'Modules' => Module::class
	];
	
	private static $many_many_extraFields = [
		'Modules' => [
			'SortOrder' => 'Int'
		]
	];
   
	/**
	 * Build CMS fields for all pages
	 * @param $fields = FieldList of standard fields
	 * @return FieldList obj
	 **/
	public function updateCMSFields(FieldList $fields){
		
		// inherit field
		$fields->addFieldToTab("Root.Modules", $inheritField = CheckboxField::create('InheritModules','Inherit modules')->setDescription('Inherit <strong>all</strong> modules from the parent page. If the parent page is also set to inherit, then we go further up the hierarchy.'));
		
		// module manager gridfield
		$gridFieldConfig = GridFieldConfig_RelationEditor::create();
		//$gridFieldConfig->addComponent(new GridFieldSortableRows('SortOrder'));
		
		$gridField = GridField::create("Modules", "Modules", $this->owner->Modules(), $gridFieldConfig);
		
		$gridFieldConfig->removeComponentsByType(GridFieldAddNewButton::class);
		$gridFieldConfig->addComponent(new GridFieldAddNewMultiClass());
		$gridField->addExtraClass('modulemanager-modules-field');
		if ($this->owner->InheritModules){
			$gridField->addExtraClass('hide');
		}

		$fields->addFieldToTab("Root.Modules", $gridField);
		
		return $fields;
		
	}
	
	// get my parent page
	public function MyParentPage($page){
		return $page->parent;
	}
	
	
	/**
	 * Get all the modules attached to this page (across all ModuleAreas)
	 * If set to inherit, we merge the parent's modules with our own
	 * @return ArrayList
	 **/
	public function PageModules(){	
		$page = $this->owner;

		// check for inheritance by recursively searching
		while ($page->InheritModules && $page->ParentID > 0){
			$page = $this->MyParentPage($page);
		}
		
		return $page->Modules()->sort('SortOrder ASC');
	}	
	
	
	/**
	 * Get all modules for a specific position
	 * @param $alias = string (the alias of the ModulePosition)
	 * @param $limit = int (limit the number of modules to show, optional)
	 * @return HTMLText
	 **/
	public function ModulePosition($alias, $limit = false){

		$positions = Config::inst()->get('ModuleManager', 'positions');
		
		if (!$positions || !isset($positions[$alias])){
			user_error("Trying to call module position \"".$alias."\" but this doesn't exist. Make sure you have setup your custom positions in your config.yml",E_USER_NOTICE);
		}
		
		// get this page's module list for specified position
		$modules = $this->PageModules()->Filter(['Position' => $alias]);
		
		// if we have no modules, then nothing doing
		if (count($modules) <= 0){
			return false;
		}
		
		// allow limiting number of modules, per position
		if ($limit){
			$modules = $modules->limit($limit);
		}
		
		// store them in a template array (for template loop)
		$items = array(
			'Position' => $alias,
			'Modules' => $modules
		);
		
		return $this->owner->customise($items)->renderWith('ModulePosition');
	}
}