# Module Manager

## Description

Manage site-wide modules (aka widgets) and select the pages on which they are to appear. This allows you to repurpose content across your website, and build easily modular content elements.


## Dependencies

* SilverStripe 4


## Installation

1. `composer require plasticstudio/ModuleManager`
2. Run /dev/build?flush=1
3. Setup your Module Positions. There is an initial `after_content` area setup to get you started.
4. Insert your Module Positions in your template (ie `$ModuleArea(after_content)`)


## Usage

### Create a module area
1. Edit your `app/_config/config.yml` file to add any additional module areas. Use the following format:
  ```
  PlasticStudio\ModuleManager\ModuleManager:
    positions:
      {ALIAS}: "{NAME}"
  ```

2. In your template, use the code `$ModulePosition(ALIAS)` where ALIAS is your position's alias string.
3. Run dev/build (`/dev/build?flush=all`)

### Create a module instance
1. Within the *Module Manager* admin, create a new `Module` object. The *type* dropdown will show the list of available module types.
2. Assign your new `Module` object to one of the positions you configured in `config.yml`.

### Build a custom module type
1. Create a new DataObject file `app/src/Modules/MyModule.php`:
  ```
  <?php
  class MyModule extends Module {
	
	// set module names
	private static $singular_name = 'My Module';
	private static $plural_name = 'My Modules';
	private static $description = 'This is my great custom module';
   
	// your custom fields
	static $db = array(
        'MyField' => 'Varchar(255)'
    );
   
	// create cms fields
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', TextField::create('MyField', 'My field'));
		return $fields;
	}	
  }
  ```
  
2. Create your template file `app/templates/Modules/MyModule.ss`:
  ```
    <div class="module module_my-module">
		<h3>$Title</h3>
		<div class="module-content">
	        $MyField
		</div>
	</div>
  ```
  
3. Perform a build and flush (`/dev/build?flush=all`)
4. Now you can create your custom module type

### Modules inheritance
To avoid having to set a module on each page within a section, you can set your pages to inherit it's parent page's modules.

1. Open your page, and navigate to the *Modules* tab
2. Check *Inherit Modules*  and Save your page.
3. You can apply this inheritance further up the page hierarchy if required.
 
