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
require_once 'class-wpfclientify-admin-master.php';

class Wpfclientify_Admin {

  private $plugin_name;
  private $version;

  public function __construct( $plugin_name, $version ) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;

    $this->GetContactsUrl = 'https://api.clientify.net/v1/contacts/';
    $this->PostContactsUrl = 'https://api.clientify.net/v1/contacts/';
    $this->PostDealsUrl = 'https://api.clientify.net/v1/deals/';
    $this->clientifykey = get_option( 'wpfunos_APIClientifyKeyClientify' );
    $this->wpfclientify_Admin_Master = new Wpfclientify_Admin_Master();

    add_action( 'wpfclientify-process-entry', array( $this,'WpfClientifyProcessEntry' ), 10, 1 );
  }
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpfclientify-admin.css', array(), $this->version, 'all' );
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpfclientify-admin.js', array( 'jquery' ), $this->version, false );
  }
  /*********************************/
  /*****  CRON                ******/
  /*********************************/

  /**
  * Register the Cron Job.
  */
  public function wpfclientifyCron() {
    $timeFirst  = strtotime('now');
    $this->custom_logs('==> Wpclientify hourly begins <==');
    $this->custom_logs('---');
    $this->wpfclientify_Admin_Master->wpfclientifyHourlyCron();
    $total = strtotime('now') - $timeFirst ;
    $this->custom_logs('==> Wpclientify hourly ends <== ' .$total.' sec.');
    $this->custom_logs('---');
  }

  /*********************************/
  /*****  HOOKS               ******/
  /*********************************/
  /**
  * add_filter( 'wpfclientify-process-entry'. array( $this,'WpfClientifyProcessEntry' ), 10, 1 )
  *
  *   "pipeline_stage": "https://api.clientify.net/v1/deal-pipeline-stages/58/",
  *   "pipeline": "https://api.clientify.net/v1/deal-pipelines/20/",
  */
  public function WpfClientifyProcessEntry( $params ){
    $direcciones = explode ( ",", get_option( 'wpfunos_APIClientifyExlcudedUsers' ) );
    foreach( $direcciones as $direccion ) {
      if( trim( $direccion ) == $params['email'] ) return;
    }

    if( get_option('wpfunos_APIClientifyActivaClientify') ){

      $userIP = apply_filters('wpfunos_userIP','dummy');

      do_action('wpfunos_log', $userIP.' - '.'==> ENVIO CLIENTIFY ' .$params['clientifyaction'] );
      $timeFirst  = strtotime('now');

      $contacts = $this->WpfClientifyShowContacts( array( "email" => $params['email'], "phone" => $params['phone'], "nombre" => $params['nombre'] )  );

      if( $contacts == ''){
        $contacts = $this->WpfClientifyCreateContact( array( "email" => $params['email'], "phone" => $params['phone'], "nombre" => $params['nombre'] )  );
        do_action('wpfunos_log', $userIP.' - '.'Nuevo usuario ' .$contacts );
      }else{
        do_action('wpfunos_log', $userIP.' - '.'Usuario ya existente ' .$contacts);
      }
      update_post_meta( $user_id, 'wpfunos_userClientifyIDusuario', $contacts );
      $params["contactID"] = $contacts;
      // Confirmar contacto recibido
      //
      //$confirmation = $this->WpfClientifyGetContact( $contacts );
      //
      $deals = $this->WpfClientifyCreateDeal( $params );
      update_post_meta( $user_id, 'wpfunos_userClientifyIDdeal', $deals );

      //
      // Cambiar pipelines
      //
      $pipeline = $this->WpfClientifyChangePipeline( $params );

      $total = strtotime('now') - $timeFirst ;
      do_action('wpfunos_log', $userIP.' - '.'==> ENVIO CLIENTIFY ' .$params['clientifyaction'].' END: '.$total.' sec.');
    }
  }

  /*********************************/
  /*****  CONTACTOS           ******/
  /*********************************/
  /**
  *
  */
  private function WpfClientifyShowContacts( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Mostrar contactos' );
    $timeFirst  = strtotime('now');

    $tel = str_replace( array( '-', '+34', ' ' ), '', $params['phone'] );

    $URLclientify = $this->GetContactsUrl.'?email=' .$params['email']. '&phone=' .$tel. '&first_name=' .$params['nombre'] ;
    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');

    do_action('wpfunos_log', $userIP.' - '.'Búsqueda: ' .$params['email']. ' - ' .$tel );

    $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify' ,'Error Clientify usuario '.$params['nombre']. ' email '.$params['email'] , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda: ' . $request['response']['message'] );

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Mostrar contactos END: '.$total.' sec.');
    return ( $bodyrequest->results[0]->id );
  }

  /**
  *
  */
  private function WpfClientifyCreateContact( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear contacto' );
    $timeFirst  = strtotime('now');

    $referer = get_transient( 'wpfunos-referer-' .$userIP );
    $utm = get_transient( 'wpfunos-query-' .$userIP );
    $tel = str_replace( array( '-', '+34', ' ' ), '', $params['phone'] );
    $pais = get_transient( 'geoip_' .$userIP );
    if( strlen($pais) < 1) $pais = 'ES';

    if( site_url() === 'https://dev.funos.es' ){
      $referer = 'https://dev.funos.es/';
    }

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "owner":          "'.sanitize_text_field( get_option( 'wpfunos_APIClientifyActionsUser' )).'",
      "first_name":     "'.sanitize_text_field( $params['nombre'] ).'",
      "email":          "'.sanitize_text_field( $params['email'] ). '",
      "phone":          "'.sanitize_text_field( $tel ).'",
      "contact_source": "'.sanitize_text_field( $referer ).'",
      "gdpr_accept":    "TRUE",
      "custom_fields": [';
      if( strlen( $utm['utm_source']   ) > 1 ) $body .= '{"field": "contactos_utm_source",   "value": "'.sanitize_text_field( $utm['utm_source'] ).  '"},';
      if( strlen( $utm['utm_medium']   ) > 1 ) $body .= '{"field": "contactos_utm_medium",   "value": "'.sanitize_text_field( $utm['utm_medium'] ).  '"},';
      if( strlen( $utm['utm_campaign'] ) > 1 ) $body .= '{"field": "contactos_utm_campaign", "value": "'.sanitize_text_field( $utm['utm_campaign'] ).'"},';
      if( strlen( $utm['utm_term']     ) > 1 ) $body .= '{"field": "contactos_utm_term",     "value": "'.sanitize_text_field( $utm['utm_term'] ).    '"},';
      $body .= '{"field": "contactos_pais", "value": "' .sanitize_text_field( $pais ). '"},{"field": "contactos_ip", "value": "' .sanitize_text_field( $userIP ). '"}]
    }';

    $request = wp_remote_post( $this->PostContactsUrl, array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));
    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify' ,'Error Clientify usuario '.sanitize_text_field( $params['nombre'] ). ' email '.sanitize_text_field( $params['email'] ) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    if( $request['response']['code'] != 201 ){
      do_action('wpfunos_log', $userIP.' - '.'$request[body]: ' . apply_filters('wpfunos_dumplog', $request['body']  ) );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación' ,'Error Clientify: ' .$request['response']['code']. ' usuario '.sanitize_text_field( $params['nombre'] ). ' email '.sanitize_text_field( $params['email'] ) , 'Content-Type: text/html; charset=UTF-8' );
    }

    $bodyrequest = json_decode( $request['body'] );
    do_action('wpfunos_log', $userIP.' - '.'Crear contacto: ' .$request['response']['message'] );

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear contacto END: '.$total.' sec.');
    return ( $bodyrequest->id );
  }

  /*********************************/
  /*****  DEALS               ******/
  /*********************************/
  /**
  *    "pipeline_desc": "nuevo",
  *  "pipeline_stage_desc": "ultima",
  */
  private function WpfClientifyCreateDeal( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear Deal' );
    $timeFirst  = strtotime('now');

    if( $params['cambios'] == 'distancia' ) return;

    $fechaentrega = new DateTime("now", new DateTimeZone('Europe/Madrid'));
    $fechaentrega->modify("+2 days");

    $amount = intval( str_replace( array('.', '€'), '', $params['precio'] ) );

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "name":  "'.sanitize_text_field( $params['clientifyaction'] ).'",
      "owner": "'.sanitize_text_field( get_option( 'wpfunos_APIClientifyActionsUser' )).'",
      "amount":"'.$amount.'",
      "contact":"https://api.clientify.net/v1/contacts/'.sanitize_text_field($params['contactID']).'/",
      "pipeline_desc": "'.sanitize_text_field( $params['pipeline'] ).'",
      "pipeline_stage_desc":"'.sanitize_text_field( $params['stage'] ).'",
      "deal_source": "'.sanitize_text_field( $params['form_name'] ).'",
      "expected_closed_date": "'.sanitize_text_field( $fechaentrega->format("Y-m-d") ). '",
      "custom_fields": [';
      if( strlen( $params['form_name']  ) > 1 ) $body .= '{"field": "oportunidades_origen",    "value": "'.sanitize_text_field( $params['form_name'] ). '"},';
      if( strlen( $params['ubicacion']  ) > 1 ) $body .= '{"field": "oportunidades_ubicacion", "value": "'.sanitize_text_field( $params['ubicacion'] ). '"},';
      if( strlen( $params['referencia'] ) > 1 ) $body .= '{"field": "oportunidades_referencia","value": "'.sanitize_text_field( $params['referencia'] ).'"},';
      if( strlen( $params['cuando']     ) > 1 ) $body .= '{"field": "oportunidades_cuando",    "value": "'.sanitize_text_field( $params['cuando'] ).    '"},';
      if( strlen( $params['destino']    ) > 1 ) $body .= '{"field": "oportunidades_destino",   "value": "'.sanitize_text_field( $params['destino'] ).   '"},';
      if( strlen( $params['ataud']      ) > 1 ) $body .= '{"field": "oportunidades_ataud",     "value": "'.sanitize_text_field( $params['ataud'] ).     '"},';
      if( strlen( $params['velatorio']  ) > 1 ) $body .= '{"field": "oportunidades_velatorio", "value": "'.sanitize_text_field( $params['velatorio'] ). '"},';
      if( strlen( $params['ceremonia']  ) > 1 ) $body .= '{"field": "oportunidades_ceremonia", "value": "'.sanitize_text_field( $params['ceremonia'] ). '"},';
      if( strlen( $params['origen']     ) > 1 ) $body .= '{"field": "oportunidades_origen",    "value": "'.sanitize_text_field( $params['origen'] ).    '"},';
      if( strlen( $params['precio']     ) > 1 ) $body .= '{"field": "oportunidades_precio",    "value": "'.sanitize_text_field( $params['precio'] ).    '"},';
      if( strlen( $params['nombreServicio']   ) > 1 ) $body .= '{"field": "oportunidades_nombreServicio",  "value": "'.sanitize_text_field( $params['nombreServicio'] ).  '"},';
      if( strlen( $params['nombreFuneraria']  ) > 1 ) $body .= '{"field": "oportunidades_nombreFuneraria", "value": "'.sanitize_text_field( $params['nombreFuneraria'] ). '"},';
      if( strlen( $params['telefonoServicio'] ) > 1 ) $body .= '{"field": "oportunidades_telefonoServicio","value": "'.sanitize_text_field( $params['telefonoServicio'] ).'"},';
      $body .= '{"field": "oportunidades_origen",    "value": "' .sanitize_text_field( $params['origen'] ). '"},{"field": "oportunidades_ip",    "value": "' .sanitize_text_field( $userIP ). '"}]
    }';
    $request = wp_remote_post( $this->PostDealsUrl, array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Deal' ,'Error Clientify Deal '.sanitize_text_field($params['nombre']). ' email '.sanitize_text_field($params['email']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    do_action('wpfunos_log', $userIP.' - '.'Crear Deal ' .$bodyrequest->id. ': ' .$request['response']['message'] );

    // TAG
    $body = '{"name":"' .sanitize_text_field( $params['origen'] ). '"}';
    $request = wp_remote_post( $this->PostDealsUrl.$bodyrequest->id.'/tags/' , array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Etiqueta' ,'Error Clientify Etiqueta '.sanitize_text_field($params['nombre']). ' email '.sanitize_text_field($params['email']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequesttag = json_decode( $request['body'] );
    do_action('wpfunos_log', $userIP.' - '.'Crear Tag ' .$bodyrequesttag->id. ': ' .$request['response']['message'] );

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear Deal END: '.$total.' sec.');
    return ( $bodyrequest->id );
  }

  /*********************************/
  /*****  UTILS               ******/
  /*********************************/
  /**
  * $confirmation = $this->WpfClientifyGetContact( $contacts );
  */
  private function WpfClientifyGetContact( $id ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    $URLclientify = $this->GetContactsUrl.$id. '/' ;

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
    $bodyrequest = json_decode( $request['body'] );

    do_action('wpfunos_log', $userIP.' - email=' .$bodyrequest->emails[0]->email. ' phone=' .$bodyrequest->phones[0]->phone. ' first_name=' .$bodyrequest->first_name);
  }

  /**
  * $this->WpfClientifyChangePipeline( $params );
  *
  */
  private function WpfClientifyChangePipeline( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Cambiar pipelines' );
    $timeFirst  = strtotime('now');

    $URLclientify = $this->GetContactsUrl.$params['contactID']. '/' ;

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
    $bodyrequest = json_decode( $request['body'] );

    $oportunidades = [];
    $funerarias = [];
    $descartadas = [];
    $aseguradoras = [];
    $deals = $bodyrequest->deals;
    foreach ($deals as $key => $deal) {

      foreach ($deal->custom_fields as $key => $value) {
        if( $value->field == 'oportunidades_origen') $origen = $value->value;
      }

      $oportunidades[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc);
      if( $deal->pipeline_desc == 'Servicios Funerarios')     $funerarias[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc, 'origen'  => $origen  );
      if( $deal->pipeline_desc== 'Oportunidades descartadas') $descartadas[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
      if( $deal->pipeline_desc == 'Aseguradoras')             $aseguradoras[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
    }

    //do_action('wpfunos_log', $userIP.' - '.'Cantidad oportunidades: ' .count($oportunidades). ' (Servicios Funerarios: ' .count($funerarias). ' Oportunidades descartadas:' .count($descartadas). ' Aseguradoras: ' .count($aseguradoras). ')' );

    //si tiene más de una oportunidad
    // SOLO QUEDA EL ÚLTIMO SI NO ES 'seleciono funeraria'
    foreach ($funerarias as $key => $funeraria) {
      if ( $key != 0 && $funeraria['stage'] != 'seleciono funeraria' ) $this->WpfClientifyUpdataDeal( array( "id" => $funeraria['id']) );
    }
    //si tiene más de una oportunidad END

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Cambiar pipelines END: '.$total.' sec.');
    return;
  }

  /**
  * $this->WpfClientifyUpdataDeal( $params );
  *
  *$params['id']
  */
  private function WpfClientifyUpdataDeal( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    $URLclientify = $this->PostDealsUrl.$params['id']. '/' ;

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "pipeline": "https://api.clientify.net/v1/deals/pipelines/46310/",
      "pipeline_stage":"https://api.clientify.net/v1/deals/pipelines/stages/196246/"
    }';
    $request = wp_remote_post( $URLclientify, array( 'headers' => $headers, 'body' => $body, 'method' => 'PATCH'  ) );

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify mover oportunidades' ,'Error Clientify mover oportunidades '.sanitize_text_field($params['id']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    do_action('wpfunos_log', $userIP.' - '.'Mover Deal '.$params['id']. ': ' .$request['response']['message']  );
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
