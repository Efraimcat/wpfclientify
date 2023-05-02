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

    //add_filter( 'wpfclientify-show-contacts', array( $this, 'WpfClientifyShowContacts' ), 10, 1 );
    //add_filter( 'wpfclientify-create-contact', array( $this, 'WpfClientifyCreateContact' ), 10, 1 );
    //add_filter( 'wpfclientify-create-deal', array( $this, 'WpfClientifyCreateDeal' ), 10, 1 );
    add_action( 'wpfclientify-process-entry', array( $this,'WpfClientifyProcessEntry' ), 10, 1 );
  }
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpfclientify-admin.css', array(), $this->version, 'all' );
  }

  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpfclientify-admin.js', array( 'jquery' ), $this->version, false );
  }

  /**
  * add_filter( 'wpfclientify-process-entry'. array( $this,'WpfClientifyProcessEntry' ), 10, 1 );
  */
  public function WpfClientifyProcessEntry( $params ){
    $direcciones = explode ( ",", get_option( 'wpfunos_APIClientifyExlcudedUsers' ) );
    foreach( $direcciones as $direccion ) {
      if( trim( $direccion ) == $params['email'] ) return;
    }

    if( get_option('wpfunos_APIClientifyActivaClientify') ){
      $clientifyaction = $params['clientifyaction'];
      $user_id = $params['user_id'];
      $email = $params['email'];
      $telefono = $params['phone']; ;
      $nombre = $params['nombre'];
      $form_name = $params['form_name'];
      $ubicacion = $params['ubicacion'];
      $referencia = $params['referencia'];
      $cuando = $params['cuando'];
      $destino = $params['destino'];
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
      do_action('wpfunos_log', $userIP.' - '.'$contacts: ' . $contacts );

      if( $contacts == ''){
        do_action('wpfunos_log', $userIP.' - '.'Nuevo usuario. ');
        $contacts = $this->WpfClientifyCreateContact( array( "email" => $email, "phone" => $telefono, "nombre" => $nombre )  );
      }else{
        do_action('wpfunos_log', $userIP.' - '.'Usuario ya existente');
      }

      do_action('wpfunos_log', $userIP.' - '.'ID usuario: ' . $contacts );
      update_post_meta( $user_id, 'wpfunos_userClientifyIDusuario', $contacts );

      $deals = $this->WpfClientifyCreateDeal( array(
        "email" => $email,
        "nombre" => $nombre,
        "clientID" => $contacts,
        "ubicacion" => $ubicacion,
        "referencia" => $referencia,
        "form_name" => $form_name,
        "cuando" => $cuando,
        "destino" => $destino,
        "velatorio" => $velatorio,
        "ceremonia" => $ceremonia,
        "origen" => $clientifyaction,
        "precio" => $precio,
        "nombreServicio" => $nombreServicio,
        "nombreFuneraria" => $nombreFuneraria,
        "telefonoServicio" => $telefonoServicio,
      ));
      do_action('wpfunos_log', $userIP.' - '.'ID deal: ' . $deals );
      update_post_meta( $user_id, 'wpfunos_userClientifyIDdeal', $deals );

      $total = strtotime('now') - $timeFirst ;
      do_action('wpfunos_log', $userIP.' - '.'==> Envio Clientify ' .$clientifyaction.' END: '.$total.' sec.');
    }
  }

  /**
  * add_filter( 'wpfclientify-show-contacts', array( $this, 'WpfClientifyShowContacts' ), 10, 1 );
  */
  public function WpfClientifyShowContacts( $params ){
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

    do_action('wpfunos_log', $userIP.' - '.'$URL: ' . $URLclientify  );
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
  * add_filter( 'wpfclientify-create-contact', array( $this, 'WpfClientifyCreateContact' ), 10, 1 );
  */
  public function WpfClientifyCreateContact( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear contacto' );
    $timeFirst  = strtotime('now');

    $email = $params['email'];
    $telefono = $params['phone']; ;
    $nombre = $params['nombre'];

    $referer = get_transient( 'wpfunos-referer-' .$userIP );
    $utm = get_transient( 'wpfunos-query-' .$userIP );
    $pais = get_transient( 'geoip_' .$userIP );

    $utm_source = ( $utm['utm_source'] != '' ) ? $utm['utm_source'] : '--' ;
    $utm_medium = ( $utm['utm_medium'] != '' ) ? $utm['utm_medium'] : '--' ;
    $utm_campaign = ( $utm['utm_campaign'] != '' ) ? $utm['utm_campaign'] : '--' ;
    $utm_term =  ( $utm['utm_term'] != '' ) ? $utm['utm_term'] : '--' ;

    $tel = str_replace(" ","", $telefono );
    $tel = str_replace("-","",$tel );
    $tel = str_replace("+34","",$tel );

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "owner":          "[owner]",
      "first_name":     "[first_name]",
      "email":          "[email]",
      "phone":          "[phone]",
      "contact_source": "[referer]",
      "gdpr_accept":    "TRUE",
      "custom_fields": [
        {"field": "(contactos)utm_source",  "value": "[utm_source]"},
        {"field": "(contactos)utm_medium",  "value": "[utm_medium]"},
        {"field": "(contactos)utm_campaign","value": "[utm_campaign]"},
        {"field": "(contactos)utm_term",    "value": "[utm_term]"},
        {"field": "(contactos)pais",        "value": "[pais]"}
      ]
    }';

    $body = str_replace ( '[first_name]' ,   $nombre ,       $body );
    $body = str_replace ( '[email]' ,        $email  ,       $body );
    $body = str_replace ( '[referer]' ,      $referer  ,     $body );
    $body = str_replace ( '[phone]' ,        $tel ,          $body );
    $body = str_replace ( '[utm_source]' ,   $utm_source ,   $body );
    $body = str_replace ( '[utm_medium]' ,   $utm_medium ,   $body );
    $body = str_replace ( '[utm_campaign]' , $utm_campaign , $body );
    $body = str_replace ( '[utm_term]' ,     $utm_term ,     $body );
    $body = str_replace ( '[pais]' ,         $pais ,         $body );
    $body = str_replace ( '[owner]' ,        get_option( 'wpfunos_APIClientifyActionsUser' ), $body );
    //do_action('wpfunos_log', $userIP.' - '.'$body: ' . $body );

    $request = wp_remote_post( $this->PostContactsUrl, array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));
    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify' ,'Error Clientify usuario '.$nombre. ' email '.$email , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    if( $request['response']['code'] != 201 ){
      do_action('wpfunos_log', $userIP.' - '.'$request[body]: ' . apply_filters('wpfunos_dumplog', $request['body']  ) );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación' ,'Error Clientify: ' .$request['response']['code']. ' usuario '.$nombre. ' email '.$email , 'Content-Type: text/html; charset=UTF-8' );
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

  /**
  * add_filter( 'wpfclientify-create-deal', array( $this, 'WpfClientifyCreateDeal' ), 10, 1 );
  */
  public function WpfClientifyCreateDeal( $params ){
    $userIP = apply_filters('wpfunos_userIP','dummy');
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear Deal' );
    $timeFirst  = strtotime('now');

    $form_name =  ( isset($params['form_name']))  ? $params['form_name']  : '';
    $clientID =   ( isset($params['clientID']))   ? $params['clientID']   : '';
    $email =      ( isset($params['email']))      ? $params['email']      : '';
    $nombre =     ( isset($params['nombre']))     ? $params['nombre']     : '';
    $ubicacion =  ( isset($params['ubicacion']))  ? $params['ubicacion']  : '';
    $referencia = ( isset($params['referencia'])) ? $params['referencia'] : '';
    $cuando =     ( isset($params['cuando']))     ? $params['cuando']     : '';
    $destino =    ( isset($params['destino']))    ? $params['destino']    : '';
    $velatorio =  ( isset($params['velatorio']))  ? $params['velatorio']  : '';
    $ceremonia =  ( isset($params['ceremonia']))  ? $params['ceremonia']  : '';
    $origen =     ( isset($params['origen']))     ? $params['origen']     : '';
    $precio =     ( isset($params['precio']))     ? $params['precio']     : '';
    $nombreServicio =    ( isset($params['nombreServicio']))       ? $params['nombreServicio']       : '';
    $nombreFuneraria =   ( isset($params['nombreFuneraria']))      ? $params['nombreFuneraria']      : '';
    $telefonoServicio =  ( isset($params['telefonoServicio']))     ? $params['telefonoServicio']     : '';

    $fechaentrega = new DateTime("now", new DateTimeZone('Europe/Madrid'));
    $fechaentrega->modify("+7 days");

    $headers = array( 'Authorization' => 'Token '.$this->clientifykey , 'Content-Type' => 'application/json');
    $body = '{
      "name":"[origen] de [first_name]",
      "owner": "[owner]",
      "amount":"100",
      "contact":"https://api.clientify.net/v1/contacts/[clientID]/",
      "deal_source": "[form_name]",
      "expected_closed_date": "[fechaentrega]",
      "custom_fields": [
        {"field": "(oportunidades)origen",    "value": "[form_name]"},
        {"field": "(oportunidades)ubicacion", "value": "[ubicacion]"},
        {"field": "(oportunidades)referencia","value": "[referencia]"},
        {"field": "(oportunidades)cuando",    "value": "[cuando]"},
        {"field": "(oportunidades)destino",   "value": "[destino]"},
        {"field": "(oportunidades)velatorio", "value": "[velatorio]"},
        {"field": "(oportunidades)ceremonia", "value": "[ceremonia]"},
        {"field": "(oportunidades)origen",    "value": "[origen]"},
        {"field": "(oportunidades)precio",    "value": "[precio]"},
        {"field": "(oportunidades)nombreServicio",  "value": "[nombreServicio]"},
        {"field": "(oportunidades)nombreFuneraria", "value": "[nombreFuneraria]"},
        {"field": "(oportunidades)telefonoServicio","value": "[telefonoServicio]"}
      ]
    }';

    $body = str_replace ( '[form_name]'    , $form_name ,  $body );
    $body = str_replace ( '[first_name]'   , $nombre ,     $body );
    $body = str_replace ( '[email]'        , $email ,      $body );
    $body = str_replace ( '[clientID]'     , $clientID ,   $body );
    $body = str_replace ( '[fechaentrega]' , $fechaentrega->format("Y-m-d") , $body );
    $body = str_replace ( '[ubicacion]'    , $ubicacion ,  $body );
    $body = str_replace ( '[referencia]'   , $referencia , $body );
    $body = str_replace ( '[cuando]'       , $cuando ,     $body );
    $body = str_replace ( '[destino]'      , $destino ,    $body );
    $body = str_replace ( '[velatorio]'    , $velatorio ,  $body );
    $body = str_replace ( '[ceremonia]'    , $ceremonia ,  $body );
    $body = str_replace ( '[origen]'       , $origen ,     $body );
    $body = str_replace ( '[precio]'       , $precio ,     $body );
    $body = str_replace ( '[nombreServicio]'   , $nombreServicio  ,  $body );
    $body = str_replace ( '[nombreFuneraria]'  , $nombreFuneraria ,  $body );
    $body = str_replace ( '[telefonoServicio]' , $telefonoServicio , $body );
    $body = str_replace ( '[owner]', get_option( 'wpfunos_APIClientifyActionsUser' ), $body );
    //do_action('wpfunos_log', $userIP.' - '.'$body: ' . $body );

    $request = wp_remote_post( $this->PostDealsUrl, array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Deal' ,'Error Clientify Deal '.$nombre. ' email '.$email , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    $bodyrequest = json_decode( $request['body'] );
    //do_action('wpfunos_log', $userIP.' - '.'$bodyrequest: ' . apply_filters('wpfunos_dumplog', $bodyrequest  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Deal $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );

    $body = '{"name":"[origen]"}';
    $body = str_replace ( '[origen]', $origen, $body );

    $request = wp_remote_post( $this->PostDealsUrl.$bodyrequest->id.'/tags/' , array( 'headers' => $headers, 'body' => $body,'method' => 'POST' ));

    if ( is_wp_error($request) ) {
      do_action('wpfunos_log', $userIP.' - '.'is_wp_error'  );
      wp_mail ( 'efraim@efraim.cat' , 'Error Clientify Creación Etiqueta' ,'Error Clientify Etiqueta '.$nombre. ' email '.$email , 'Content-Type: text/html; charset=UTF-8' );
      exit;
    }
    do_action('wpfunos_log', $userIP.' - '.'Tag $request[response][code]: ' . apply_filters('wpfunos_dumplog', $request['response']['code']  ) );
    do_action('wpfunos_log', $userIP.' - '.'Tag $request[response][message]: ' . apply_filters('wpfunos_dumplog', $request['response']['message']  ) );

    $total = strtotime('now') - $timeFirst ;
    do_action('wpfunos_log', $userIP.' - '.'==> Clientify Crear Deal END: '.$total.' sec.');
    return ( $bodyrequest->id );
  }


}
