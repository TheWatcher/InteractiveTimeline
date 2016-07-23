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
 * @copyright Copyright © 2014-2016 Chris Page
 * @license GNU General Public Licence 2.0 or later
 */

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'InteractiveTimeline' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['InteractiveTimeline'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['InteractiveTimelineAlias'] = __DIR__ . '/InteractiveTimeline.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for InteractiveTimeline extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the InteractiveTimeline extension requires MediaWiki 1.25+' );
}
