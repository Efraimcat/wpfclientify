<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://efraim.cat
 * @since      1.0.0
 *
 * @package    Wpfclientify
 * @subpackage Wpfclientify/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<!-- DEAL
$bodyrequest: Object (stdClass)
 url -> String: 'https://api.clientify.net/v1/deals/5037247/'
 id -> Number: 5037247
 owner -> String: 'efraim@efraim.cat'
 owner_name -> String: 'Efraim Bayarri'
 owner_picture -> NULL
 name -> String: 'Nuevo deal para Efraim Bayarri efraim.bayarri@gmail.com'
 amount -> Number: 100.00
 amount_user -> NULL
 currency -> String: 'EUR'
 contact -> String: 'https://api.clientify.net/v1/contacts/43741985/'
 contact_name -> String: 'Efraim Bayarri'
 contact_email -> String: 'efraim.bayarri@gmail.com'
 contact_phone -> Number: 690158670
 contact_medium -> NULL
 contact_source -> NULL
 created -> String: '2023-04-30T10:22:13.745722+02:00'
 modified -> String: '2023-04-30T10:22:13.863233+02:00'
 expected_closed_date -> String: '2023-05-07'
 actual_closed_date -> NULL
 company -> NULL
 source -> NULL
 deal_source -> String: 'TeLlamamosGratis'
 status -> Number: 1
 stages_duration -> </br> |   [0] = Object (stdClass)</br> |   |   stage_name -> String: '⭐️ Nuevo Interesado'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 0</br> |   [1] = Object (stdClass)</br> |   |   stage_name -> String: '😓 No respondio'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 1</br> |   [2] = Object (stdClass)</br> |   |   stage_name -> String: '🙋🏻 Primer contacto'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 2</br> |   [3] = Object (stdClass)</br> |   |   stage_name -> String: '🍝 Para nutrir'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 3</br> |   [4] = Object (stdClass)</br> |   |   stage_name -> String: '👉🏻 Funeraria Asignada'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 4</br> |   [5] = Object (stdClass)</br> |   |   stage_name -> String: '☎️ [FUNERARIA] Llamar al cliente'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 5</br> |   [6] = Object (stdClass)</br> |   |   stage_name -> String: '📊[FUNERARIA]Enviar presupuesto'</br> |   |   stage_duration -> Object (stdClass)</br> |   |   |   hours -> Number: 0</br> |   |   |   minutes -> Number: 0</br> |   |   |   days -> Number: 0</br> |   |   stage_position -> Number: 6
 status_desc -> String: 'Open'
 probability -> Number: 0
 probability_desc -> String: '0%'
 who_can_view -> Number: 1
 pipeline_stage -> String: 'https://api.clientify.net/v1/deals/pipelines/stages/194909/'
 pipeline_stage_desc -> String: '⭐️ Nuevo Interesado'
 pipeline -> String: 'https://api.clientify.net/v1/deals/pipelines/46018/'
 pipeline_desc -> String: 'Proceso de Venta'
 involved_contacts ->
 tags ->
 remarks -> String: ''
 involved_companies ->
 wall_entries -> </br> |   [0] = Object (stdClass)</br> |   |   url -> String: 'https://api.clientify.net/v1/wall-entries/1364344314/'</br> |   |   id -> Number: 1364344314</br> |   |   user -> String: 'Efraim Bayarri'</br> |   |   created -> String: '2023-04-30T10:22:13.852501+02:00'</br> |   |   extra -> String: '{"deal_name": "Nuevo deal para Efraim Bayarri efraim.bayarri@gmail.com", "deal_stage": "\u2b50\ufe0f Nuevo Interesado", "deal_link": "/deals/5037247/", "deal_amount": "100.00", "deal_status": "Open"}'</br> |   |   extra_datetime -> String: '2023-05-08T02:14:00+02:00'</br> |   |   type -> String: 'deal_creation'</br> |   |   link -> String: ''</br> |   |   source_id -> Number: 5037247</br> |   |   object_id -> Number: 5037247
 tasks ->
 events ->
 custom_fields -> </br> |   [0] = Object (stdClass)</br> |   |   field -> String: '(oportunidades)origen'</br> |   |   value -> String: 'TeLlamamosGratis'</br> |   |   field_detail -> Object (stdClass)</br> |   |   |   id -> Number: 359365</br> |   |   |   field_type -> Number: 3</br> |   |   |   dropdown_choices ->
 products ->
 integrations -> '
-->

