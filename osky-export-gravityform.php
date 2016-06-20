<?php
/*
Plugin Name: Osky Export Gravity Form
Description: Custom Export Feature for HLA
Author: Ekhwan Sakib
Version: 0.1
*/

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

define( 'OCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
require OCS_PLUGIN_DIR . '/includes/modules/functions.php';
require OCS_PLUGIN_DIR . '/includes/modules/aus-postcode-state.php';

// function osky_export_scripts()
// {
//     // Register the script like this for a plugin:
//     wp_register_script( 'rome-script', plugins_url( '/js/rome/dist/rome.js', __FILE__ ) );
//     wp_register_script( 'export-script', plugins_url( '/js/export.js', __FILE__ ) );

//     wp_enqueue_script( 'rome-script' );
//     wp_enqueue_script( 'export-script' );
// }
// add_action( 'wp_enqueue_scripts', 'osky_export_scripts' );

function osky_export_menu(){
        add_menu_page( 'HLA Report Generator', 'HLA Report', 'manage_options', 'hla-report-generator', 'load_generator_page' );
}
add_action('admin_menu', 'osky_export_menu');

function load_generator_page(){
		$forms_id = array(
			6, // Make an appointment
			8, //Survey
			17, //Landing Page
			19, //Tinnitus
			20, //Win Ipad
			);

		$forms = array();

		foreach( $forms_id as $form_id ) {
			$temp = GFAPI::get_form( $form_id );
			$forms[$form_id] = $temp['title'];
		}

		include( OCS_PLUGIN_DIR . '/includes/templates/export.php' );
}

function osky_export_assets( $hook )
{
	if ( 'toplevel_page_hla-report-generator' != $hook ) {
        return;
    }

    // Register the script like this for a plugin:
    wp_register_script( 'osky-rome-script', plugins_url( '/js/rome/dist/rome.js', __FILE__ ), array(), null, true);
    wp_register_script( 'osky-export-script', plugins_url( '/js/export.js', __FILE__ ), array(), null, true );

    wp_register_style( 'osky-rome-style', plugins_url( '/css/rome.css', __FILE__ ), array(), 'all' );
    wp_register_style( 'osky-export-style', plugins_url( '/css/export.css', __FILE__ ), array(), 'all' );

    wp_enqueue_script( 'osky-rome-script' );
    wp_enqueue_script( 'osky-export-script' );
    wp_enqueue_style( 'osky-rome-style' );
    wp_enqueue_style( 'osky-export-style' );

}
add_action( 'admin_enqueue_scripts', 'osky_export_assets' );

function osky_hla_export() {
	if( isset( $_GET['hla_report_export'] ) && $_GET['hla_report_export'] == true ) {
		$form_id    = $_GET['form'];
		$start_date = $_GET['start_date'];
		$end_date   = $_GET['end_date'];
		$send_to    = $_GET['email'];

		$db_start_date = get_db_date( $start_date );
		$db_end_date = get_db_date( $end_date );

		$entries = sanitize_entries( $form_id, $db_start_date, $db_end_date );

		$entries = extra_data( $entries );

		$entries = filter_entries( $form_id, $entries );

		$entries = generate_array_leads( $form_id, $entries );

		$export = array();
		$export[] = array('FullName','ClinicName','Homephone','Mobile','DOB','CampaignCode','Stream','Incentive','State','Postcode','Email','PathIndicator','SurveyLink','Appointment_Me','Appointment_Partner','Message', 'Entry Date');

		foreach ($entries as $entry) {
			array_push($export, $entry);
		}

		if( count( $export ) > 1){

			array_to_csv_download( $export );
		}
		die();

	}
}
add_action( 'admin_init', 'osky_hla_export' );