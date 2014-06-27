<?php
/**
 *
 *
 * @ingroup Extensions
 */

class InteractiveTimeline {

    public static function parserHook( $input, $args = array(), $parser, $frame ) {
        $output = $parser->recursiveTagParse( $text, $frame );
        return '<pre>' . $output . '</pre>';
    }

    public static function onParserFirstCallInit( &$parser ) {
        // Adds the <itimeline>...</itimeline> tag to the parser.
        $parser -> setHook('itimeline', 'InteractiveTimeline::parserHook');

        return true;
    }
}