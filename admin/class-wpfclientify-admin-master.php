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

    $this->$db_version = '1.0.3';
  }

  /**
  * Cron job maintenance tasks.
  */
  public function wpfclientifyHourlyCron(){
    $this->wpfclientifyMasterDatabase();
    $this->wpfclientifyMasterContacts();
    return;
  }

  public function wpfclientifyMasterDatabase(){
    $this->custom_logs('Wpfclientify Database');
    $timeFirst  = strtotime('now');

    $this->custom_logs('Database: ' .$this->$db_version. ' actual: ' .get_option( "wpfclientify_db_version" ) );
    if ( get_option( "wpfclientify_db_version" ) != $this->$db_version ) {
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
        contactos_ip tinytext DEFAULT '' NOT NULL,
        contactos_pais tinytext DEFAULT '' NOT NULL,
        contactos_utm_term tinytext DEFAULT '' NOT NULL,
        contactos_utm_campaign tinytext DEFAULT '' NOT NULL,
        contactos_utm_medium tinytext DEFAULT '' NOT NULL,
        contactos_utm_source tinytext DEFAULT '' NOT NULL,
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
        oportunidades_ip tinytext DEFAULT '' NOT NULL,
        oportunidades_origen tinytext DEFAULT '' NOT NULL,
        oportunidades_destino tinytext DEFAULT '' NOT NULL,
        oportunidades_ataud tinytext DEFAULT '' NOT NULL,
        oportunidades_velatorio tinytext DEFAULT '' NOT NULL,
        oportunidades_ceremonia tinytext DEFAULT '' NOT NULL,
        oportunidades_cuando tinytext DEFAULT '' NOT NULL,
        oportunidades_ubicacion tinytext DEFAULT '' NOT NULL,
        oportunidades_referencia tinytext DEFAULT '' NOT NULL,
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
  * Cron job maintenance Base de datos
  */
  public function wpfclientifyMasterContacts(){
    $this->custom_logs('Wpfclientify Contacts');
    $timeFirst  = strtotime('now');
    global $wpdb, $wp_locale;
    $table_name = $wpdb->prefix . 'wpf_clientify_contacts';

    $fechabusqueda = new DateTime("now", new DateTimeZone('Europe/Madrid'));
    $fechabusqueda->modify("-2 days");

    //https://api.clientify.net/v1/contacts/?created[gt]=2023-05-08
    //"next": "https://api.clientify.net/v1/contacts/?created%5Bgt%5D=2023-05-06&page=2"
    // primera vez
    //$URLclientify = $this->GetContactsUrl ;
    // posteriores
    $URLclientify = $this->GetContactsUrl. '?modified[gt]=' .$fechabusqueda->format("Y-m-d") ;
    while( $URLclientify != NULL ){
      $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
      $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
      if ( is_wp_error($request) ) {
        $this->custom_logs('Wpfclientify Contacts: is_wp_error');
        exit;
      }
      $bodyrequest = json_decode( $request['body'] );
      $URLclientify = $bodyrequest->next;
      foreach ($bodyrequest->results as $key => $result) {
        $contacto = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table_name WHERE contact_id = %s", $result->id ), ARRAY_A);
        $contactos_pais = '';
        $contactos_ip = '';
        $contactos_utm_source = '';
        $contactos_utm_term = '';
        $contactos_utm_campaign = '';
        $contactos_utm_medium = '';
        foreach ($result->custom_fields as $key => $value) {
          if ( $value->field == 'contactos_pais' ) $contactos_pais = $value->value;
          if ( $value->field == 'contactos_ip' ) $contactos_ip = $value->value;
          if ( $value->field == 'contactos_utm_source' ) $contactos_utm_source = $value->value;
          if ( $value->field == 'contactos_utm_term' ) $contactos_utm_term = $value->value;
          if ( $value->field == 'contactos_utm_campaign' ) $contactos_utm_campaign = $value->value;
          if ( $value->field == 'contactos_utm_medium' ) $contactos_utm_medium = $value->value;
        }
        $contact_source = "";
        if ( $result->contact_source != NULL ) $contact_source = $result->contact_source->name;
        if ( $contacto != NULL ){
          //update
          $this->custom_logs('Wpfclientify Contacts UPDATE: ' .$result->id. ', ' .$result->first_name );
          $wpdb->update(
            $table_name, array(
              'first_name' => $result->first_name,
              'contact_source' => $contact_source,
              'email' => $result->emails[0]->email,
              'phone' => $result->phones[0]->phone,
              'contactos_ip' => $contactos_ip,
              'contactos_pais' => $contactos_pais,
              'contactos_utm_source' => $contactos_utm_source,
              'contactos_utm_term' => $contactos_utm_term,
              'contactos_utm_campaign' => $contactos_utm_campaign,
              'contactos_utm_medium' => $contactos_utm_medium,
              'created' => $result->created,
              'modified' => $result->modified,
            ),
            array(
              'contact_id' => $result->id,
            )
          );
        }else{
          //Create
          $this->custom_logs('Wpfclientify Contacts CREATE: ' .$result->id. ', ' .$result->first_name );
          $wpdb->insert(
            $table_name, array(
              'time' => current_time( 'mysql' ),
              'contact_id' => $result->id,
              'first_name' => $result->first_name,
              'contact_source' => $contact_source,
              'email' => $result->emails[0]->email,
              'phone' => $result->phones[0]->phone,
              'contactos_ip' => $contactos_ip,
              'contactos_pais' => $contactos_pais,
              'contactos_utm_source' => $contactos_utm_source,
              'contactos_utm_term' => $contactos_utm_term,
              'contactos_utm_campaign' => $contactos_utm_campaign,
              'contactos_utm_medium' => $contactos_utm_medium,
              'created' => $result->created,
              'modified' => $result->modified,
            )
          );
        }
      }
      $this->custom_logs('Wpfclientify Contacts: ---' );
    }// END while( $URLclientify != NULL )

    $this->custom_logs('Wpfclientify Contacts ENDS' );
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
