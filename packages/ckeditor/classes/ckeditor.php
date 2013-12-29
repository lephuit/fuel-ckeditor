<?php
/**
 * CKEditor Package
 *
 * Originally derived from the CKEditor package found here: https://github.com/alwarren/fuelphp.CKEditor_package
 * This version is refactored and updated for CKEditor 4.3.1 and FuelPHP 1.7.1.
 *
 * @package     FuelPHP
 * @subpackage  CKEditor
 * @author      Shea Lewis <shea@efellemedia.com>
 */

namespace CKEditor;

class CKEditor
{
	/**
	 * @var  string  The version of CKEditor
	 */
	const version = '3.6.2';

	/**
	 * @var  string  A constant string unique for each release of CKEditor
	 */
	const timestamp = 'B8DJ5M3';

	/**
	 * @var  string  URL to the CKEditor installation directory (absolute or relative to document root)
	 *
	 * If not set, CKEditor will try and guess its path.
	 */
	public static $base_path;

	/**
	 * @var  array  An array that holds the global CKEditor configuration
	 */
	public static $config = array();

	/**
	 * @var  bool  A boolean variable indicating whether CKEditor has been initialized or not.
	 *
	 * Set to true only if you have already included <<script>> tag loading ckeditor.js
	 * on your page.
	 */
	public static $initialized = false;

	/**
	 * @var  bool  Boolean variable indicating whether created code should be printed out or returned by a function
	 */
	public static $return_output = false;

	/**
	 * @var  array  An array with textarea attributes
	 *
	 * When CKEditor is created with the editor() method, an HTML <<textarea>> element is created.
	 * It will be displayed to anyone with JavaScript disabled or with an incompatible browser.
	 */
	public static $textarea_attributes = array(
		'rows' => 8,
		'cols' => 60,
	);

	/**
	 * @var  string  A string indicating the creation date of CKEditor
	 *
	 * Do not change unless you want to force browsers to not use a previous cached version of CKEditor.
	 */
	public static $timestamp = 'B8DJ5M3';

	/**
	 * @var  array  An array that holds event listeners
	 */
	private static $events = array();

	/**
	 * @var  array  An array that holds global event listeners
	 */
	private static $global_events = array();


	/**
	 * Main constructor
	 *
	 * @param  string  $base_path  URL to the CKEditor installation directory.
	 */
	final private function __construct($base_path = null)
	{
		if ( ! empty($base_path))
		{
			static::$base_path = $base_path;
		}
	}

	/**
	 * Initializes CKEditor only once
	 *
	 * @return  string
	 */
	private static function initialize()
	{
		static $init_complete;
		$output = '';

		if ( ! empty($init_complete))
		{
			return '';
		}

		$arguments = '';
		$ckeditor_path = static::ckeditor_path();

		if ( ! empty(static::$timestamp) and static::$timestamp != "%"."%TIMESTAMP%")
		{
			$arguments = '?t='.static::$timestamp;
		}

		// Skip relative paths
		if (strpos($ckeditor_path, '..') !== 0)
		{
			$output .= static::script('window.CKEDITOR_BASEPATH="'.$ckeditor_path.'";');
		}

		$output .= '<script type="text/javascript" src="'.$ckeditor_path.'ckeditor.js'.$arguments.'"></script>'."\n";

		$extra_code = '';

		if (static::$timestamp != self::$timestamp)
		{
			$extra_code .= ($extra_code ? "\n" : '').'CKEDITOR.timestamp = "'.static::$timestamp.'";';
		}

		if ($extra_code)
		{
			$output .= static::script($extra_code);
		}

		$init_complete = static::$initialized = true;

		return $output;
	}

	/**
	 * Creates a new CKEditor instance
	 *
	 * In incompatible browsers, CKEditor will downgrade to a plain HTML <<textarea>> element.
	 *
	 * @param  string  $name    Name of the CKEditor instance (this will also be the "name" attribute of the <<textarea>> element)
	 * @param  string  $value   Initial value (optional)
	 * @param  array   $config  The specific configuration options to apply to this editor instance (optional)
	 * @param  array   $events  Event liseners for this editor instance (optional)
	 */
	public static function editor($name = 'editor', $value = '', $config = array(), $events = array())
	{
		$attributes = '';

		foreach (static::$textarea_attributes as $key => $val)
		{
			$attributes .= ' '.$key.'="'.str_replace('"', '&quot;', $val).'"';
		}

		$output = '<textarea name="'.$name.'"'.$attributes.'>'.htmlspecialchars($value)."</textarea>\n";

		if ( ! static::$initialized)
		{
			$output .= static::initialize();
		}

		$_config  = static::config_settings($config, $events);
		$js       = static::return_global_events();

		if ( ! empty($_config))
		{
			$js .= 'CKEDITOR.replace("'.$name.'", '.static::encode_js($_config).');';
		}
		else
		{
			$js .= 'CKEDITOR.replace("'.$name.'");';
		}

		$output .= static::script($js);

		if ( ! static::$return_output)
		{
			print $output;
			$output = '';
		}

		return $output;
	}

