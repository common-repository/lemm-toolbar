<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Plugin Name: Lemm Toolbar
 * Plugin URI: https://wordpress.org/plugins/lemm-toolbar/
 * Description: Toolbar for Shortcuts
 * Version: 0.0.9
 * Author: Jonathan Wedel
 * Author URI: https://www.lemm.de/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

class LemmToolbar{

    /**
     * @var LemmToolbar
     */
    public static $instance;

    /**
     * @var stdClass
     */
    protected $configObject;

    /**
     * @var string $adminslug
     */
    protected $adminslug;

    /**
     * LemmToolbar CONSTRUCTOR
     */
    public function __construct(){
        //Include Advanced Custom Fields Plugin
        add_action( 'admin_init', array( $this, 'activeACF' ));
        add_action( 'admin_init', array( $this, 'activeACFFA' ));
        $this->activeACF();
        $this->activeACFFA();

        //Register Options
        $this->register_options();

        //Add Admin Page
        add_action( 'admin_menu', array( $this, 'lemm_admin_page' ) );
        add_action( 'admin_init', array( $this, 'lemm_toolbar_settings' ) );


        //Add Frontend Javascript and CSS
        add_action( 'wp_enqueue_scripts', array( $this, 'lemm_toolbar_scripts' ) );
        //Add Frontend Output
        add_action( 'wp_footer', array( $this, 'lemm_toolbar_frontend' ) );



    }

    /**
     * ENQUEUE SCRIPTS
     */
    public function lemm_toolbar_scripts(){
        wp_register_style( 'Tool_CSS', plugins_url( 'lemm-toolbar/assets/css/basis.css' ), __FILE__ );
        wp_enqueue_style( 'Tool_CSS' );
    }

    /**
     * INCLUDE ACF
     */
    public function activeACF(){
        //If ACF Pro exists in plugins folder
        if ( file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php' )) {
            include_once ( WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php' );
        }
        //Elseif ACF Pro exists in vendor of lemm-toolbar
        elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields-pro/acf.php' )) {
            include_once ( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields-pro/acf.php' );
        }
        //Else unpack zip
        else {
            $this->unzip( 'vendor/acf-pro.zip' );
        }
    }

    /**
     * INCLUDE ACFFA
     */
    public function activeACFFA(){
        //If ACFFA Pro exists in plugins folder
        if ( file_exists( WP_PLUGIN_DIR . '/advanced-custom-fields-font-awesome/acf-font-awesome.php' )) {
            include_once ( WP_PLUGIN_DIR . '/advanced-custom-fields-font-awesome/acf-font-awesome.php' );
        }
        //Elseif ACFFA Pro exists in vendor of lemm-toolbar
        elseif ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields-font-awesome/acf-font-awesome.php' )) {
            include_once ( plugin_dir_path( __FILE__ ) . 'vendor/advanced-custom-fields-font-awesome/acf-font-awesome.php' );
        }
        //Else unpack zip
        else {
            $this->unzip( 'vendor/acffa.zip' );
        }
    }

    /**
     * FUNCTION TO UNPACK ZIP FILES
     * @param $zipFile
     */
    public function unzip( $zipFile ){
        $zip    = new ZipArchive;
        $file   = plugin_dir_path( __FILE__ ) . $zipFile;
        $res    = $zip->open( $file );
        if( $res === TRUE ) {
            $zip->extractTo( plugin_dir_path( __FILE__ ) . 'vendor/' );
            $zip->close();
        }

    }

