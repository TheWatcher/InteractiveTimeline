<?php
/**
 *
 *
 * @ingroup Extensions
 */

class InteractiveTimeline {

    /** Determine whether the specified argument is a valid boolean value.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validBoolean( $arg, &$valid ) {
        $arg = trim( $arg );

        if ( preg_match( "/^(true|false)$/i", $arg, $matches ) ) {
            $valid = true;
            return (strtolower($matches[0]) === 'true');
        }

        $valid = false;
        return NULL;
    }


    /** Determine whether the specified argument represents a valid css size.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validCSSSize( $arg, &$valid ) {
        $arg = trim( $arg );

        if ( preg_match( "/^(-?\d+(\.\d+)?(%|cm|mm|in|em|ex|pt|pc|px)?)$/i", $arg, $matches ) ) {
            $valid = true;
            return $matches[0];
        }

        $valid = false;
        return NULL;
    }


    /** Determine whether the specified argument is a valid date with optional time.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validDatetime( $arg, &$valid ) {
        $arg = trim( $arg );

        // Note that this is not a full IS8601 checker - it does not support periods,
        // ordinal dates, or decimals in the time section.
        // $matches[N]       1       2               3                             4                5               6           7       8              9
        //                   YYYY  - MM(restricted)- DD(restricted)           T ?  HH              :MM             :SS          Z +/-   HH            :MM
        if ( preg_match( "/^(\d{4})-?(0[1-9]|1[0-2])-?(0[1-9]|[12]\d|3[01])(?:[ T]([01]\d|2[0-4])(?::([0-5]\d))?(?::([0-5]\d))?(Z|[-+])(0\d|1[0-4])(?::?([0-5]\d))?)?$/", $arg, $matches)){
            $valid = true;
            $date = $matches[1] . "-" . $matches[2] . "-" . $matches[3] . "T" .             // Date must be specified in full
                ( isset( $matches[4] ) && $matches[4] !== '' ? $matches[4] : "00" ) . ":" . // Hour can be optional, force to 00 if not set
                ( isset( $matches[5] ) && $matches[5] !== '' ? $matches[5] : "00" ) . ":" . // Similarly for minutes
                ( isset( $matches[6] ) && $matches[6] !== '' ? $matches[6] : "00" );        // And seconds.

            // If a timezone is set, use it
            if ( isset( $matches[7] ) && isset( $matches[8] ) ) {
                $date .= $matches[7].$matches[8] . ":" .
                    ( isset( $matches[9] ) && $matches[4] !== '' ? $matches[9] : "00" ); // Minute part of time offset is optional

            // Otherwise default to UTC explicitly (otherwise the browser will use local time)
            } else {
                $date .= "Z";
            }

            return $date;
        }

        $valid = false;
        return NULL;
    }


    /** Determine whether the specified argument is a valid integer value.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validInteger( $arg, &$valid ) {
        $arg = trim( $arg );

        if ( preg_match( "/^\d+$/", $arg, $matches) ) {
            $valid = true;
            return intval( $matches[0] );
        }

        $valid = false;
        return NULL;
    }


    /** Determine whether the specified argument is a locale supported by
     *  the CHAP Timeline code. This expects timeline-locales.js to be loaded.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validLocale( $arg, &$valid ) {
        $arg = trim( $arg );

        if ( preg_match( "/^(ca(?:_ES)?|en(?:_US|_UK)?|nl(?:_NL|_BE)?|fi(?:_FI)?|fr(?:_FR|_BE|_CA)?|de(?:_DE|_CH)?|da(?:_DK)?|ru(?:_RU)?|es(?:_ES)?|tr(?:_TR)?)$/", $arg, $matches ) ) {
            $valid = true;
            return $matches[0];
        }

        $valid = false;
        return NULL;
    }


    /** Determine whether the specified argument is a valid CHAP Timeline
     *  style value. This only accepts the built-in styles and it does
     *  not support custom styles.
     *
     * @param arg       The value to check.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @return The validated value or NULL if validation failed
     */
    public static function validTimestyle( $arg, &$valid ) {
        $arg = trim( $arg );

        if ( preg_match( "/^(box|dot)$/i", $arg, $matches ) ) {
            $valid = true;
            return strtolower($matches[0]);
        }

        $valid = false;
        return NULL;
    }


