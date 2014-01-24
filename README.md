Description
===========

Manage site-wide modules (widgets) and select the pages on which they are to appear.


Dependancies
============

* SilverStripe 3.1
* SilverStripe GridFieldExtensions module (`https://github.com/ajshort/silverstripe-gridfieldextensions`)

Installation
============

1. Clone this repository into your root folder (ie `public_html/module-manager`)
2. Run /dev/build?flush=1
3. Load your Module Positions
4. Insert your Module Positions in your template (ie `$ModuleArea(footer)`)


Usage
=====

Create your module areas from the Module Manager tab within the CMS and add your modules. To include the module area in a template simply write $ModuleArea(area) where area is the alias to your module area.