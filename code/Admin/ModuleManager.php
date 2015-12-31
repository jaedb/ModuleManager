<?php

class ModuleManager extends DataObject
{

    public function getCMSFields()
    {
        $fields = FieldList::create($tabSet = TabSet::create('Root'));
        return $fields;
    }
    
    public function setRequest($req)
    {
        return false;
    }
    
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $moduleManager = ModuleManager::get()->First();
        if (!$moduleManager) {
            self::make_module_manager();
            DB::alteration_message("Added default module manager", "created");
        }
    }
    
    public static function make_module_manager()
    {
        $moduleManager = ModuleManager::create();
        $moduleManager->write();
        return $moduleManager;
    }
    
    public static function CurrentModuleManager()
    {
        $current = ModuleManager::get()->First();
        return $current;
    }
    
    public function CMSEditLink()
    {
        return singleton('ModuleManagerController')->Link();
    }
}
