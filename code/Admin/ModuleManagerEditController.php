<?php

/**
 * @package cms
 */
class ModuleManagerEditController extends ModuleManagerController {

	static $url_segment = 'module-manager/edit';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $url_priority = 41;
	static $required_permission_codes = 'CMS_ACCESS_CMSMain';
	static $session_namespace = 'ModuleManager';

	public function Breadcrumbs($unlinked = false) {
		$crumbs = parent::Breadcrumbs($unlinked);
		$crumbs[0]->Title = _t('ModuleManagerController.MENUTITLE');
		return $crumbs;
	}

}
