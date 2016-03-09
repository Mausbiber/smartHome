<?php
    if ( isset( $_GET['lang'] ) )
    {
        if ( !set_lang_id( $_GET['lang'] ) ) set_lang_id( get_lang_id() );
    }
    else
    {
        set_lang_id( get_lang_id() );
    }

    function is_language_supported( $lang_id )
    {
        $langs = array( "en", "de" );

        return ( in_array( $lang_id, $langs ) ) ? true : false;
    }

    function set_lang_id( $lang_id )
    {
        if ( strlen( $lang_id ) == 2 && is_language_supported( $lang_id ) )
        {
            $language_file = includeTrailingCharacter( realpath(dirname(__FILE__)), "/" ) . $lang_id.".php";

            if ( is_file( $language_file ) && file_exists( $language_file ) )
            {
                $expiration_date = time()+3600*24*365;
                setcookie( 'lang', $lang_id, $expiration_date, '/');
                include_once( $language_file );
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function get_lang_id()
    {
        return ( isset( $_COOKIE['lang'] ) && strlen( $_COOKIE['lang'] ) == 2 && is_language_supported( $_COOKIE['lang'] ) ) ? htmlspecialchars($_COOKIE['lang']) : 'en';
    }

    function includeTrailingCharacter($string, $character)
    {
        if (strlen($string) > 0) {
            if (substr($string, -1) !== $character) {
                return $string . $character;
            } else {
                return $string;
            }
        } else {
            return $character;
        }
    }
?>