    /**
     * REGISTER ACF BACKEND FORM
     */
    public function register_options(){
        if( function_exists('acf_add_local_field_group') ):

            /** TOOLBAR FIELDS */
            acf_add_local_field_group( array(
                'key' => 'group_5c62ed79b0edc',
                'title' => 'lemm_toolbar_group',
                'fields'    => array(
                    array(
                        'key'           => 'field_5c62ed7d5b896',
                        'label'         => 'Tools (max 8)',
                        'name'          => 'lemm_tb_tool',
                        'type'          => 'repeater',
                        'layout'        => 'table',
                        'max'           => 8,
                        'sub_fields'    => array(
                            //TOOLBAR NAME
                            array(
                                'key'           => 'field_5c62edc55b897',
                                'label'         => 'Name',
                                'name'          => 'lemm_tb_name',
                                'type'          => 'text',
                                'required'      => 1,
                            ),
                            //TOOLBAR LINK
                            array(
                                'key'           => 'field_5c62edc55b898',
                                'label'         => 'Link',
                                'name'          => 'lemm_tb_link',
                                'type'          => 'link',
                            ),
                            //TOOLBAR ICON - ACFFA
                            array(
                                'key'           => 'field_5c62ede65b899',
                                'label'         => 'Icon',
                                'name'          => 'lemm_tb_icon',
                                'type'          => 'font-awesome',
                                'icon_sets'     => array(
                                    0   => 'far',
                                    1   => 'fas',
                                    2   => 'fal',
                                    3   => 'fab',
                                ),
                                'save_format'   => 'element',
                                'show_preview'  => 0,
                                'enqueue_fa'    => 1,
                            ),
                            array(
                                'key'           => 'field_KeYBmYYPwdl13',
                                'label'         => 'CSS-Class',
                                'name'          => 'css-class',
                                'type'          => 'text',
                                'instructions'  => 'Leerzeichen und Sonderzeichen werden entfernt.<br/> Die Zeichen "-" und "_" sind erlaubt.',
                            ),
                        ),
                    ),
                ),
                'location'  => array(
                    array(
                        array(
                            'param'     => 'options_page',
                            'operator'  => '==',
                            'value'     => 'lemm-toolbar',
                        ),
                    ),
                ),
                'menu_order'    => 0,
                'position'      => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));

            /** TOOLBAR STYLING **/
            acf_add_local_field_group( array(
                'key'                   => 'group_5c8f873d1e23d',
                'title'                 => 'Styling Einstellungen',
                'fields'                => array(
                    array(
                        'key'           => 'field_5c8f8745808f3',
                        'label'         => 'Toolbar Styling',
                        'name'          => 'toolbar_styling',
                        'type'          => 'group',
                        'required'      => 0,
                        'layout'        => 'block',
                        'sub_fields'    => array(
                            //TAB FIELD
                            array(
                                'key'           => 'field_5c8f9207808f5',
                                'label'         => 'Farben',
                                'name'          => 'farben',
                                'type'          => 'tab',
                                'placement'     => 'top',
                            ),
                            //PRIMARY COLOR
                            array(
                                'key'           => 'field_5c8f9232808f6',
                                'label'         => 'Primär Farbe',
                                'name'          => 'primaer_farbe',
                                'type'          => 'color_picker',
                            ),
                            //SECONDARY COLOR
                            array(
                                'key'           => 'field_5c8f9256808f7',
                                'label'         => 'Sekundär Farbe',
                                'name'          => 'sekundaer_farbe',
                                'type'          => 'color_picker',
                            ),
                            //FONT COLOR
                            array(
                                'key'           => 'field_5c8f927d808f8',
                                'label'         => 'Schrift Farbe',
                                'name'          => 'schrift_farbe',
                                'type'          => 'color_picker',
                            ),
                            //TAB FIELD
                            array(
                                'key'           => 'field_5c8f929e808f9',
                                'label'         => 'Styling',
                                'name'          => 'styling',
                                'type'          => 'tab',
                                'required'      => 0,
                                'placement'     => 'top',
                                'endpoint'      => 0,
                            ),
                            //Z-INDEX
                            array(
                                'key'           => 'field_5c8f930c808fa',
                                'label'         => 'z-index',
                                'name'          => 'z-index',
                                'type'          => 'number',
                                'instructions'  => 'Geben Sie den gewünschten z-index ein',
                            ),
                            //BORDER RADIUS
                            array(
                                'key'           => 'field_5c8f935d808fb',
                                'label'         => 'border-radius',
                                'name'          => 'border-radius',
                                'type'          => 'number',
                            ),
                            //UNIT FOR BORDER RADIUS
                            array(
                                'key'               => 'field_5c8f93f0808fc',
                                'label'             => 'Einheit für border-radius',
                                'name'              => 'radius-einheit',
                                'type'              => 'radio',
                                'instructions'      => 'Geben Sie die gewünschte Einheit für den border-radius an',
                                'choices'           => array(
                                    'prozent'   => '%',
                                    'pixel'     => 'px',
                                ),
                                'allow_null'        => 0,
                                'other_choice'      => 0,
                                'layout'            => 'vertical',
                                'return_format'     => 'label',
                                'save_other_choice' => 0,
                            ),
                            //COOKIE TAB
                            array(
                                'key'           => 'field_YuCNog9jAkSZl',
                                'label'         => 'Cookie',
                                'name'          => 'cookie',
                                'type'          => 'tab',
                                'required'      => 0,
                                'placement'     => 'top',
                                'endpoint'      => 0,
                            ),
                            //USE COOKIE
                            array(
                                'key'               => 'field_Qw9pZN3R8n7m6',
                                'label'             => 'Cookie verwenden',
                                'name'              => 'use-cookie',
                                'type'              => 'checkbox',
                                'instructions'      => 'Soll ein Cookie gesetzt werden um die Toolbar beim ersten Seitenaufruf aufzuklappen?',
                                'choices'           => array(
                                    'TRUE'   => 'Cookie verwenden',
                                ),
                                'allow_null'        => 0,
                                'other_choice'      => 0,
                                'layout'            => 'vertical',
                                'return_format'     => 'value',
                                'save_other_choice' => 0,
                            ),
                            //COOKIE EXPIRES
                            array(
                                'key'           => 'field_KLcBaLfKOpIg9',
                                'label'         => 'Expires',
                                'name'          => 'cookie-expires',
                                'type'          => 'number',
                                'instructions'  => 'Für wie lange soll der Cookie in Tagen gespeichert werden? (Default 30 Tage)',
                            ),
                        ),
                    ),
                ),
                'location'     => array(
                    array(
                        array(
                            'param'     => 'options_page',
                            'operator'  => '==',
                            'value'     => 'lemm-toolbar',
                        ),
                    ),
                ),
                'menu_order'            => 1,
                'position'              => 'normal',
                'style'                 => 'default',
                'label_placement'       => 'top',
                'instruction_placement' => 'label',
                'active'                => 1,
            ));
        endif;
    }

