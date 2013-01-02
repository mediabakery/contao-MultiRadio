<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package MultiRadio
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Mediabakery',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Widgets
	'Mediabakery\MultiRadio\MultiRadio' => 'system/modules/MultiRadio/widgets/MultiRadio.php',
));
