<?php

/**
 * Fired during plugin activation
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Bform
 * @subpackage Bform/includes
 * @author     Sr.Ralo <adreloa@gmail.com>
 */
class Bform_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$templates_table  = 'plugin_uleam_templates';
		$forms_table      = 'plugin_uleam_forms';
		$responses_table  = 'plugin_uleam_respuestas';

		$sql_templates = "CREATE TABLE {$templates_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			nombre VARCHAR(191) NOT NULL,
			esquema_json LONGTEXT NOT NULL,
			categoria VARCHAR(120) DEFAULT '' NOT NULL,
			fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$sql_forms = "CREATE TABLE {$forms_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			template_id BIGINT UNSIGNED DEFAULT NULL,
			nombre VARCHAR(191) NOT NULL,
			esquema_json LONGTEXT NOT NULL,
			slug_shortcode VARCHAR(191) NOT NULL,
			activo TINYINT(1) DEFAULT 1 NOT NULL,
			fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY idx_template_id (template_id),
			KEY idx_activo (activo),
			KEY idx_fecha_creacion (fecha_creacion)
		) {$charset_collate};";

		$sql_responses = "CREATE TABLE {$responses_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			form_id BIGINT UNSIGNED NOT NULL,
			datos_usuario LONGTEXT NOT NULL,
			metadatos LONGTEXT NULL,
			fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
			PRIMARY KEY  (id),
			KEY idx_form_id (form_id)
		) {$charset_collate};";

		dbDelta( $sql_templates );
		dbDelta( $sql_forms );
		dbDelta( $sql_responses );

	}

}
