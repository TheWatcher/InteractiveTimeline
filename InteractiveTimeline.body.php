<?php
/**
 *
 *
 * @ingroup Extensions
 */

class InteractiveTimeline {

    /** Determine whether the specified argument is an integer, and optionally
     *  whether it is positive and non-zero. Note that the negative and zero
     *  checks are not applied to the default.
     *
     * @param arg       The value to check.
     * @param default   The value to use if validation fails.
     * @param valid     A reference to a variable that will be set to true if
     *                  the argument is valid, false if it is not.
     * @param allowNeg  Allow negative numbers.
     * @param allowZero Allow the value to be zero.
     * @return The validated value or the default if validation failed
     */
    public static function intValidate( $arg, $default, &$valid, $allowNeg = false, $allowZero = false ) {
        $arg = trim( $arg );

        $regex = $allowNeg ? "/^-?\d+$/" : "/^\d+$/";
        if(preg_match($regex, $arg) && ($allowZero || $arg)) {
            $valid = true;
            return intval($arg);
        }

        $valid = false;
        return $default;
    }

    public static function parserHook( $input, $args = array(), $parser, $frame ) {
        global $wgOut;

		static $tlNumber = 0;
		$elemID = 'itimeline-' . ++$tlNumber;

        $options = array(
            "width"  => self::intValidate($args['width'], 100, $valid),
            "height" => self::intValidate($args['height'], 200, $valid),
        );

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