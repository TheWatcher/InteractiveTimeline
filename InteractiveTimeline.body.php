<?php
/**
 *
 *
 * @ingroup Extensions
 */

class InteractiveTimeline {

    public static function parserHook( $input, $args = array(), $parser, $frame ) {
        global $wgOut;
        $options = array(
            "width" => $args['width'] || 100,
            "height" => $args['height'] || 200,
        );

        return Html::element('script', array('type' => 'text/javascript'), "var itdata_test = ".FormatJson::encode($options));
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