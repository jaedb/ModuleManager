# Description

Manage site-wide modules (aka widgets) and select the pages on which they are to appear. This allows you to repurpose content across your website, and build easily modular content elements.


# Dependencies

* SilverStripe 3.1
* SilverStripe GridFieldExtensions module (`https://github.com/ajshort/silverstripe-gridfieldextensions`)


# Installation

1. Clone this repository into your root folder (ie `public_html/module-manager`)
2. Run /dev/build?flush=1
3. Load your Module Positions
4. Insert your Module Positions in your template (ie `$ModuleArea(footer)`)


# Usage

### Create a module area
1. Within the *Module Manager* admin, create a new ModulePosition object. The `Alias` field will be automatically generated, or you can enter your custom alias name.
2. In your template, use the code `$ModulePosition(alias)` where alias is your position's alias string.
3. Flush your template cache (`?flush=all`)

### Create a module instance
1. Within the *Module Manager* admin, create a new `Module` object. The *type* dropdown will show the list of available module types.
2. Assign your new `Module` object to the `ModulePosition` object you created earlier.

### Build a custom module type
1. Create a new DataObject file `mysite/code/Modules/MyModule.php`:
  ```
  <?php
  class MyModule extends Module {
	
	// set module names
	private static $singular_name = 'My Module';
	private static $plural_name = 'My Modules';
	private static $description = 'This is my great custom module';
   
	// your custom fields
	static $db = array(
        'CustomField' => 'Text'
    );
   
	// create cms fields
	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', TextField::create('CustomField', 'My Custom Field'));
		return $fields;
	}	
  }
  ```
2. Create your template file `themes/mytheme/Modules/MyModule.ss`:
  ```
  <div class="module-item my-custom-module">
	<h3>$Title</h3>
	<div class="module-content">
		$Content
        $CustomField
	</div>
  </div>
  ```
3. Perform a build and flush (`/dev/build?flush=all`)
4. Now you can create your custom module type

### Modules inheritance
To avoid having to set a module on each page within a section, you can set your pages to inherit it's parent page's modules.'
1. Open your page, and navigate to the *Modules* tab
2. Check *Inherit Modules*  and Save your page.
3. You can apply this inheritance further up the page hierarchy if required.
 
