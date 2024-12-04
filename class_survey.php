<?php
/**
 * Plugin Name: Survey Pluign
 * Plugin URI: https//codexpert.io
 * Description: Survey Pluign
 * Version: 1.0
 * Tested up to: 6.3
 * Author: codexpert
 * Author URI: https://codexpert.io
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  wp-survey
 */

// Exit if accessed directly.


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

use Code\Survey\Inc\Class_addnew_survey;
use Code\Survey\Inc\Class_viewall_survey;
use Code\Survey\Inc\Class_frontend;
use Code\Survey\Inc\Class_result_survey;
use Code\Survey\Inc\Class_visual_result;

Class class_survey{

      private $newsurvay;
      private $viewsurvey;
      private $resultsurvey; 
      private $forntend;
      private $result;
      private $visualresult;

     const VERSION = '1.0';

        public function __construct()
        {  
            $this->code_define_constants();
            register_activation_hook(CODXPERT_MAIN_FILE, [$this, 'activate']); 
            add_action('admin_menu', [$this,'register_my_survey_page']);
            add_action('admin_enqueue_scripts', [$this, 'code_enqueue_assets']);
            add_action('wp_enqueue_scripts', [$this, 'frontend_enqueue_assets']);
            
            $this->newsurvay = new Class_addnew_survey();
            $this->viewsurvey = new Class_viewall_survey();
            $this->forntend = new Class_frontend();
            $this->result = new Class_result_survey();
            $this->visualresult = new Class_visual_result();

            add_action('plugins_loaded', [$this, 'initialize_frontend_load']);
        }    
    
        public function code_define_constants()
        {
            define('CODXPERT_VERSION', self::VERSION);
            define('CODXPERT_FILE', __FILE__);
            define('CODXPERT_PATH', __DIR__);
            define('CODXPERT_PLUGIN_DIR', plugin_dir_path(__FILE__));
            define('CODXPERT_URL', plugins_url('', CODXPERT_FILE));
            define('CODXPERT_ASSETS', CODXPERT_URL . '/assets');
            define('CODXPERT_MAIN_FILE', __FILE__);
        }


        public function activate() {
            global $wpdb;
            $code_survey = $wpdb->prefix . 'code_survey';
            $survey_result = $wpdb->prefix . 'survey_result';
            $charset_collate = $wpdb->get_charset_collate();
        
            $sql = "CREATE TABLE $code_survey (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                title varchar(255) NOT NULL,
                details text NOT NULL,
                question_1 varchar(100) NOT NULL,
                question_2 varchar(100) NOT NULL,
                question_3 varchar(100) NOT NULL,
                question_4 varchar(100) NOT NULL,
                startdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                enddate datetime NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        
            $sql = "CREATE TABLE $survey_result (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                answer varchar(100) NOT NULL,
                sid int NOT NULL,
                uid int NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
        
            dbDelta($sql);
        }

        public function initialize_frontend_load() {
            new Code\Survey\Inc\Class_frontend(); 
        }
         
        public function code_enqueue_assets()
        {   
            wp_enqueue_script('jquery');
            wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/2.1.3/js/dataTables.js', array('jquery'), time(), true);             
            wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css', array('jquery'), time(), false);  
            wp_enqueue_script('jquery-toast-plugin', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js', array('jquery'), '1.3.2', true);
            wp_enqueue_style('jquery-toast-plugin-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css');            

            wp_enqueue_script('barchart-js', 'https://cdn.canvasjs.com/canvasjs.min.js', array('jquery'), time(), true);   

            wp_enqueue_script('jquery-modal', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js', array('jquery'), time(), true);    
            wp_enqueue_style('jquery-modal-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css');
            

            wp_enqueue_script('backend-js', plugin_dir_url(__FILE__) . 'assets/js/backend.js', array('jquery'), time(), true);
            wp_localize_script('backend-js', 'ajax_ob', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('survey-wp-nonce'),
            ));
        }

        public function frontend_enqueue_assets()
        { 
            wp_enqueue_script('frontend-js', plugin_dir_url(__FILE__) . 'assets/js/frontend.js', array('jquery'), time(), true);
            wp_localize_script('frontend-js', 'ajax_ob', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('survey-wp-nonce'),
                'user_id' => get_current_user_id()
            ));
        }      

        public function register_my_survey_page() {
            add_submenu_page(
                'tools.php',
                __('Survey', 'wp-survey'), // Page Title
                __('Survey', 'wp-survey'), // Menu Title
                'manage_options',  // Capability
                'survey-pool',  //slug
                array($this, 'code_dashboard'), //callback function
            ); 
        } 

        public function code_dashboard(){              

            if (!current_user_can('manage_options')) {
                return;
            }
            // Get the active tab from the $_GET param
    
            $default_tab = 'addpool'; // Set your default tab here
    
            $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;
            // Nonce verification code
            if ( !isset( $_GET['tab'] ) || !wp_verify_nonce( $_GET['tab'] ) ){
    
            /* echo '<div class="wrap">'; */
            echo '<div class="ai-blog-navbar-header">';
            // Here are our tabs
            echo '<nav class="nav-tab-wrapper">';

                echo '<a href="' . esc_url( '?page=survey-pool&tab=addpool' ) . '" class="nav-tab ' . ( esc_attr( $tab ) === 'addpool' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Add New Survey', 'wp-survey' ) . '</a>';
                echo '<a href="' . esc_url( '?page=survey-pool&tab=viewpool' ) . '" class="nav-tab ' . ( esc_attr( $tab ) === 'viewpool' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'View Pool', 'wp-survey' ) . '</a>';
                echo '<a href="' . esc_url( '?page=survey-pool&tab=poolresult' ) . '" class="nav-tab ' . ( esc_attr( $tab ) === 'poolresult' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Survey Result', 'wp-survey' ) . '</a>';
                echo '<a href="' . esc_url( '?page=survey-pool&tab=visually' ) . '" class="nav-tab ' . ( esc_attr( $tab ) === 'poolresult' ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'visual result', 'wp-survey' ) . '</a>';
    
            echo '</nav>';
    
            echo '</div>';  
            echo '<div class="tab-content">';
            
            switch ($tab):

                case 'addpool':
                  $this->newsurvay->create_survey();
                break;
                case 'viewpool':   
                  $this->viewsurvey->viewall_survey();
                break;
    
                case 'poolresult':                     
                    
                    $this->result->survey_result();
                break;

                case 'visually';
                  $this->visualresult->visual_result();
                break;

            endswitch;
            echo '</div>';
    
            } else {
                // Nonce is not valid, then showing this error
                echo 'Nonce verification failed!';
            }      

        }  

        
}

new class_survey();