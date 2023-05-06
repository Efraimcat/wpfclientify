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

    add_action( 'wpfclientify-process-entry', array( $this,'WpfClientifyProcessEntry' ), 10, 1 );
  }
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpfclientify-admin.css', array(), $this->version, 'all' );
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpfclientify-admin.js', array( 'jquery' ), $this->version, false );
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
      $clientifyaction = $params['clientifyaction'];
      $pipeline = $params['pipeline'];
      $stage = $params['stage'];
      $cambios = $params['cambios'];
      $user_id = $params['user_id'];
      $email = $params['email'];
      $telefono = $params['phone'];
      $nombre = $params['nombre'];
      $form_name = $params['form_name'];
      $ubicacion = $params['ubicacion'];
      $referencia = $params['referencia'];
      $cuando = $params['cuando'];
      $destino = $params['destino'];
      $ataud = $params['ataud'];
      $velatorio = $params['velatorio'];
      $ceremonia = $params['ceremonia'];
      $origen = $params['origen'];
      $precio = $params['precio'];
      $nombreServicio = $params['nombreServicio'];
      $nombreFuneraria = $params['nombreFuneraria'];
      $telefonoServicio = $params['telefonoServicio'];

      $userIP = apply_filters('wpfunos_userIP','dummy');

      do_action('wpfunos_log', $userIP.' - '.'==> Envio Clientify ' .$clientifyaction );
      $timeFirst  = strtotime('now');

      $contacts = $this->WpfClientifyShowContacts( array( "email" => $email, "phone" => $telefono, "nombre" => $nombre )  );
      //do_action('wpfunos_log', $userIP.' - '.'$contacts: ' . $contacts );

      if( $contacts == ''){
        do_action('wpfunos_log', $userIP.' - '.'Nuevo usuario. ');
        $contacts = $this->WpfClientifyCreateContact( array( "email" => $email, "phone" => $telefono, "nombre" => $nombre )  );
      }else{
        do_action('wpfunos_log', $userIP.' - '.'Usuario ya existente');
      }

      do_action('wpfunos_log', $userIP.' - '.'ID usuario: ' . $contacts );
      update_post_meta( $user_id, 'wpfunos_userClientifyIDusuario', $contacts );

      // Confirmar contacto recibido
      //
      //$confirmation = $this->WpfClientifyGetContact( $contacts );
      //

      $params = array(
        "pipeline" => $pipeline,
        "stage" => $stage,
        "email" => $email,
        "cambios" => $cambios,
        "nombre" => $nombre,
        "contactID" => $contacts,
        "ubicacion" => $ubicacion,
        "referencia" => $referencia,
        "form_name" => $form_name,
        "cuando" => $cuando,
        "destino" => $destino,
        "ataud" => $ataud,
        "velatorio" => $velatorio,
        "ceremonia" => $ceremonia,
        "origen" => $clientifyaction,
        "precio" => $precio,
        "nombreServicio" => $nombreServicio,
        "nombreFuneraria" => $nombreFuneraria,
        "telefonoServicio" => $telefonoServicio,
      );
      $deals = $this->WpfClientifyCreateDeal( $params );
      do_action('wpfunos_log', $userIP.' - '.'ID deal: ' . $deals );
      update_post_meta( $user_id, 'wpfunos_userClientifyIDdeal', $deals );

      //
      // Cambiar pipelines
      //
      $params = array(
        "pipeline" => $pipeline,
        "stage" => $stage,
        "email" => $email,
        "cambios" => $cambios,
        "nombre" => $nombre,
        "contactID" => $contacts,
      );
      $pipeline = $this->WpfClientifyChangePipeline( $params );
      //
      // Cambiar pipelines
      //


      $total = strtotime('now') - $timeFirst ;
      do_action('wpfunos_log', $userIP.' - '.'==> Envio Clientify ' .$clientifyaction.' END: '.$total.' sec.');
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

    $email = $params['email'];
    $telefono = $params['phone']; ;
    $nombre = $params['nombre'];

    $tel = str_replace(" ","", $telefono );
    $tel = str_replace("-","",$tel );
    $tel = str_replace("+34","",$tel );

    $URLclientify = $this->GetContactsUrl.'?email=' .$email. '&phone=' .$tel. '&first_name=' .$nombre ;
    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');

    do_action('wpfunos_log', $userIP.' - '.'Búsqueda: ' .$email. ' - ' .$tel );
    //do_action('wpfunos_log', $userIP.' - '.'$URL: ' . $URLclientify  );
    //do_action('wpfunos_log', $userIP.' - '.'$headers: ' . apply_filters('wpfunos_dumplog', $headers   ) );

    $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify' ,'Error Clientify usuario '.$nombre. ' email '.$email , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    //[response] = </br> |   [code] = Number: 200</br> |   [message] = String: 'OK'
    $bodyrequest = json_decode( $request['body'] );
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $bodyrequest->count: ' . apply_filters('wpfunos_dumplog',  $bodyrequest->count  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $bodyrequest->results[0]->id: ' . apply_filters('wpfunos_dumplog', $bodyrequest->results[0]->id ) );

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
    $pais = get_transient( 'geoip_' .$userIP );
    if( strlen($pais) < 1) $pais = 'ES';

    if( site_url() === 'https://dev.funos.es' ){
      $referer = 'https://dev.funos.es/';
    }

    $tel = str_replace(" ","", $params['phone'] );
    $tel = str_replace("-","",$tel );
    $tel = str_replace("+34","",$tel );

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "owner":          "'.sanitize_text_field( get_option( 'wpfunos_APIClientifyActionsUser' )).'",
      "first_name":     "'.sanitize_text_field( $params['nombre'] ).'",
      "email":          "'.sanitize_text_field( $params['email'] ). '",
      "phone":          "'.sanitize_text_field( $tel ).'",
      "contact_source": "'.sanitize_text_field( $referer ).'",
      "gdpr_accept":    "TRUE",
      "custom_fields": [';
      if( strlen( $utm['utm_source']   ) > 1 ) $body .= '{"field": "(contactos)utm_source",   "value": "'.sanitize_text_field( $utm['utm_source'] ).  '"},';
      if( strlen( $utm['utm_medium']   ) > 1 ) $body .= '{"field": "(contactos)utm_medium",   "value": "'.sanitize_text_field( $utm['utm_medium'] ).  '"},';
      if( strlen( $utm['utm_campaign'] ) > 1 ) $body .= '{"field": "(contactos)utm_campaign", "value": "'.sanitize_text_field( $utm['utm_campaign'] ).'"},';
      if( strlen( $utm['utm_term']     ) > 1 ) $body .= '{"field": "(contactos)utm_term",     "value": "'.sanitize_text_field( $utm['utm_term'] ).    '"},';
      $body .= '{"field": "(contactos)pais", "value": "' .sanitize_text_field( $pais ). '"},{"field": "(contactos)ip", "value": "' .sanitize_text_field( $userIP ). '"}]
    }';
    //do_action('wpfunos_log', $userIP.' - '.'$body: ' . $body );

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
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Búsqueda $bodyrequest->id: ' . apply_filters('wpfunos_dumplog', $bodyrequest->id ) );

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
    $fechaentrega->modify("+7 days");

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "name":  "'.sanitize_text_field( $params['origen'] ). ' de '.sanitize_text_field($params['nombre']).'",
      "owner": "'.sanitize_text_field( get_option( 'wpfunos_APIClientifyActionsUser' )).'",
      "amount":"100",
      "contact":"https://api.clientify.net/v1/contacts/'.sanitize_text_field($params['contactID']).'/",
      "pipeline_desc": "'.sanitize_text_field( $params['pipeline'] ).'",
      "pipeline_stage_desc":"'.sanitize_text_field( $params['stage'] ).'",
      "deal_source": "'.sanitize_text_field( $params['form_name'] ).'",
      "expected_closed_date": "'.sanitize_text_field( $fechaentrega->format("Y-m-d") ). '",
      "custom_fields": [';
      if( strlen( $params['form_name']  ) > 1 ) $body .= '{"field": "(oportunidades)origen",    "value": "'.sanitize_text_field( $params['form_name'] ). '"},';
      if( strlen( $params['ubicacion']  ) > 1 ) $body .= '{"field": "(oportunidades)ubicacion", "value": "'.sanitize_text_field( $params['ubicacion'] ). '"},';
      if( strlen( $params['referencia'] ) > 1 ) $body .= '{"field": "(oportunidades)referencia","value": "'.sanitize_text_field( $params['referencia'] ).'"},';
      if( strlen( $params['cuando']     ) > 1 ) $body .= '{"field": "(oportunidades)cuando",    "value": "'.sanitize_text_field( $params['cuando'] ).    '"},';
      if( strlen( $params['destino']    ) > 1 ) $body .= '{"field": "(oportunidades)destino",   "value": "'.sanitize_text_field( $params['destino'] ).   '"},';
      if( strlen( $params['ataud']      ) > 1 ) $body .= '{"field": "(oportunidades)ataud",     "value": "'.sanitize_text_field( $params['ataud'] ).     '"},';
      if( strlen( $params['velatorio']  ) > 1 ) $body .= '{"field": "(oportunidades)velatorio", "value": "'.sanitize_text_field( $params['velatorio'] ). '"},';
      if( strlen( $params['ceremonia']  ) > 1 ) $body .= '{"field": "(oportunidades)ceremonia", "value": "'.sanitize_text_field( $params['ceremonia'] ). '"},';
      if( strlen( $params['origen']     ) > 1 ) $body .= '{"field": "(oportunidades)origen",    "value": "'.sanitize_text_field( $params['origen'] ).    '"},';
      if( strlen( $params['precio']     ) > 1 ) $body .= '{"field": "(oportunidades)precio",    "value": "'.sanitize_text_field( $params['precio'] ).    '"},';
      if( strlen( $params['nombreServicio']   ) > 1 ) $body .= '{"field": "(oportunidades)nombreServicio",  "value": "'.sanitize_text_field( $params['nombreServicio'] ).  '"},';
      if( strlen( $params['nombreFuneraria']  ) > 1 ) $body .= '{"field": "(oportunidades)nombreFuneraria", "value": "'.sanitize_text_field( $params['nombreFuneraria'] ). '"},';
      if( strlen( $params['telefonoServicio'] ) > 1 ) $body .= '{"field": "(oportunidades)telefonoServicio","value": "'.sanitize_text_field( $params['telefonoServicio'] ).'"},';
      $body .= '{"field": "(oportunidades)origen",    "value": "' .sanitize_text_field( $params['origen'] ). '"},{"field": "(oportunidades)ip",    "value": "' .sanitize_text_field( $userIP ). '"}]
    }';
    //do_action('wpfunos_log', $userIP.' - '.'$body: ' . $body );
    $request = wp_remote_post( $this->PostDealsUrl, array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Deal' ,'Error Clientify Deal '.sanitize_text_field($params['nombre']). ' email '.sanitize_text_field($params['email']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );

    $body = '{"name":"' .sanitize_text_field( $params['origen'] ). '"}';
    $request = wp_remote_post( $this->PostDealsUrl.$bodyrequest->id.'/tags/' , array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Etiqueta' ,'Error Clientify Etiqueta '.sanitize_text_field($params['nombre']). ' email '.sanitize_text_field($params['email']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    do_action('wpfunos_log', $userIP.' - '.'Tag $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Tag $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );

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
    //do_action('wpfunos_log', $userIP.' - '.'$URL: ' . $URLclientify  );

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $request = wp_remote_post( $URLclientify, array( 'method' => 'GET', 'headers' => $headers, 'timeout' => 45  ) );
    $bodyrequest = json_decode( $request['body'] );
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );

    $email = $bodyrequest->emails[0]->email;
    $name = $bodyrequest->first_name;
    $phone = $bodyrequest->phones[0]->phone;

    do_action('wpfunos_log', $userIP.' - email=' .$email. ' phone=' .$phone. ' first_name=' .$name);
  }

  /**
  * $this->WpfClientifyChangePipeline( $params );
  *
  *$params = array(
  *  "pipeline" => $pipeline,
  *  "stage" => $stage,
  *  "email" => $email,
  *  "cambios" => $cambios,
  *  "nombre" => $nombre,
  *  "contactID" => $contacts,
  *);
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
      $oportunidades[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
      if( $deal->pipeline_desc == 'Servicios Funerarios')     $funerarias[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
      if( $deal->pipeline_desc== 'Oportunidades descartadas') $descartadas[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
      if( $deal->pipeline_desc == 'Aseguradoras')             $aseguradoras[] = array( 'id' => $deal->id, 'pipeline' => $deal->pipeline_desc, 'stage' => $deal->pipeline_stage_desc );
    }

    //do_action('wpfunos_log', $userIP.' - '.'$oportunidades: ' . apply_filters('wpfunos_dumplog', $oportunidades  ) );
    /**
    *[0] = </br> |   [id] = Number: 5082716</br> |   [pipeline] = String: 'Aseguradoras'</br> |   [stage] = String: 'Nuevo interesado'
    *[1] = </br> |   [id] = Number: 5082543</br> |   [pipeline] = String: 'Aseguradoras'</br> |   [stage] = String: 'Nuevo interesado'
    *[2] = </br> |   [id] = Number: 5082421</br> |   [pipeline] = String: 'Aseguradoras'</br> |   [stage] = String: 'Nuevo interesado'
    *[3] = </br> |   [id] = Number: 5082304</br> |   [pipeline] = String: 'Servicios Funerarios'</br> |   [stage] = String: 'comparador funerarias''
    */

    do_action('wpfunos_log', $userIP.' - '.'Cantidad oportunidades: ' .count($oportunidades). ' (Servicios Funerarios: ' .count($funerarias). ' Oportunidades descartadas:' .count($descartadas). ' Aseguradoras: ' .count($aseguradoras). ')' );

    //si tiene más de una oportunidad
    // SOLO QUEDA EL ÚLTIMO
    foreach ($funerarias as $key => $funeraria) {
      if ( $key != 0) $this->WpfClientifyUpdataDeal( array( "id" => $funeraria['id']) );
    }
    //si tiene más de una oportunidad END

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Cambiar pipelines END: '.$total.' sec.');
    //return ( $bodyrequest->id );
    return;
  }

  /**
  * $this->WpfClientifyUpdataDeal( $params );
  *
  *$params['id']
  */
  private function WpfClientifyUpdataDeal( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'ID: ' . $params['id'] );
    $URLclientify = $this->PostDealsUrl.$params['id']. '/' ;
    //do_action('wpfunos_log', $userIP.' - '.'$URL: ' . $URLclientify  );

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "pipeline": "https://api.clientify.net/v1/deals/pipelines/46310/",
      "pipeline_stage":"https://api.clientify.net/v1/deals/pipelines/stages/196246/"
    }';

    /**curl --location --request PATCH 'https://api.clientify.net/v1/deals/5089205/' \
    --header 'Authorization: Token 4a02e84dc80e316cca5decab08f0839469f92e95' \
    --header 'Content-Type: application/json' \
    --data '{ "pipeline": "https://api.clientify.net/v1/deals/pipelines/46310/","pipeline_stage":"https://api.clientify.net/v1/deals/pipelines/stages/196246/" }'
    */

    $request = wp_remote_post( $URLclientify, array( 'headers' => $headers, 'body' => $body, 'method' => 'PATCH'  ) );

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify mover oportunidades' ,'Error Clientify mover oportunidades '.sanitize_text_field($params['id']) , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );
  }



}
