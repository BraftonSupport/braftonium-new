<?php
class MapUtility {

    /**
     * @param $content string
     * @param $blocks Array of parsed block objects
     * @return array acf/google-map blocks
     */
    private static function getAllMapBlocksFromContent($content,$blocks) : array {
        $columns = array_column($blocks,"blockName");
        return array_filter($columns,function($key) {
            return $key == 'acf/google-map';
        }, ARRAY_FILTER_USE_BOTH );
    }

    /**
     * @param $content string
     * @return string - id of the last gutenberg map block to be rendered from inside page or post content 
     */
    public static function getLastMapBlockID( $content ) : string {
        $blocks = parse_blocks($content);
        $maps = MapUtility::getAllMapBlocksFromContent($content,$blocks);
        $mapsKeys = array_keys($maps);
        $lastMapBlock = array_key_exists($mapsKeys[array_key_last($mapsKeys)], $blocks) ? $blocks[$mapsKeys[array_key_last($mapsKeys)]] : array();
        return $lastMapBlock['attrs']['id'] ? : "";
    }

}
