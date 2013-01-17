<?php

// define this directory
define('MODULE_MANAGER_DIR', basename(dirname(__FILE__)) );

// enable CMS tab
//Object::add_extension('CMSPageEditController', 'ModuleManager');

// add CMS tab
CMSMenu::add_menu_item('ModuleManager', 'Module Manager', 'admin/module-manager/', 'ModuleManager');

// include css
LeftAndMain::require_css(MODULE_MANAGER_DIR . '/css/cms.css');

// add functionality to SiteTree to enable calls from templates to include module areas
Object::add_extension('SiteTree', 'ModuleDisplay');

// add functionality to SiteTree so we can choose which modules to show on each page
Object::add_extension('SiteTree', 'ModuleSelection');

