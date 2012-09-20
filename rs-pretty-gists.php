<?php
/*
Plugin Name: Pretty Gists
Plugin URI: https://github.com/saurini/Wordpress-Pretty-Gists
Description: Gives you the ability to add Github Gists to your site decorated with line numbes and alternating line color.
Author: Rob Saurini
Version: 0.8
Author URI: http://saurini.com

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

// Inserts an embedded gist into the content usage [gist id="12345"]
function rs_pretty_gist_func( $atts ){
        extract(shortcode_atts(array(
                'id' => '1337'), $atts));

        if ( preg_match( '/[^0-9^a-z^A-Z]/', $id ) )
                return;

        return <<<HTML
        <script type="text/javascript" src="http://gist.github.com/{$id}.js"></script>
        <script type="text/javascript">
        ( function( \$ ){

                // store this in global scope so it can be modified and then accessed with the each is done
                var line_number;
                var longest_line = 0;

                \$( '.gist .line' ).each( function(){


                        var line_id = \$( this ).attr( 'id' );
                        var line_length = 0;
                        line_number = line_id.match( /\d+/ )[0];

                        \$( this ).prepend( '<span class="line-number">' + line_number + '.</span>' );



                        \$( this ).children().each( function(){
                                line_length += \$( this ).text().length;
                        });

                        if ( line_length > longest_line )
                                longest_line = line_length;

                });
                \$( '.gist .line' ).css( 'width', ( longest_line * 8 ) + 'px' );

                var max_line_number_length = ( line_number.toString().length ) * 9 + 9+ 'px';
                \$( '.gist .line-number' ).css( 'width', max_line_number_length );

        } )( jQuery );
        </script>

HTML;
}

add_shortcode( 'rs_gist', 'rs_pretty_gist_func' );

