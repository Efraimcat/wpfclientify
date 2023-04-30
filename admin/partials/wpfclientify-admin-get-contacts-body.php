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
$body = '{
  "first_name": "[first_name]",
  "email": "[email]",
  "phone": "[phone]",
  "gdpr_accept": "TRUE",
  "custom_fields": [
    {"field":"Referencia Funos","value":"[referencia]"},
    {"field":"Ubicación buscada","value":"[ubicacion]"},
    {"field":"[Comparador] ¿Cuando vas a tomar el servicio?","value":"[cuando]"},
    {"field":"[Comparador] ¿Entierro o incineración?","value":"[destino]"},
    {"field":"[Comparador] ¿Con o sin velatorio?","value":"[velatorio]"},
    {"field":"[Comparador] ¿Tipo de ceremonia?","value":"[ceremonia]"}
  ]
}';
