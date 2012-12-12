<?php
/**
 *
 *
 * @package widgetmanager
 */
class ModuleManager extends ModelAdmin {

	public static $menu_title    = 'Module Manager';
	public static $menu_priority = -1;
	public static $url_segment   = 'module-manager';

	public static $managed_models  = array(
				'Module', 
				'ModulePosition');
	public static $model_importers = array();

}