	/**
	 * Replaces a <<textarea>> element with a CKEditor instance.
	 *
	 * @param  string  $id      The ID or name of the <<textarea>> element
	 * @param  array   $config  The specific configuration options to apply to this editor instance (optional)
	 * @param  array   $events  Event liseners for this editor instance (optional)
	 */
	public static function replace($id, $config = array(), $events = array())
	{
		$output = '';

		if ( ! static::$initialized)
		{
			$output .= static::initialize();
		}

		$_config  = static::config_settings($config, $events);
		$js       = static::return_global_events();

		if ( ! empty($_config))
		{
			$js .= 'CKEDITOR.replace("'.$id.'", '.static::encode_js($_config).');';
		}
		else
		{
			$js .= 'CKEDITOR.replace("'.$id.'");';
		}

		$output .= static::script($js);

		if ( ! static::$return_output)
		{
			print $output;
			$output = '';
		}

		return $output;
	}

	/**
	 * Replaces all <<textarea>> elements available in the document with editor instances
	 *
	 * @param  string  $class_name  If set, replace all <<textarea>> elements with class $class_name in the page
	 */
	public static function replace_all($class_name = null)
	{
		$output = '';

		if ( ! static::$initialized)
		{
			$output .= static::initialize();
		}

		$_config  = static::config_settings();
		$js       = static::return_global_events();

		if (empty($_config))
		{
			if (empty($class_name))
			{
				$js .= 'CKEDITOR.replaceAll();';
			}
			else
			{
				$js .= 'CKEDITOR.replaceAll("'.$class_name.'");';
			}
		}
		else
		{
			$class_detection  = '';
			$js              .= "CKEDITOR.replaceAll(function(textarea, config) {\n";
			
			if ( ! empty($class_name))
			{
				$js .= "	var classRegex = new RegExp('(?:^| )' + '".$class_name."' | '(?:$| )');\n";
				$js .= "	if (!classRegex.test(textarea.className))\n";
				$js .= "		return false;\n";
			}

			$js .= "	CKEDITOR.tools.extend(config, ".static::encode_js($_config).", true);";
			$js .= "} );";
		}

		$output .= static::script($js);

		if ( ! static::$return_output)
		{
			print $output;
			$output = '';
		}

		return $output;
	}

	/**
	 * Adds an event listener
	 *
	 * Events are fired by CKEditor in various situations
	 *
	 * @param  string  $event  Event name
	 * @param  string  $js     Javascript anonymous function or function name
	 */
	public static function add_event_handler($event, $js)
	{
		if ( ! isset(static::$events[$event]))
		{
			static::$events[$event] = array();
		}

		// Avoid duplicates
		if ( ! in_array($js, static::$events[$event]))
		{
			static::$events[$event][] = $js;
		}
	}

	/**
	 * Clear registered event handlers
	 *
	 * This function will have no effect on already created editor instances
	 *
	 * @param  string  $event  Event name, if not set all event handlers will be removed (optional)
	 */
	public static function clear_event_handlers($event = null)
	{
		if ( ! empty($event))
		{
			static::$events[$event] = array();
		}
		else
		{
			static::$events = array();
		}
	}

	/**
	 * Adds a global event listener
	 *
	 * Events are fired by CKEditor in various situations
	 *
	 * @param  string  $event  Global event name
	 * @param  string  $js     Javascript anonymous function or function name
	 */
	public static function add_global_event_handler($event, $js)
	{
		if ( ! isset(static::$global_events[$event]))
		{
			static::$global_events[$event] = array();
		}

		// Avoid duplicates
		if ( ! in_array($js, static::$global_events[$event]))
		{
			static::$global_events[$event][] = $js;
		}
	}

	/**
	 * Clear registered global event handlers
	 *
	 * This function will have no effect if the global event handler has already been printed or returned
	 *
	 * @param  string  $event  Global event name, if not set all global event handlers will be removed (optional)
	 */
	public static function clear_global_event_handlers($event = null)
	{
		if ( ! empty($event))
		{
			static::$global_events[$event] = array();
		}
		else
		{
			static::$global_events = array();
		}
	}

	/**
	 * Returns global event handlers
	 */
	private static function return_global_events()
	{
		static $returned_events;
		$output = '';

		if ( ! isset($returned_events))
		{
			$returned_events = array();
		}

		if ( ! empty(static::$global_events))
		{
			foreach (static::$global_events as $event_name => $handlers)
			{
				foreach ($handlers as $handler => $code)
				{
					if ( ! isset($returned_events[$event_name]))
					{
						$returned_events[$event_name] - array();
					}

					// Return only new events
					if ( ! in_array($code, $returned_events[$event_name]))
					{
						$output .= ($code ? "\n" : "")."CKEDITOR.on('".$event_name."', $code);";

						$returned_events[$event_name][] = $code;
					}
				}
			}
		}

		return $output;
	}