    /** Validate the argument with the specified name, and store the validated
     *  result in the options array. If no value has been set for the named
     *  argument, or the value specified for the argument is not valid, this
     *  will not update the options array.
     *
     * @param options A reference to an array to store the validated options in.
     * @param args    A reference to an array containing the arguments supplied by the tag.
     * @param name    The name of the argument to validate. This may contain mixed case,
     *                even though MediaWiki will lowercase names in the args array, the
     *                options may require mixed case. The name supplied is converted to
     *                lowercase to look up the value in args, and used 'as is' when setting
     *                the value in options.
     * @param type    The type of validation to apply to the value. Supported values
     *                are 'boolean', 'csssize', 'datetime', 'integer', 'locale', and 'timestyle'.
     */
    public static function validateArgument( &$options, &$args, $name, $type) {
        // All mediawiki args are lowercase, but the options may be lowerCamelCase.
        $lcname = strtolower($name);

        // Only bother trying to do anything if the user has set the option
        if ( isset( $args[$lcname] ) ) {
            $value = NULL;

            // Use the appropriate validator to check the option
            switch ( $type ) {
                case "boolean"  : $value = self::validBoolean  ( $args[$lcname], $valid );
                    break;
                case "csssize"  : $value = self::validCSSSize  ( $args[$lcname], $valid );
                    break;
                case "datetime" : $value = self::validDatetime ( $args[$lcname], $valid );
                    break;
                case "integer"  : $value = self::validInteger  ( $args[$lcname], $valid );
                    break;
                case "locale"   : $value = self::validLocale   ( $args[$lcname], $valid );
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


    public static function buildOptions( &$args ) {

        // Establish any defaults that differ from timeline's own defaults
        $options = array(
            "selectable"     => 'false', // No point in making timeline entries selectable
            "timeChangeable" => 'false', // probably redundant as editable is false, but be sure.
        );

        // And now check any user-specified arguments
        self::validateArgument($options, $args, 'animate'        , 'boolean'  );
        self::validateArgument($options, $args, 'animateZoom'    , 'boolean'  );
        self::validateArgument($options, $args, 'axizOnTop'      , 'boolean'  );
        self::validateArgument($options, $args, 'end'            , 'datetime' );
        self::validateArgument($options, $args, 'eventMargin'    , 'integer'  );
        self::validateArgument($options, $args, 'eventMarginAxis', 'integer'  );
        self::validateArgument($options, $args, 'groupsOnRight'  , 'boolean'  );
        self::validateArgument($options, $args, 'groupsWidth'    , 'csssize'  );
        self::validateArgument($options, $args, 'groupMinheight' , 'integer'  );
        self::validateArgument($options, $args, 'height'         , 'csssize'  );
        self::validateArgument($options, $args, 'locale'         , 'locale'   );
        self::validateArgument($options, $args, 'max'            , 'datetime' );
        self::validateArgument($options, $args, 'min'            , 'datetime' );
        self::validateArgument($options, $args, 'minheight'      , 'integer'  );
        self::validateArgument($options, $args, 'moveable'       , 'boolean'  );
        self::validateArgument($options, $args, 'stackEvents'    , 'boolean'  );
        self::validateArgument($options, $args, 'start'          , 'datetime' );
        self::validateArgument($options, $args, 'style'          , 'timestyle');
        self::validateArgument($options, $args, 'showCurrentTime', 'boolean'  );
        self::validateArgument($options, $args, 'showMajorLabels', 'boolean'  );
        self::validateArgument($options, $args, 'showMinorLabels', 'boolean'  );
        self::validateArgument($options, $args, 'showNavigation' , 'boolean'  );
        self::validateArgument($options, $args, 'width'          , 'csssize'  );
        self::validateArgument($options, $args, 'zoomable'       , 'boolean'  );
        self::validateArgument($options, $args, 'zoomMax'        , 'integer'  );
        self::validateArgument($options, $args, 'zoomMin'        , 'integer'  );

    }




    public static function parserHook( $input, $args = array(), $parser, $frame ) {
        global $wgOut;

		static $tlNumber = 0;
		$elemID = 'itimeline-' . ++$tlNumber;


        $parserOutput = $parser -> getOutput();
        $parserOutput -> addJSConfigVars($elemID, FormatJson::encode($options));

        return Html::rawelement('div', array('id' => $elemID, 'class' => 'itimeline'));
    }

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
        $out->addModules( 'ext.InteractiveTimeline.loader' );
        $out->addModules( 'ext.InteractiveTimeline.timeline' );

		// Always return true, indicating that parser initialization should
		// continue normally.
		return true;
	}

    public static function onParserFirstCallInit( &$parser ) {
        // Adds the <itimeline>...</itimeline> tag to the parser.
        $parser -> setHook('itimeline', 'InteractiveTimeline::parserHook');

        return true;
    }
}