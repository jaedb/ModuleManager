<?php
/**
 *
 *
 * @package widgetmanager
 */
class ModuleManagerAdmin extends ModelAdmin {

	static $menu_title    = 'Module Manager';
	static $menu_priority = -1;
	static $url_segment   = 'module-manager';
	static $menu_icon = 'modulemanager/images/cms-icon.png';

	public static $managed_models  = array(
				'Module',
				'ModulePosition');
				
	public static $model_importers = array();

}