    /**
     * ADMIN BACKEND
     * @return string $this->adminslug
     */
    public function lemm_admin_page(){

        if ( empty ( $GLOBALS['admin_page_hooks']['lemm-plugins'] ) ) {
            add_menu_page('Lemm Plugins', 'Lemm Plugins', 'manage_options', 'lemm-plugins', array($this, 'lemm_plugins_admin_page'), 'dashicons-paperclip');
            add_submenu_page( 'lemm-plugins', 'Lemm Toolbar Page', 'Lemm Toolbar', 'manage_options', 'lemm-plugins', array( $this, 'lemm_toolbar_adminpage_content' ));
            return $this->adminslug = 'lemm-plugins';
        } else {
            $parent_slug    = 'lemm-plugins';
            $page_title     = 'Lemm Toolbar Page';
            $menu_title     = 'Lemm Toolbar';
            $capability     = 'manage_options';
            $slug           = 'lemm-toolbar';
            $callback       = array( $this, 'lemm_toolbar_adminpage_content' );
            add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $slug, $callback );
            return $this->adminslug = $slug;
        }
    }

    public function lemm_plugins_admin_page(){}

    /**
     * INSERT FORM IN ADMIN PAGE
     */
    public function lemm_toolbar_adminpage_content(){
        echo "<h1>Lemm Toolbar</h1>";
        do_action( 'acf/input/admin_enqueue_scripts' );
        $options = array(
            'id'            => 'acf-form',
            'post_id'       => 'options',
            'new_post'      => false,
            'field_groups'  => array(
                'group_5c62ed79b0edc',
                'group_5c8f873d1e23d',
            ),
            'return'        => admin_url( 'admin.php?page=' . $this->adminslug ),
            'submit_value'  => 'Speichern',
        );
        acf_form( $options );
    }

    /** SEND ACF FORM */
    public function lemm_toolbar_settings(){
        if( isset($_GET["page"]) && $_GET["page"] === $this->adminslug ){
            acf_form_head();
        }
    }

    /**
     * FRONTEND
     */
    public function lemm_toolbar_frontend(){

        $template   = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/toolbar.html' );
        $partial    = file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/tool.html' );
        $this->prepareConfigObject();

        $template = str_replace( "{ltb_settings}", json_encode( $this->configObject ), $template );
        $template = str_replace( "{path}", plugin_dir_url( __FILE__ ), $template );

        $rfields = get_field( 'field_5c62ed7d5b896', 'option' );
        $fields = array_reverse( $rfields );
        $zIndex = 1000;
        $class = 0;

        if( is_array( $fields ) ){

            $toolArray  = Array();
            foreach( $fields AS $field ){

                /**
                 * String Replace - In /templates/tool.html
                 */
                $toolArray[] = str_replace(
                    Array("{LINK}", "{TARGET}", "{ICON-CLASS-NR}", "{Z-INDEX}", "{ICON}", "{TITLE}", "{CLASS}"),
                    Array( $this->link( $field['lemm_tb_link'] ), $this->target( $field['lemm_tb_link'] ), $class, $zIndex, $field['lemm_tb_icon'], $field['lemm_tb_name'], $this->htmlClass( $field['css-class'] )),
                    $partial
                );

                $zIndex++;
                $class++;
            }
            /**
             * String Replace - In /template/toolbar.html
             */
            echo str_replace("{TOOL}", implode("\r\n", $toolArray), $template);

        }
    }

    public function htmlClass( $class ){
        return sanitize_html_class( $class );
    }

    /**
     * FIELD VALIDATION FOR PRIMARY COLOR
     * @param $color
     * @return string
     */
    public function primaryColor( $color ){
        if( !empty( $color ) ){
            return $color;          //PRIMARY COLOR
        } else {
            return '#000000';
        }
    }

    /**
     * FIELD VALIDATION FOR SECONDARY COLOR
     * @param $color
     * @return string
     */
    public function secColor( $color ){
        if( !empty( $color ) ){
            return $color;          //SECONDARY COLOR
        } else {
            return '#333333';
        }
    }

    /**
     * FIELD VALIDATION FOR FONT COLOR
     * @param $color
     * @return string
     */
    public function fontColor( $color ){
        if( !empty( $color ) ){
            return $color;          //FONT COLOR
        } else {
            return '#ffffff';
        }
    }

    /**
     * FIELD VALIDATION FOR LINK
     * @param $field
     * @return string
     */
    public function link( $field ){
        if( !empty( $field ) ){
            $temp = $field['url'];
            return $temp;
        } else {
            return '';
        }
    }

    /**
     * FIELD VALIDATION FOR LINK TARGET
     * @param $target
     * @return string
     */
    public function target( $target ){
        if( !empty( $target['target'] ) ){
            return "_blank";
        } else {
            return "_self";
        }
    }

    /**
     * Set up ConfigObject - Object for JSON file
     */
    public function prepareConfigObject(){
        $this->configObject = new \stdClass();
        $this->configObject->primaerFarbe   = $this->primaryColor( get_field( 'field_5c8f8745808f3', 'option' )[ 'primaer_farbe' ] );
        $this->configObject->sekundaerFarbe = $this->secColor( get_field( 'field_5c8f8745808f3', 'option' )[ 'sekundaer_farbe' ] );
        $this->configObject->schriftFarbe   = $this->fontColor( get_field( 'field_5c8f8745808f3', 'option' )[ 'schrift_farbe' ] );
        $this->configObject->zIndex         = get_field( 'field_5c8f8745808f3', 'option' )['z-index'];
        $this->configObject->borderRadius   = get_field( 'field_5c8f8745808f3', 'option' )['border-radius'];
        $this->configObject->radiusEinheit  = get_field( 'field_5c8f8745808f3', 'option' )['radius-einheit'];
        $this->configObject->useCookie      = get_field( 'field_5c8f8745808f3', 'option' )['use-cookie'];
        $this->configObject->cookieExpires  = get_field( 'field_5c8f8745808f3', 'option' )['cookie-expires'];
    }

    /**
     * @return LemmToolbar
     */
    public static function getInstance(){
        if( !is_object( static::$instance ) ){
            static::$instance = new static();
        }
        return static::$instance;
    }
}

/**
 * start the action
 */
LemmToolbar::getInstance();