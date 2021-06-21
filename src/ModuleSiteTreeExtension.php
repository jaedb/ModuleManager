<?php

namespace PlasticStudio\ModuleManager;

use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

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

		if ($this->owner->InheritModules){
			$tab_name = "Root.Modules";
		} else {
			$tab_name = "Root.Modules (".$this->owner->Modules()->count().")";
		}

		$fields->removeByName('Modules');
		
		$fields->addFieldToTab($tab_name, $inherit_field = CheckboxField::create('InheritModules','Inherit modules')->setDescription('Inherit <strong>all</strong> modules from the parent page. If the parent page is also set to inherit, then we go further up the hierarchy.'));

		// When we inherit, the page's modules become irrelevant
		if (!$this->owner->InheritModules){
			$gridfield_config = GridFieldConfig_RelationEditor::create();

			$gridfield = GridField::create("Modules", "Modules", $this->owner->Modules(), $gridfield_config);
			$gridfield->addExtraClass('modulemanager-modules-field');

			$gridfield_config->addComponent(new GridFieldOrderableRows('SortOrder'));
			$gridfield_config->addComponent(new GridFieldAddNewMultiClass());
			$gridfield_config->removeComponentsByType(GridFieldAddNewButton::class);
			$gridfield_config->removeComponentsByType(GridFieldPageCount::class);

			$fields->addFieldToTab($tab_name, $gridfield);
		}

		
		return $fields;
		
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
			$page = $page->parent();
		}

		// We're at the top, but we're still set to inherit, so let's inherit an array of nothing
		if ($page->InheritModules){
			return ArrayList::create();
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

		$positions = Config::inst()->get(ModuleManager::class, 'positions');
		
		if (!$positions || !isset($positions[$alias])){
			user_error("Module position \"".$alias."\" doesn't exist. Have setup your custom positions in your config.yml?",E_USER_NOTICE);
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