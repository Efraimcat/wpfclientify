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

  }

}
