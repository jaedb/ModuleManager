<?php

namespace Jaedb\ModuleManager;

use SilverStripe\Admin\ModelAdmin;

class Admin extends ModelAdmin {

    private static $url_segment = 'modules';
    private static $menu_title = 'Modules';
    private static $menu_icon_class = 'font-icon-edit-list';

    private static $managed_models = array(
		Module::class
    );
}
