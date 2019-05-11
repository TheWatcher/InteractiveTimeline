<?php
/**
 * This file is part of the InteractiveTimeline Extension to MediaWiki
 * https://www.mediawiki.org/wiki/Extension:InteractiveTimeline
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

class InteractiveTimeline {

	/**
	 * Determine whether the specified argument is a valid boolean value.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validBoolean( $arg, &$valid ) {
		$arg = trim( $arg );

		if ( preg_match( "/^(true|false)$/i", $arg, $matches ) ) {
			$valid = true;
			return (strtolower($matches[0]) === 'true');
		}

		$valid = false;
		return null;
	}


	/**
	 * Determine whether the specified argument represents a valid css size.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validCSSSize( $arg, &$valid ) {
		$arg = trim( $arg );

		if ( preg_match( "/^(-?\d+(\.\d+)?(%|cm|mm|in|em|ex|pt|pc|px)?)$/i", $arg, $matches ) ) {
			$valid = true;
			return $matches[0];
		}

		$valid = false;
		return null;
	}


	/**
	 * Determine whether the specified argument is a valid date with optional time.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validDatetime( $arg, &$valid ) {
		$arg = trim( $arg );

		// Note that this is not a full IS8601 checker - it does not support periods,
		// ordinal dates, or decimals in the time section. It also only supports hours
		// in the range 00-23 (ISO8601 allows 24) as *none* of the Date.parse() implementations
		// in any browser I've checked will parse '24:00:00' correctly.
		// $matches[N]       1         2                 3                         4                 5              6           7      8               9
		//                   YYYY  -  MM            -  DD(restricted)          T   HH               :MM            :SS          Z +/-  HH             :MM
		if ( preg_match( "/^(\d{4})-?(0[1-9]|1[0-2])-?(0[1-9]|[12]\d|3[01])(?:[ T]([01]\d|2[0-3])(?::([0-5]\d))?(?::([0-5]\d))?([-+Z])(0\d|1[0-4])?(?::?([0-5]\d))?)?$/", $arg, $matches)){
			$valid = true;

			// Date must be specified in full
			$date = $matches[1] . "-" . $matches[2] . "-" . $matches[3] . "T";

			// Hour, minute, and second are optional and should default to zero
			$date .= ( isset( $matches[4] ) && $matches[4] !== '' ? $matches[4] : "00" ) . ":" .
				( isset( $matches[5] ) && $matches[5] !== '' ? $matches[5] : "00" ) . ":" .
				( isset( $matches[6] ) && $matches[6] !== '' ? $matches[6] : "00" );

			// If a timezone is set, use it
			if ( isset( $matches[7] ) && ( $matches[7] === 'Z' || isset( $matches[8] ) ) ) {
				$date .= $matches[7];

				// If the time is not in UTC, and offset must be specified.
				if ( $matches[7] !== 'Z' ) {
					$date .= $matches[8];

					// Minute part of time offset is optional
					$date .= ":" . ( isset( $matches[9] ) && $matches[9] !== '' ? $matches[9] : "00" );
				}

			// Otherwise default to UTC explicitly (if not included the browser
			// will use local time)
			} else {
				$date .= "Z";
			}

			return $date;
		}

		$valid = false;
		return null;
	}


	/**
	 * Determine whether the specified argument is a valid integer value.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validInteger( $arg, &$valid ) {
		$arg = trim( $arg );

		if ( preg_match( "/^\d+$/", $arg, $matches) ) {
			$valid = true;
			return intval( $matches[0] );
		}

		$valid = false;
		return null;
	}


	/**
	 * Determine whether the specified argument is a locale supported by
	 * the CHAP Timeline code. This expects timeline-locales.js to be loaded.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validLocale( $arg, &$valid ) {
		$arg = trim( $arg );

		if ( preg_match( "/^(ca(?:_ES)?|en(?:_US|_UK)?|nl(?:_NL|_BE)?|fi(?:_FI)?|fr(?:_FR|_BE|_CA)?|de(?:_DE|_CH)?|da(?:_DK)?|ru(?:_RU)?|es(?:_ES)?|tr(?:_TR)?)$/", $arg, $matches ) ) {
			$valid = true;
			return $matches[0];
		}

		$valid = false;
		return null;
	}


	/**
	 * Determine whether the specified argument is a valid CHAP Timeline
	 * style value. This only accepts the built-in styles and it does
	 * not support custom styles.
	 *
	 * @param string $arg    The value to check.
	 * @param boolean $valid A reference to a variable that will be set to true if
	 *                       the argument is valid, false if it is not.
	 * @return string The validated value or null if validation failed
	 */
	public static function validTimestyle( $arg, &$valid ) {
		$arg = trim( $arg );

		if ( preg_match( "/^(box|dot)$/i", $arg, $matches ) ) {
			$valid = true;
			return strtolower( $matches[0] );
		}

		$valid = false;
		return null;
	}


	/**
	 * Validate the argument with the specified name, and store the validated
	 * result in the options array. If no value has been set for the named
	 * argument, or the value specified for the argument is not valid, this
	 * will not update the options array.
	 *
	 * @param array $options A reference to an array to store the validated
	 *                         options in.
	 * @param array $args     A reference to an array containing the arguments
	 *                         supplied by the tag.
	 * @param string $name     The name of the argument to validate. This may
	 *                         contain mixed case: even though MediaWiki will
	 *                         lowercase names in the args array, the options may
	 *                         require mixed case. The name supplied is converted
	 *                         to lowercase to look up the value in args, and
	 *                         used 'as is' when setting the value in options.
	 * @param string $type     The type of validation to apply to the value.
	 *                         Supported values are 'boolean', 'csssize',
	 *                         'datetime', 'integer', 'locale', and 'timestyle'.
	 */
	public static function validateArgument( &$options, &$args, $name, $type) {
		// All mediawiki args are lowercase, but the options may be lowerCamelCase.
		$lcname = strtolower( $name );

		// Only bother trying to do anything if the user has set the option
		if ( isset( $args[$lcname] ) ) {
			$value = null;

			// Use the appropriate validator to check the option
			switch ( $type ) {
				case "boolean": $value = self::validBoolean( $args[$lcname], $valid );
					break;
				case "csssize": $value = self::validCSSSize( $args[$lcname], $valid );
					break;
				case "datetime": $value = self::validDatetime( $args[$lcname], $valid );
					break;
				case "integer": $value = self::validInteger( $args[$lcname], $valid );
					break;
				case "locale": $value = self::validLocale( $args[$lcname], $valid );
					break;
				case "timestyle": $value = self::validTimestyle( $args[$lcname], $valid );
					break;
			}

			// If a valid value was obtained, store it.
			if ( isset( $value ) && $valid ) {
				$options[$name] = $value;
			}
		}
	}


	/**
	 * Construct the options to set for the timeline. This validates the arguments
	 * set in the itimeline tag and stores valid arguments in the options array.
	 *
	 * @param array $options A reference to an array to store the validated options.
	 * @param array $args    A reference to an array containing the arguments
	 *                       supplied by the tag.
	 */
	public static function buildTimelineOptions( &$options, &$args ) {

		// Establish any defaults that differ from timeline's own defaults
		$options['selectable']= false;      // No point in making timeline entries selectable

		// And now check any user-specified arguments
		self::validateArgument( $options, $args, 'end', 'datetime' );
		self::validateArgument( $options, $args, 'height', 'csssize' );
		self::validateArgument( $options, $args, 'locale', 'locale' );
		self::validateArgument( $options, $args, 'max', 'datetime' );
		self::validateArgument( $options, $args, 'min', 'datetime' );
		self::validateArgument( $options, $args, 'minHeight', 'integer' );
		self::validateArgument( $options, $args, 'moveable', 'boolean' );
		self::validateArgument( $options, $args, 'stack', 'boolean' );
		self::validateArgument( $options, $args, 'start', 'datetime' );
		self::validateArgument( $options, $args, 'style', 'timestyle' );
		self::validateArgument( $options, $args, 'showCurrentTime', 'boolean' );
		self::validateArgument( $options, $args, 'showMajorLabels', 'boolean' );
		self::validateArgument( $options, $args, 'showMinorLabels', 'boolean' );
		self::validateArgument( $options, $args, 'width', 'csssize' );
		self::validateArgument( $options, $args, 'zoomable', 'boolean' );
		self::validateArgument( $options, $args, 'zoomMax', 'integer' );
		self::validateArgument( $options, $args, 'zoomMin', 'integer' );
	}


	/**
	 * Given a timeline event definition line, ensure that it contains the required
	 * parts (start date and event description, or start, end, and description) and
	 * that the start and possibly end values are valid dates. This generates the
	 * divs that allow easier extraction of the event information in javascript.
	 *
	 * @param string $line The line containing the timeline event to validate.
	 * @return string The divs describing the event if the line is valid, null otherwise.
	 */
	public static function buildTimelineLine( $line ) {

		// Sections are delimited by |
		$parts = explode( "|", trim( $line ) );

		// Lines *must* consist of two or three parts: a date or interval, an optional group, and the text
		if ( count( $parts ) == 2 || count( $parts ) == 3 ) {

			// The first part might be an interval
			$dates = explode( "/", $parts[0]);

			// The first part must be a datetime string, or the whole line is rejected
			$value = self::validDatetime( $dates[0], $valid );
			if ( $valid ) {
				$output = Html::element( 'div', array( 'class' => 'itl-start' ), $value );

				// If there are two dates, the second is the end date
				if ( count( $dates ) == 2) {
					$value = self::validDatetime( $dates[1], $valid );
					if ( $valid ) {
						$output .= Html::element( 'div', array( 'class' => 'itl-end' ), $value );

					}
				}

				// Two parts implies date and text...
				if ( count( $parts ) == 2 ) {
					$body = $parts[1];

				// While three is date, group, and text
				} else {
					$body = $parts[2];
					$output .= Html::rawelement( 'div', array( 'class' => 'itl-group' ), $parts[1] );
				}
				$output .= Html::rawelement( 'div', array( 'class' => 'itl-body' ), $body );


				return $output;
			}
		}

		return null;
	}


	/**
	 * Validate the timeline events in the itimeline tag body, and produce a series
	 * of divs containing the data, one per event, for easier parsing in javascript.
	 *
	 * @param string $input   The content of the tag.
	 * @param array $args     The attributes of the tag.
	 * @param Parser $parser  Parser instance available to render wikitext into html,
	 *                        or parser methods.
	 * @param PPFrame $frame  Can be used to see what template arguments ({{{1}}})
	 *                        this hook was used with.
	 * @return string The itl-event list to use inside the itimeline tag.
	 */
	public static function buildTimelineEvents( $input, $parser, $frame ) {

		// First expand any templates/transclusions
		$body = $parser->recursiveTagParse( $input, $frame );

		// now split on lines
		$lines = explode( "\n", $body );

		$output = "";
		// Convert the lines to validated output
		foreach ( $lines as $line ) {
			$linedata = self::buildTimelineLine( $line );
			if ( isset( $linedata ) ) {
				$output .= Html::rawelement( 'div', array( 'class' => 'itl-event' ), $linedata ) . "\n";
			}
		}

		return $output;
	}


	/**
	 * Parser hook handler for <itimeline>
	 *
	 * @param string $input   The content of the tag.
	 * @param array $args     The attributes of the tag.
	 * @param Parser $parser  Parser instance available to render wikitext into html,
	 *                        or parser methods.
	 * @param PPFrame $frame  Can be used to see what template arguments ({{{1}}})
	 *                        this hook was used with.
	 * @return string HTML to insert in the page.
	 */
	public static function parserHook( $input, $args = array(), $parser, $frame ) {
		global $wgOut;

		static $tlNumber = 0;
		$elemID = 'itimeline-' . ++$tlNumber;

		$options = array();
		self::buildTimelineOptions( $options, $args );

		$timelinedata = self::buildTimelineEvents( $input, $parser, $frame );

		// Store the timeline setup
		$timelinedata .=  Html::rawelement( 'div', array( 'class' => 'itl-config' ), FormatJson::encode( $options ) );

		return Html::rawelement( 'div', array( 'id' => $elemID, 'class' => 'itimeline' ), $timelinedata );
	}


	/**
	 * Add the Interactive Timeline resource modules to the load queue of all pages.
	 *
	 * @param OutputPage $out Instance of OutputPage.
	 * @param Skin $skin The skin instance.
	 * @return boolean Always returns true.
	 */
	public static function onBeforePageDisplay( &$out, &$skin ) {
		global $wgITvisjsCDNcss, $wgITvisjsCDNjs;

		// Ensure that the required resource modules are loaded
		$out->addModules( 'ext.InteractiveTimeline.loader' );

		// Load vis.js from a CDN.
		$script = '
			<link rel="stylesheet" type="text/css" href="'.$wgITvisjsCDNcss.'">
			<script type="text/javascript" src="'.$wgITvisjsCDNjs.'"></script>
		';

		$out->addHeadItem("InteractiveTimeline CDN", $script);

		return true;
	}


	/**
	 * Register the <itimeline> tag with the Parser.
	 *
	 * @param $parser Parser instance of Parser
	 * @return boolean Always returns true
	 */
	public static function onParserFirstCallInit( &$parser ) {
		// Adds the <itimeline>...</itimeline> tag to the parser.
		$parser -> setHook( 'itimeline', 'InteractiveTimeline::parserHook' );

		return true;
	}
}
