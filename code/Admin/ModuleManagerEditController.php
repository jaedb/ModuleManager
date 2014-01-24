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
/*
	static $allowed_actions = array(
		'show',
		'EditForm',
		'doEditForm',
		'doCancel'
	);
*/
	public function Breadcrumbs($unlinked = false) {
		$crumbs = parent::Breadcrumbs($unlinked);
		$crumbs[0]->Title = 'Module Manager';
		return $crumbs;
	}
/*
	public function getResponseNegotiator() {
		$negotiator = parent::getResponseNegotiator();
		$controller = $this;
		$negotiator->setCallback('ListViewForm', function() use(&$controller) {
			return $controller->ListViewForm()->forTemplate();
		});
		return $negotiator;
	}
	
	// pull the current module ID
	public function currentModule(){
		
		// get the ID from the URL		
		$params = $this->getRequest()->params();
		
		if( isset( $params['ID'] ) )
			return Convert::raw2sql($params['ID']);			
		
		return false;
	}
	
	// construct the edit form for this module
	public function EditForm($id = null, $fields = null) {
	
		$currentModuleID = $this->currentModule();
		
		// if no moduleID given, redirect back to top level
		if( !$currentModuleID )
			return $this->redirect(singleton('ModuleManagerController')->Link());
		
		// get the cmsfields from ModuleManager DataObject
		$module = Module::get()->byID($currentModuleID);
		
		// if no module exists with that ID
		if( !isset($module->ID) )
			return $this->redirect(singleton('ModuleManagerController')->Link());
			
		$fields = $module->getCMSFields();
		
		// actions		
		$actions = new FieldList(
			FormAction::create("doSave", _t('CMSMain.Save',"Save"))
				->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
				->setUseButtonTag(true),
			FormAction::create("doCancel", _t('CMSMain.Cancel',"Cancel"))
				->addExtraClass('ss-ui-action-destructive ss-ui-action-cancel')
				->setUseButtonTag(true)
		);
		
		$form = CMSForm::create($this, 'EditForm', $fields, $actions)->setHTMLID('Form_EditForm');
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->addExtraClass('cms-content center cms-edit-form');

		if($form->Fields()->hasTabset()) $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
		$form->setHTMLID('Form_EditForm');
		$form->loadDataFrom($module);
		$form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

		// Use <button> to allow full jQuery UI styling
		$actions = $actions->dataFields();
		if($actions) foreach($actions as $action) $action->setUseButtonTag(true);

		return $form;
	}

	public function doSave($data, $form) {
	
		// get the module
		$module = Module::get()->byID($data['ModuleID']);
		
		// if we can't find the module by ID
		if( !isset($module->ID) ){
		
			Session::set(
				"FormInfo.Form_EditForm.formError.message", 
				'Could not find module #'.$data['ModuleID']
			);
			Session::set("FormInfo.Form_EditForm.formError.type", 'bad');
		
			return $this->redirect(singleton('ModuleManagerController')->Link());
		}
		
		// save the changes
		$form->saveInto($module);
		$module->write();
		
		// set a success notification
		Session::set("FormInfo.Form_EditForm.formError.message", 'Successfully saved module');
		Session::set("FormInfo.Form_EditForm.formError.type", 'good');
		
		//return $this->redirect(Controller::join_links(singleton('ModuleManagerEditController'), $newModule->ID));
		return $this->redirect(singleton('ModuleManagerController')->Link());
	}

	public function doCancel($data, $form) {
		return $this->redirect(singleton('ModuleManagerController')->Link());
	}
*/
}
