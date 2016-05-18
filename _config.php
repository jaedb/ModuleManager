<?php

// define this directory
define('MODULE_MANAGER_DIR', basename(dirname(__FILE__)) );

// include css
LeftAndMain::require_css(MODULE_MANAGER_DIR . '/css/cms.css');

// add functionality to SiteTree
Object::add_extension('SiteTree', 'ModuleSiteTreeExtension');

CMSMenu::remove_menu_item('ModuleManagerAddController');
CMSMenu::remove_menu_item('ModuleManagerEditController');

