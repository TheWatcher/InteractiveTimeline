<?php
if ( ! defined( 'MEDIAWIKI' ) )
		die();
/**
 * A parser extension to MediaWiki that adds the <itimeline> tag to
 * allow interactive timelines to be displayed via the CHAP Timeline
 * library developed by Almende B.V.
 *
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Extensions
 * @author Chris Page <chris@starforge.co.uk>
 * @copyright Copyright © 2014 Chris Page
 * @license GNU General Public Licence 2.0 or later
 */

// Set up the extension Special:Version information
$wgExtensionCredits['parserhook'][] = array(
		'path'           => __FILE__,
		'name'           => 'InteractiveTimeline',
		'version'        => '0.1.0',
		'url'            => '',
		'descriptionmsg' => 'interactivetimeline-desc',
		'author'         => array( 'Chris Page' )
);

// Register files
$wgAutoloadClasses['InteractiveTimeline'] = __DIR__ . '/InteractiveTimeline.body.php';
$wgMessagesDirs['InteractiveTimeline'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['InteractiveTimeline'] = __DIR__ . '/InteractiveTimeline.i18n.php';

// Register hooks
$wgHooks['ParserFirstCallInit'][] = 'InteractiveTimeline::onParserFirstCallInit';
$wgHooks['BeforePageDisplay'][] = 'InteractiveTimeline::onBeforePageDisplay';

// Register modules

/* If using chap-links-library as a git submodule, use the following */
$chapResourceTemplate = array(
		'localBasePath' => __DIR__.'/chap-links-library/js/src/timeline',
		'remoteExtPath' => 'InteractiveTimeline/chap-links-library/js/src/timeline',
);

/* If using just the timeline files, use the following */
/* $chapResourceTemplate = array(
		'localBasePath' => __DIR__.'/timeline',
		'remoteExtPath' => 'InteractiveTimeline/timeline',
); */

$wgResourceModules['ext.InteractiveTimeline.timeline'] = $chapResourceTemplate + array(
		'scripts'  => array (
				'timeline-min.js',
				'timeline-locales.js'
		),
		'styles'   => 'timeline.css',
		'position' => 'top',
);

$wgResourceModules['ext.InteractiveTimeline.loader'] = array(
		'localBasePath' => __DIR__.'/modules',
		'remoteExtPath' => 'InteractiveTimeline/modules',
		'scripts'       => 'ext.interactivetimeline.js',
		'styles'        => 'ext.interactivetimeline.css',
		'position'      => 'top',
);
