<?php
/*
Plugin Name: Pretty Gists
Plugin URI: https://github.com/saurini/Wordpress-Pretty-Gists
Description: Gives you the ability to add Github Gists to your site decorated with line numbes and alternating line color.
Author: Rob Saurini
Version: 1.0
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


class RS_Pretty_Gists{

  public function __construct(){

    add_action( 'admin_menu', array( &$this, 'rs_pretty_gists_add_options_page' ) );
    add_action( 'admin_init', array( &$this, 'rs_pretty_gists_admin_init' ) );
    add_action( 'admin_head', array( &$this, 'rs_pretty_gists_admin_scripts' ) );
  }
  
  public function rs_pretty_gists_admin_scripts(){
    $url =  plugins_url() . '/pretty-gists/style.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
  }

  public function rs_pretty_gists_add_options_page(){

    add_options_page('Pretty Gist Customization', 'Pretty Gists', 'manage_options', 'rs-pretty-gists', array( &$this, 'rs_pretty_gists_render_settings' ) );


  }

  public function rs_pretty_gists_render_settings(){
?>
    <div id="rs-pretty-gists-wrapper" class="wrap">
      <div id="icon-options-general" class="icon32"><br></div><h2>Pretty Gist Customization</h2>
      <p>Options for coloring embedded Gists</p>
      <form action="options.php" method="post">
        <?php settings_fields( 'rs_pretty_gists' ); ?>
        <?php do_settings_sections( 'rs-pretty-gists' ); ?>
      <p class="submit">  
        <input name="Submit" id="submit" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
      </p>
      </form>
    </div>
<?php
  }

  public function rs_pretty_gists_admin_init(){

    register_setting( 'rs_pretty_gists', 'rs_pretty_gists_options', array( &$this, 'rs_pretty_gists_options_validate') );

    add_settings_section( 'rs_pretty_gists_main', 'Color Settings', array( &$this, 'rs_pretty_gists_section_text' ), 'rs-pretty-gists' );

    add_settings_field( 'rs_pretty_gists_options_num_color', 'Line number color', array( &$this, 'rs_pretty_gists_options_num_color' ), 'rs-pretty-gists', 'rs_pretty_gists_main' );    

    add_settings_field( 'rs_pretty_gists_options_border_color', 'Border color', array( &$this, 'rs_pretty_gists_options_border_color' ), 'rs-pretty-gists', 'rs_pretty_gists_main' );

    add_settings_field( 'rs_pretty_gists_options_bg_color', 'Background color', array( &$this, 'rs_pretty_gists_options_bg_color' ), 'rs-pretty-gists', 'rs_pretty_gists_main' );

    add_settings_field( 'rs_pretty_gists_options_alt_bg_color', 'Alternating background color', array( &$this, 'rs_pretty_gists_options_alt_bg_color' ), 'rs-pretty-gists', 'rs_pretty_gists_main' );

  }

  public function rs_pretty_gists_section_text(){
    echo "<p>Add any color you want, so long is it's in hexadecimal notation ( e.g. ff0000 for red ).</p>";
  }

  public function rs_pretty_gists_options_num_color(){

    $options = get_option( 'rs_pretty_gists_options' );
    echo <<<INPUT
      <input id="rs_pretty_gists_num_color" name="rs_pretty_gists_options[num_color]" size="8" type="text" value="{$options[ 'num_color' ]}" />
      <p class="description">Default is <span class="swatch num-color"></span>#3aa1c9</p>
INPUT;
  }

  public function rs_pretty_gists_options_border_color(){

    $options = get_option( 'rs_pretty_gists_options' );

    echo <<<INPUT
        <input id="rs_pretty_gists_border_color" name="rs_pretty_gists_options[border_color]" size="8" type="text" value="{$options[ 'border_color' ]}" />
        <p class="description">Default is <span class="swatch border-color"></span>#aec7ba</p>
INPUT;
  }

  public function rs_pretty_gists_options_bg_color(){

    $options = get_option( 'rs_pretty_gists_options' );

    echo <<<INPUT
        <input id="rs_pretty_gists_bg_color" name="rs_pretty_gists_options[bg_color]" size="8" type="text" value="{$options[ 'bg_color' ]}" />
        <p class="description">Default is <span class="swatch bg-color"></span>#f8f8ff</p>
INPUT;
  }

  public function rs_pretty_gists_options_alt_bg_color(){

    $options = get_option( 'rs_pretty_gists_options' );

    echo <<<INPUT
        <input id="rs_pretty_gists_alt_bg_color" name="rs_pretty_gists_options[alt_bg_color]" size="8" type="text" value="{$options[ 'alt_bg_color' ]}" />
        <p class="description">Default is <span class="swatch alt-bg-color"></span>#f0f0f0</p>
INPUT;
  }

  public function rs_pretty_gists_options_validate( $input ){

    foreach( $input as $setting => $value ){

      $valid_input[ $setting ] = trim( $input[ $setting ] );

      if( ! preg_match( '/^#?[a-f0-9]{3,6}$/i', $valid_input[ $setting ] ) )
        $valid_input[ $setting ] = '';
      
      $valid_input[ $setting ] = ltrim( $valid_input[ $setting ], '#' );
    }

    return $valid_input;
  }

}

$rs_pretty_gists = new RS_Pretty_Gists;


function pretty_gist_func( $atts ){

  extract(shortcode_atts(array('id' => '1337'), $atts));

  if ( preg_match( '/[^0-9^a-z]/i', $id ) )
    return;

  $default_colors = array( 'num_color' => '3AA1C9', 'border_color' => 'AEC7BA', 'bg_color' => 'F8F8FF', 'alt_bg_color' => 'f0f0f0' );
  $user_colors = get_option( 'rs_pretty_gists_options' );
  $gist_colors = array();

  foreach( $default_colors as $key => $value ){
    $gist_colors[ $key ] =  empty( $user_colors[ $key ] ) ?  $default_colors[ $key ] : $user_colors[ $key ] ;
  }

  return <<<HTML
  <style type="text/css">
    html body div .gist .gist-file .gist-data {
      background-color: #{$gist_colors['bg_color']};
    }
    html body div .gist .gist-file .gist-data  pre {
      padding: 0 !important;
    }
    .gist .line {
      height: 20px;
      line-height: 20px;
    }
    .gist .line:nth-child( 2n+1 ){
      background-color: #{$gist_colors['alt_bg_color']};
    }
    .gist .line-number{
      border-right: 3px solid #{$gist_colors['border_color']};
      color: #{$gist_colors['num_color']};
      display: inline-block;
      font-weight: bold;
      height: 20px;
      margin: 0 5px;
    }
  </style>
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

    \$( '.gist .line' ).css( 'width', ( longest_line * 9 ) + 'px' );

    var max_line_number_length = ( line_number.toString().length ) * 9 + 9+ 'px';
    \$( '.gist .line-number' ).css( 'width', max_line_number_length );
    
    // Set the target for each link at the bottom to _blank
    \$( '.gist-meta a' ).each( function(){

      \$( this ).attr( 'target', '_blank' );
       
      var href = \$( this ).attr( 'href' );
       
      if ( href.match( /\/raw/ ) ){
       
        \$( this ).on( 'click', function(){
        
          var raw_window = window.open( href , "Raw Gist", "toolbar=0,status=0,width=500,height=600,scrollbars=1" );
          raw_window.moveTo( 500, 50 );
          
          // Prevent the link from opening the usual way
          return false;
        
         });
       }
     });
   } )( jQuery );
 </script>

HTML;
}

add_shortcode( 'pretty_gist', 'pretty_gist_func' );


