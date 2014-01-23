<?php
class ModuleManagerAddController extends ModuleManagerEditController {

	static $url_segment = 'module-manager/add';
	static $url_rule = '/$Action/$ID/$OtherID';
	static $url_priority = 42;
	static $menu_title = 'Add module';

	static $allowed_actions = array(
		'AddForm',
		'doAdd',
		'doCancel'
	);
	
	// the form to select the module type that we want to create
	function AddForm() {
		
		$moduleTypes = array();
		foreach($this->ModuleTypes() as $type) {
			$html = sprintf('<strong class="title">&nbsp;&nbsp;%s</strong><span class="description">%s</span>',
				$type['Name'],
				$type['Description'],
				$type['ClassName']
			);
			$moduleTypes[$type['ClassName']] = $html;
		}

		$numericLabelTmpl = '<span class="step-label"><span class="flyout">%d</span><span class="arrow"></span><span class="title">%s</span></span>';

		$topTitle = _t('CMSPageAddController.ParentMode_top', 'Top level');
		$childTitle = _t('CMSPageAddController.ParentMode_child', 'Under another page');

		$fields = new FieldList(
			$typeField = new OptionsetField(
				"ModuleType", 
				sprintf($numericLabelTmpl, 1, 'Choose module type'), 
				$moduleTypes, 
				'Page'
			)
		);
		
		$actions = new FieldList(
			FormAction::create("doAdd", _t('CMSMain.Create',"Create"))
				->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
				->setUseButtonTag(true),
			FormAction::create("doCancel", _t('CMSMain.Cancel',"Cancel"))
				->addExtraClass('ss-ui-action-destructive ss-ui-action-cancel')
				->setUseButtonTag(true)
		);
		
		$this->extend('updatePageOptions', $fields);
		
		$form = CMSForm::create( 
			$this, "AddForm", $fields, $actions
		)->setHTMLID('Form_AddForm');
		$form->setResponseNegotiator($this->getResponseNegotiator());
		$form->addExtraClass('cms-add-form stacked cms-content center cms-edit-form ' . $this->BaseCSSClasses());
		$form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

		return $form;
	}

	public function doAdd($data, $form) {
		
		// figure out what type of module
		$className = isset($data['ModuleType']) ? $data['ModuleType'] : "Module";
		
		// build a new module of specified class
		$newModule = new $className;
		$newModule->Title		= 'New Module';
		$newModule->Description	= 'Enter your description here';
		$newModule->write();
		
		// set a success message
		Session::set("FormInfo.Form_EditForm.formError.message", 'Successfully created module');
		Session::set("FormInfo.Form_EditForm.formError.type", 'good');
		
		return $this->redirect(Controller::join_links(singleton('ModuleManagerEditController')->Link('show'), $newModule->ID));
	}

	public function doCancel($data, $form) {
		return $this->redirect(singleton('ModuleManagerController')->Link());
	}

}
