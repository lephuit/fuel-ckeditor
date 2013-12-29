CKEditor Package
================

A FuelPHP package for CKEditor.

Installation
------------
1. Download CKEditor [here](http://ckeditor.com/download).
2. Copy the `ckeditor` folder from the download archive into your public javascript folder.
3. Download the FuelPHP CKEditor package [here](https://github.com/kaisama/fuelphp.CKEditor_package/zipball/master).
3. Drop the CKEditor package into `fuel/packages`.
4. Open `fuel/packages/ckeditor/config/config.php` in your favorite editor.
5. Set the `basepath` option to the web path to your CKEditor javascript. The default path is `public/assets/js/ckeditor`.
6. Set the `toolbar` option to your preferred set of editor buttons.
7. Add any additional editor options.

Usage
-----
**Load the class:**

```php
if ( ! Package::loaded('CKEditor'))
{
	Package::load('CKEditor');
}
```

(Optionally, you can autoload the class. See the FuelPHP documentation for more information)

**Render the editor:**

```php
echo CKEditor::editor();
```