<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
/**
* The admin-specific functionality of the plugin.
*
* @link       https://github.com/Efraimcat/wpfclientify/
* @since      1.0.0
*
* @package    WpfClientify
* @subpackage WpfClientify/admin
*/

class Wpfclientify_Admin_Master extends Wpfclientify_Admin {
  public function __construct( ) {

    $this->GetContactsUrl = 'https://api.clientify.net/v1/contacts/';
    $this->GetDealsUrl = 'https://api.clientify.net/v1/deals/';
    $this->clientifykey = get_option( 'wpfunos_APIClientifyKeyClientify' );

    $this->$db_version = '1.0.2';
  }

  /**
  * Cron job maintenance tasks.
  */
  public function wpfclientifyHourlyCron(){
    $this->wpfclientifyMasterDatabase();
    //$this->wpfunosMaintenanceUsuariosCSV();
    return;
  }

  public function wpfclientifyMasterDatabase(){
    $this->custom_logs('Wpfclientify Database');
    $timeFirst  = strtotime('now');

    $this->custom_logs('Database: ' .$this->$db_version. ' actual: ' .get_option( "wpfclientify_db_version" ) );
    if ( get_option( "wpfclientify_db_version" ) != $db_version ) {
      $this->custom_logs('Database: Updating DB ' .get_option( "wpfclientify_db_version" ). ' a ' .$this->$db_version );
      global $wpdb;
      $table_name = $wpdb->prefix . 'wpf_clientify_contacts';
      $charset_collate = $wpdb->get_charset_collate();

      $sqlcontacts = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        contact_id tinytext DEFAULT '' NOT NULL,
        first_name tinytext DEFAULT '' NOT NULL,
        contact_source tinytext DEFAULT '' NOT NULL,
        email tinytext DEFAULT '' NOT NULL,
        phone tinytext DEFAULT '' NOT NULL,
        ip tinytext DEFAULT '' NOT NULL,
        pais tinytext DEFAULT '' NOT NULL,
        utm_term tinytext DEFAULT '' NOT NULL,
        utm_campaign tinytext DEFAULT '' NOT NULL,
        utm_medium tinytext DEFAULT '' NOT NULL,
        utm_source tinytext DEFAULT '' NOT NULL,
        created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
      );";

      $table_name = $wpdb->prefix . 'wpf_clientify_deals';
      $sqldeals = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        deal_id tinytext DEFAULT '' NOT NULL,
        name tinytext DEFAULT '' NOT NULL,
        contact tinytext DEFAULT '' NOT NULL,
        contact_name tinytext DEFAULT '' NOT NULL,
        contact_email tinytext DEFAULT '' NOT NULL,
        contact_phone tinytext DEFAULT '' NOT NULL,
        contact_source tinytext DEFAULT '' NOT NULL,
        amount tinytext DEFAULT '' NOT NULL,
        pipeline tinytext DEFAULT '' NOT NULL,
        pipeline_desc tinytext DEFAULT '' NOT NULL,
        pipeline_stage tinytext DEFAULT '' NOT NULL,
        pipeline_stage_desc tinytext DEFAULT '' NOT NULL,
        tags tinytext DEFAULT '' NOT NULL,
        ip tinytext DEFAULT '' NOT NULL,
        origen tinytext DEFAULT '' NOT NULL,
        destino tinytext DEFAULT '' NOT NULL,
        ataud tinytext DEFAULT '' NOT NULL,
        velatorio tinytext DEFAULT '' NOT NULL,
        ceremonia tinytext DEFAULT '' NOT NULL,
        cuando tinytext DEFAULT '' NOT NULL,
        ubicacion tinytext DEFAULT '' NOT NULL,
        referencia tinytext DEFAULT '' NOT NULL,
        expected_closed_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
      );";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sqlcontacts );
      dbDelta( $sqldeals );
      update_option( "wpfclientify_db_version", $this->$db_version );
    }

    $this->custom_logs('Wpfclientify Database ENDS' );
    $total = strtotime('now') - $timeFirst ;
    $this->custom_logs('--- ' .$total.' sec.');
  }


  /**
  * Utility: create entry in the log file.
  * $this->custom_logs( $this->dumpPOST($message) );
  */
  private function custom_logs($message){
    $upload_dir = wp_upload_dir();
    if (is_array($message)) {
      $message = json_encode($message);
    }
    if (!file_exists( $upload_dir['basedir'] . '/wpfunos-logs') ) {
      mkdir( $upload_dir['basedir'] . '/wpfunos-logs' );
    }
    $time = current_time("d-M-Y H:i:s:v");
    $ban = "#$time: $message\r\n";
    $file = $upload_dir['basedir'] . '/wpfunos-logs/wpfunos-adminlog-' . current_time("Y-m-d") . '.log';
    $open = fopen($file, "a");
    fputs($open, $ban);
    fclose( $open );
  }


}