<!-- CONTACTO
$bodyrequest: Object (stdClass)
 url -> String: 'https://api.clientify.net/v1/contacts/43804930/'
 id -> Number: 43804930
 owner -> String: 'efraim@efraim.cat'
 owner_id -> Number: 103855
 owner_name -> String: 'Efraim Bayarri'
 first_name -> String: 'mariam test'
 last_name -> String: ''
 status -> String: 'cold-lead'
 title -> String: ''
 company -> NULL
 company_details -> NULL
 contact_type -> NULL
 contact_source -> NULL
 emails -> </br> |   [0] = Object (stdClass)</br> |   |   id -> Number: 38021449</br> |   |   type -> Number: 4</br> |   |   email -> String: 'alejandrahamber@gmail.com'
 phones -> </br> |   [0] = Object (stdClass)</br> |   |   id -> Number: 27138740</br> |   |   type -> Number: 1</br> |   |   phone -> Number: 919930954
 addresses ->
 picture_url -> NULL
 custom_fields ->
 tags ->
 description -> String: ''
 remarks -> String: ''
 summary -> String: ''
 created -> String: '2023-04-29T18:02:53.179188+02:00'
 modified -> String: '2023-04-29T18:02:53.261215+02:00'
 last_contact -> NULL
 related_tasks ->
 deals ->
 wall_entries -> </br> |   [0] = Object (stdClass)</br> |   |   url -> String: 'https://api.clientify.net/v1/wall-entries/1363814245/'</br> |   |   id -> Number: 1363814245</br> |   |   user -> String: 'Efraim Bayarri'</br> |   |   created -> String: '2023-04-29T18:02:53.265897+02:00'</br> |   |   extra -> String: '{"creation_type": "api"}'</br> |   |   extra_datetime -> NULL</br> |   |   type -> String: 'contact_creation'</br> |   |   link -> String: ''</br> |   |   source_id -> Number: 43804930</br> |   |   object_id -> Number: 43804930</br> |   [1] = Object (stdClass)</br> |   |   url -> String: 'https://api.clientify.net/v1/wall-entries/1363814244/'</br> |   |   id -> Number: 1363814244</br> |   |   user -> String: 'Efraim Bayarri'</br> |   |   created -> String: '2023-04-29T18:02:53.249440+02:00'</br> |   |   extra -> String: '{}'</br> |   |   extra_datetime -> NULL</br> |   |   type -> String: 'gdpr_acceptance'</br> |   |   link -> String: ''</br> |   |   source_id -> Number: 43804930</br> |   |   object_id -> Number: 43804930
 page_views -> Number: 0
 total_visits -> Number: 0
 first_visit -> NULL
 last_visit -> NULL
 gdpr_accept -> TRUE
 visitor_key -> String: ''
 attachments ->
 websites ->
 medium -> String: ''
 linkedin_url -> String: ''
 linkedin_id -> String: ''
 linkedin_picture_url -> String: ''
 skype_username -> String: ''
 birthday -> NULL
 twitter_id -> String: ''
 lead_scoring -> Number: 0
 facebook_url -> String: ''
 twitter_url -> String: ''
 googleplus_url -> String: ''
 pinterest_url -> String: ''
 foursquare_url -> String: ''
 aboutme_url -> String: ''
 klout_url -> String: ''
 instagram_url -> String: ''
 manychat_url -> NULL
 facebook_picture_url -> String: ''
 twitter_picture_url -> String: ''
 facebook_id -> String: ''
 google_id -> String: ''
 taxpayer_identification_number -> String: ''
 unsubscribed -> FALSE
 automations ->
 gdpr_acceptance_date -> String: '2023-04-29T18:02:53.249440+02:00'
 country -> String: ''
 integrations -> '

 -->


