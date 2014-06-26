<?php
/**
 *
 *
 * @ingroup Extensions
 */

class InteractiveTimeline {

    public static function parserHook( $text, $args = array(), $parser ) {


    }

	public static function onParserFirstCallInit( &$parser ) {
        // Adds the <itimeline>...</itimeline> tag to the parser.
        $parser -> setHook('itimeline', 'InteractiveTimeline::parserHook');

        return true;
    }
}