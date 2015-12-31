<?php
/**
 * @package modulemanager
 */
class Module extends DataObject
{
    
    // set object parameters
    public static $db = array(
        'Title'               => 'Varchar(128)',
        'Alias'            => 'Text'
    );
    
    // set module names
    public static $singular_name = 'Module';
    public static $plural_name = 'Modules';
    public static $description = 'Standard Module';
    
    public static $has_one = array(
        'Position' => 'ModulePosition'
    );
    
    public static $many_many = array(
        'Pages' => 'SiteTree'
    );
    
    public static $summary_fields = array(
        'ModuleName' => 'Type',
        'Title' => 'Title',
        'Position.Title' => 'Position',
        'Pages.Count' => 'Page usage'
    );
    
    // returns name of this module type (uses static $singular_name)
    public function ModuleName()
    {
        $object = new ReflectionClass($this->ClassName);
        $properties = $object->getStaticProperties();
        return $properties['singular_name'];
    }
    
    // returns name of this module type (uses static $description)
    public function ModuleDescription()
    {
        $object = new ReflectionClass($this->ClassName);
        $properties = $object->getStaticProperties();
        return $properties['description'];
    }
    
    public static $searchable_fields = array(
        'Title',
        'PositionID'
    );
   
    // create cms fields
    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root'));
        
        // required information
        $fields->addFieldToTab('Root.Main', new HiddenField('ModuleID', 'ModuleID', $this->ID));
        $fields->addFieldToTab('Root.Main', new ReadonlyField('Type', 'Module Type', $this->ModuleName()));
        $fields->addFieldToTab('Root.Main', new TextField('Title', 'Title'));
        $fields->addFieldToTab('Root.Main', new TextField('Alias', 'Alias (unique identifier)'));
        $fields->addFieldToTab('Root.Main', new DropdownField(
                'PositionID',
                'Position',
                $this->GetModulePositions()
            ));
        
        // what pages is this module active on
        $gridFieldConfig = GridFieldConfig_RelationEditor::create();
        $gridField = new GridField("Pages", "Pages", $this->Pages(), $gridFieldConfig);
        $fields->addFieldToTab("Root.Pages", $gridField);
        
        return $fields;
    }
    
    // return list of possible module positions for cms dropdown field
    public function GetModulePositions()
    {
        if ($Positions = DataObject::get('ModulePosition')) {
            
            // construct container
            $map = array();
            
            // loop each position and inject into map
            foreach ($Positions as $Position) {
                $map[$Position->ID] = $Position->Title .' ('.$Position->Alias.')';
            }
            
            return $map;
        } else {
            return array('You need to create a Module Position first');
        }
    }
    
    // return list of possible module positions for cms dropdown field
    public function ModulePositionName()
    {
        $position = ModulePosition::get()->byID($this->ModulePosition);
        return $position->Title;
    }
    
    // produce the html markup using templates, for the holder template
    public function ModuleLayout()
    {
        
        // try rendering with this module's own template
        $output = $this->renderWith($this->ClassName);
        
        // no custom template, so use base template (Model.ss)
        //if( !$output )
            //$output = $this->renderWith('Module');
            // TODO: Make this work. To make it work we actually need to properly check if we have a custom template for this class.

        return $output;
    }
    
    // convert string into url-friendly string
    public function URLFriendly($string)
    {
    
        // replace non letter or digits by -
        $string = preg_replace('~[^\\pL\d]+~u', '-', $string);
        $string = trim($string, '-');

        // transliterate
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // lowercase
        $string = strtolower($string);

        // remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        if (empty($string)) {
            return 'n-a';
        }

        return $string;
    }
    
    // before saving, check alias
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        
        // convert name to lowercase, dashed
        $newAlias = $this->URLFriendly($this->Title);
        
        // get positions that already have this alias
        $moduleAliasesThatMatch = ModulePosition::get()->Filter('Alias', $newAlias)->First();
        
        // if we find a match
        if (isset($moduleAliasesThatMatch->ID)) {
            
            // create a new unique alias (based on ID)
            $this->Alias = $newAlias .'-'. $this->ID;
        
        // no match, meaning we're safe to use this as a unique alias
        } else {
            $this->Alias = $newAlias;
        }
    }
}