	/**
	 * Returns the configuration array
	 *
	 * Global and instance specific settings are merged into one array
	 *
	 * @param  array  $config  The specific configuration settings to apply to editor instance(s)
	 * @param  array  $events  Event listeners for editor instance(s)
	 */
	private static function config_settings($config = array(), $events = array())
	{
		$_config = static::$config;
		$_events = static::$events;

		if (is_array($config) and ! empty($config))
		{
			$_config = array_merge($_config, $config);
		}

		if (is_array($events) and ! empty($events))
		{
			foreach ($events as $event_name => $code)
			{
				if ( ! isset($_events[$event_name]))
				{
					$_events[$event_name] = array();
				}

				if ( ! in_array($code, $_events[$event_name]))
				{
					$_events[$event_name][] = $code;
				}
			}
		}

		if ( ! empty($_events))
		{
			foreach ($_events as $event_name => $handlers)
			{
				if (empty($handlers))
				{
					continue;
				}
				else if (count($handlers) == 1)
				{
					$_config['on'][$event_name] = '@@'.$handlers[0];
				}
				else
				{
					$_config['on'][$event_name] = '@@function (ev){';

					foreach ($handlers as $handler => $code)
					{
						$_config['on'][$event_name] .= '('.$code.')(ev);';
					}

					$_config['on'][$event_name] .= '}';
				}
			}
		}

		return $_config;
	}

	/**
	 * Returns path to ckeditor.js
	 *
	 * @return  string
	 */
	private static function ckeditor_path()
	{
		if ( ! empty(static::$base_path))
		{
			return static::$base_path;
		}

		/**
		 * The absolute pathname of the currently executing script.
		 * Note: If a script is executed with the CLI, as a relative path such as file.php or ../file.php,
		 * $_SERVER['SCRIPT_FILENAME'] will contain the relative path specified by the user.
		 */
		if (isset($_SERVER['SCRIPT_FILENAME']))
		{
			$real_path = dirname($_SERVER['SCRIPT_FILENAME']);
		}
		else
		{
			$real_path = realpath('./');
		}

		/**
		 * The filename of the currently executing script, relative to the document root.
		 * For instance, $_SERVER['PHP_SELF'] in a script at the address http://example.com/test.php/foo.bar
		 * would be /test.php/foo.bar.
		 */
		$self_path = dirname($_SERVER['PHP_SELF']);
		$file = str_replace("\\", "/", __FILE__);

		if ( ! $self_path or ! $real_path or ! $file)
		{
			return "/ckeditor/";
		}

		$document_root = substr($real_path, 0, strlen($real_path) - strlen($self_path));
		$file_url = substr($file, strlen($document_root));
		$ckeditor_url = str_replace('ckeditor_php5.php', '', $file_url);

		return $ckeditor_url;
	}

	/**
	 * Prints JavaScript code
	 *
	 * @param   string  $js      JavaScript code to be printed
	 * @return  string  $output  Formatted JavaScript
	 */
	private static function script($js)
	{
		$output = "<script type=\"text/javascript\">";
		$output .= "//<!CDATA[\n";
		$output .= $js;
		$output .= "\n//]]>";
		$output .= "</script>\n";

		return $output;
	}

	/**
	 * This little function provides basic JSON support
	 *
	 * @param   mixed  $val
	 * @return  string
	 */
	private static function encode_js($value)
	{
		switch (gettype($value))
		{
			case 'null':
				return 'null';
			break;

			case 'bool':
				return $value ? 'true' : 'false';
			break;

			case 'int':
				return $value;
			break;

			case 'float':
				return str_replace(',', '.', $value);
			break;

			case 'array':
			case 'object':
				if (is_array($value) and (array_keys($value) === range(0, count($value) - 1)))
				{
					// Recursive looping FTW
					return '['.implode(',', array_map(array(self, 'encode_js'), $value)).']';
				}

				$temp = array();

				foreach ($value as $key => $val)
				{
					$temp[] = static::encode_js("{$key}").':'.static::encode_js($val);
				}

				return '{'.implode(',', $temp).'}';
			break;

			default:
				if (strpos($value, '@@') === 0)
				{
					return substr($value, 2);
				}

				if (strtoupper(substr($value, 0, 9)) == 'CKEDITOR.')
				{
					return $value;
				}

				return '"'.str_replace(array("\\", "/", "\n", "\t", "\r", "\x08", "\x0c", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'), $value).'"';
		}
	}
}