<!--
 $bodyrequest: Object (stdClass)
 count -> Number: 1
 next -> NULL
 previous -> NULL
 results -> </br>
 |   [0] = Object (stdClass)</br>
 |   |   url -> String: 'https://api.clientify.net/v1/contacts/43741985/'</br>
 |   |   id -> Number: 43741985</br>
 |   |   owner -> String: 'efraim@efraim.cat'</br>
 |   |   owner_name -> String: 'Efraim Bayarri'</br>
 |   |   first_name -> String: 'Efraim Bayarri'</br>
 |   |   last_name -> String: ''</br>
 |   |   status -> String: 'cold-lead'</br>
 |   |   title -> String: ''</br>
 |   |   company -> NULL</br>
 |   |   taxpayer_identification_number -> String: ''</br>
 |   |   medium -> String: ''</br>
 |   |   contact_source -> NULL</br>
 |   |   emails -> </br>
 |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   id -> Number: 37978010</br>
 |   |   |   |   type -> Number: 4</br>
 |   |   |   |   email -> String: 'efraim.bayarri@gmail.com'</br>
 |   |   phones -> </br>
 |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   id -> Number: 27093873</br>
 |   |   |   |   type -> Number: 1</br>
 |   |   |   |   phone -> Number: 690158670</br>
 |   |   picture_url -> NULL</br>
 |   |   custom_fields -> </br>
 |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   field -> String: '[Comparador] ¿Tipo de ceremonia?'</br>
 |   |   |   |   value -> String: 'Solo sala'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 358668</br>
 |   |   |   |   |   field_type -> Number: 1</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   |   |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Sin ceremonia'</br>
 |   |   |   |   |   |   [1] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Solo sala'</br>
 |   |   |   |   |   |   [2] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Ceremonia civil'</br>
 |   |   |   |   |   |   [3] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Ceremonia religiosa'</br>
 |   |   |   [1] = Object (stdClass)</br>
 |   |   |   |   field -> String: '[Comparador] ¿Con o sin velatorio?'</br>
 |   |   |   |   value -> String: 'Velatorio'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 358667</br>
 |   |   |   |   |   field_type -> Number: 1</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   |   |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Con Velatorio'</br>
 |   |   |   |   |   |   [1] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Sin Velatorio'</br>
 |   |   |   [2] = Object (stdClass)</br>
 |   |   |   |   field -> String: '[Comparador] ¿Entierro o incineración?'</br>
 |   |   |   |   value -> String: 'Incineración'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 358666</br>
 |   |   |   |   |   field_type -> Number: 1</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   |   |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Entierro'</br>
 |   |   |   |   |   |   [1] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Incineración'</br>
 |   |   |   [3] = Object (stdClass)</br>
 |   |   |   |   field -> String: '[Comparador] ¿Cuando vas a tomar el servicio?'</br>
 |   |   |   |   value -> String: 'Ahora'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 358665</br>
 |   |   |   |   |   field_type -> Number: 1</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   |   |   |   |   [0] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Ahora mismo'</br>
 |   |   |   |   |   |   [1] = Object (stdClass)</br>
 |   |   |   |   |   |   |   name -> String: 'Próximamente'</br>
 |   |   |   [4] = Object (stdClass)</br>
 |   |   |   |   field -> String: 'Ubicación buscada'</br>
 |   |   |   |   value -> String: 'Barcelona'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 358660</br>
 |   |   |   |   |   field_type -> Number: 3</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   |   [5] = Object (stdClass)</br>
 |   |   |   |   field -> String: 'Referencia Funos'</br>
 |   |   |   |   value -> String: 'funos-327064484'</br>
 |   |   |   |   field_detail -> Object (stdClass)</br>
 |   |   |   |   |   id -> Number: 356456</br>
 |   |   |   |   |   field_type -> Number: 3</br>
 |   |   |   |   |   dropdown_choices -> </br>
 |   |   tags -> </br>
 |   |   created -> String: '2023-04-28T12:33:58.161162+02:00'</br>
 |   |   modified -> String: '2023-04-28T12:36:52.355944+02:00'</br>
 |   |   last_contact -> NULL'
  -->

<!-- WEBHOOK
{
  "hook": {
    "event": "deal.saved",
    "target": "https://webhook.site/8a556893-a0ec-4b8f-a081-256a215ecc6a",
    "id": 3657
  },
  "data": {
    "tasks": [],
    "probability": "0%",
    "pipeline_stage": "Servicios funerarios descartados",
    "expected_closed_date": "2023-05-14T00:00:00",
    "owner": "Alejandro López",
    "id": 5104288,
    "custom_fields": [
      {
        "field": "(oportunidades)ip",
        "value": "80.29.72.33"
      },
      {
        "field": "(oportunidades)origen",
        "value": "Comparador funerarias"
      },
      {
        "field": "(oportunidades)ceremonia",
        "value": "Solo sala"
      },
      {
        "field": "(oportunidades)velatorio",
        "value": "Sin velatorio"
      },
      {
        "field": "(oportunidades)ataud",
        "value": "Ataúd económico"
      },
      {
        "field": "(oportunidades)destino",
        "value": "Incineración"
      },
      {
        "field": "(oportunidades)cuando",
        "value": "Proximamente"
      },
      {
        "field": "(oportunidades)referencia",
        "value": "funos-1139467810"
      },
      {
        "field": "(oportunidades)ubicacion",
        "value": "Denia/Dènia"
      }
    ],
    "involved_contacts": [],
    "source": null,
    "contact_phone": "619830900",
    "events": [],
    "status": "Abierta",
    "tags": [
      "comparador funerarias"
    ],
    "company": "",
    "contact_email": "sandraykeke08@gmail.com",
    "remarks": "",
    "pipeline": "Oportunidades descartadas",
    "name": "Comparador funerarias",
    "created": "2023-05-07T18:02:41.384893Z",
    "amount": "0,00 €",
    "contact": "Sandra",
    "involved_companies": []
  }
}
-->
