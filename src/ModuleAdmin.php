<?php

namespace Jaedb\ModuleManager;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;

class Admin extends ModelAdmin {

    private static $url_segment = 'modules';
    private static $menu_title = 'Modules';
    private static $menu_icon_class = 'font-icon-edit-list';

    private static $managed_models = array(
		Module::class
    );


    public function getEditForm($id = null, $fields = null){
        $form = parent::getEditForm($id, $fields);

        $gridFieldName = $this->sanitiseClassName(Module::class);
        $gridField = $form->Fields()->fieldByName($gridFieldName);

        // Swap out our "Add" button for the multiclass Add
        $gridField->getConfig()->addComponent(new GridFieldAddNewMultiClass());
		$gridField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);

        return $form;
    }
}
