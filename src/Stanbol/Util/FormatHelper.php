<?php

namespace Stanbol\Util;

/**
 * <p>Utility class to deal with request and response formats</p>
 *
 * @author Antonio David Pérez Morales <adperezmorales@gmail.com>
 */
class FormatHelper {
    
    /**
     * <p>Guess the mime type format of a model</p>
     * 
     * @param string $model The model which guessing the format from
     * @param string $filename Optional The filename containing the model in order to try to guess the format from it
     * 
     */
    public static function guessFormat($model, $filename = null) {
     return \EasyRdf_Format::guessFormat($model, $filename)->getDefaultMimeType();
    }
    
}

?>
