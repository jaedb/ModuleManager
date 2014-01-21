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

	/**
	 * @return Form
	 */
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
				"PageType", 
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
		$className = isset($data['PageType']) ? $data['PageType'] : "Page";
		$parentID = isset($data['ParentID']) ? (int)$data['ParentID'] : 0;

		$suffix = isset($data['Suffix']) ? "-" . $data['Suffix'] : null;

		if(!$parentID && isset($data['Parent'])) {
			$page = SiteTree::get_by_link($data['Parent']);
			if($page) $parentID = $page->ID;
		}

		if(is_numeric($parentID) && $parentID > 0) $parentObj = DataObject::get_by_id("SiteTree", $parentID);
		else $parentObj = null;
		
		if(!$parentObj || !$parentObj->ID) $parentID = 0;

		if($parentObj) {
			if(!$parentObj->canAddChildren()) return Security::permissionFailure($this);
			if(!singleton($className)->canCreate()) return Security::permissionFailure($this);
		} else {
			if(!SiteConfig::current_site_config()->canCreateTopLevel())
				return Security::permissionFailure($this);
		}

		$record = $this->getNewItem("new-$className-$parentID".$suffix, false);
		if(class_exists('Translatable') && $record->hasExtension('Translatable') && isset($data['Locale'])) {
			$record->Locale = $data['Locale'];
		}

		try {
			$record->write();
		} catch(ValidationException $ex) {
			$form->sessionMessage($ex->getResult()->message(), 'bad');
			return $this->getResponseNegotiator()->respond($this->request);
		}

		$editController = singleton('CMSPageEditController');
		$editController->setCurrentPageID($record->ID);

		Session::set(
			"FormInfo.Form_EditForm.formError.message", 
			_t('CMSMain.PageAdded', 'Successfully created page')
		);
		Session::set("FormInfo.Form_EditForm.formError.type", 'good');
		
		return $this->redirect(Controller::join_links(singleton('CMSPageEditController')->Link('show'), $record->ID));
	}

	public function doCancel($data, $form) {
		return $this->redirect(singleton('ModuleManagerController')->Link());
	}

}
