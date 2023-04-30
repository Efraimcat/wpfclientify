<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}
/**
* Provide a admin area view for the plugin
*
* This file is used to markup the admin-facing aspects of the plugin.
*
* @link       https://efraim.cat
* @since      1.0.0
*
* @package    Wpfapi
* @subpackage Wpfapi/admin/partials
*/

(oportunidades)origen
(oportunidades)ceremonia
(oportunidades)velatorio
(oportunidades)destino
(oportunidades)contratante
(oportunidades)difunto
(oportunidades)cuando
(oportunidades)ubicacion
(oportunidades)referencia
(oportunidades)precio
(oportunidades)nombreServicio
(oportunidades)nombreFuneraria
(oportunidades)telefonoServicio


// referencia.Tipo,ubicación,cuando,destino,velatorio,ceremonia
{
  "name":"Nuevo deal con pipeline desc y pipeline stage, fecha y custom fields",
  "amount":"11.33",
  "contact":"https://api.clientify.net/v1/contacts/integer/",
  "company":"https://api.clientify.net/v1/companies/integer/",
  "pipeline_desc": "nuevo",
  "pipeline_stage_desc": "ultima",
  "source": 3,
  "deal_source": "Correo electrónico",
  "expected_closed_date": "2019-11-30",
  "custom_fields": [{"field": "quaderno_id","value": "cdn_1133"}]
}

require_once 'HTTP/Request2.php';
$request = new HTTP_Request2();
$request->setUrl('https://api.clientify.net/v1/deals/');
$request->setMethod(HTTP_Request2::METHOD_POST);
$request->setConfig(array(
'follow_redirects' => TRUE
));
$request->setHeader(array(
'Authorization' => 'Token YOUR_CLIENTIFY_API_KEY',
'Content-Type' => 'application/json'
));


$request->setBody('{\n
  "name":"Staging deal from Postman",\n
  "amount":"23452",\n
  "contact":"https://api.clientify.net/v1/contacts/integer/",\n
  "company":"https://api.clientify.net/v1/companies/integer/",\n
  "pipeline":"https://api.clientify.net/v1/deal-pipelines/integer/",\n
  "pipelineStage":"https://api.clientify.net/v1/deal-pipeline-stages/{{deal-pipeline-stages-id}}/"\n
}');
