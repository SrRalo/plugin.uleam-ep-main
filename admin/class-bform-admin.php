<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bform
 * @subpackage Bform/admin
 * @author     Sr.Ralo <adreloa@gmail.com>
 */
class Bform_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Hook suffix for plugin admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $page_hook_suffix    Hook suffix returned by add_menu_page.
	 */
	private $page_hook_suffix;

	/**
	 * Hook suffix for principal admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $principal_hook_suffix    Hook suffix returned by add_submenu_page.
	 */
	private $principal_hook_suffix;

	/**
	 * Hook suffix for constructor admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $constructor_hook_suffix    Hook suffix returned by add_submenu_page.
	 */
	private $constructor_hook_suffix;

	/**
	 * Hook suffix for logic admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $logic_hook_suffix    Hook suffix returned by add_submenu_page.
	 */
	private $logic_hook_suffix;

	/**
	 * Hook suffix for analytics admin page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $analytics_hook_suffix    Hook suffix returned by add_submenu_page.
	 */
	private $analytics_hook_suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->page_hook_suffix = '';
		$this->principal_hook_suffix = '';
		$this->constructor_hook_suffix = '';
		$this->logic_hook_suffix = '';
		$this->analytics_hook_suffix = '';

	}

	/**
	 * Register plugin admin menu and submenu.
	 *
	 * @since    1.0.0
	 */
	public function register_admin_menu() {

		$capability = 'manage_options';
		$menu_slug  = 'bform-menu-root';
		$principal_slug = 'bform-principal';
		$constructor_slug = 'bform-constructor';
		$logic_slug = 'bform-logica';
		$analytics_slug = 'bform-analiticas';

		$this->page_hook_suffix = add_menu_page(
			__( 'ULEAM Constructor de Formularios', 'bform' ),
			__( 'ULEAM Formularios', 'bform' ),
			$capability,
			$menu_slug,
			array( $this, 'display_menu_placeholder_page' ),
			'dashicons-feedback',
			30
		);

		$this->principal_hook_suffix = add_submenu_page(
			$menu_slug,
			__( 'Principal', 'bform' ),
			__( 'Principal', 'bform' ),
			$capability,
			$principal_slug,
			array( $this, 'display_principal_page' )
		);

		$this->constructor_hook_suffix = add_submenu_page(
			$menu_slug,
			__( 'Constructor', 'bform' ),
			__( 'Constructor', 'bform' ),
			$capability,
			$constructor_slug,
			array( $this, 'display_constructor_page' )
		);

		$this->logic_hook_suffix = add_submenu_page(
			$menu_slug,
			__( 'Lógica', 'bform' ),
			__( 'Lógica', 'bform' ),
			$capability,
			$logic_slug,
			array( $this, 'display_logic_page' )
		);

		$this->analytics_hook_suffix = add_submenu_page(
			$menu_slug,
			__( 'Analíticas', 'bform' ),
			__( 'Analíticas', 'bform' ),
			$capability,
			$analytics_slug,
			array( $this, 'display_analytics_page' )
		);

		remove_submenu_page( $menu_slug, $menu_slug );

	}

	/**
	 * Render plugin root placeholder page.
	 *
	 * @since    1.0.0
	 */
	public function display_menu_placeholder_page() {
		echo '<div class="wrap"><h1>' . esc_html__( 'ULEAM Formularios', 'bform' ) . '</h1><p>' . esc_html__( 'Selecciona una pestaña del submenú para comenzar.', 'bform' ) . '</p></div>';
	}

	/**
	 * Render plugin principal admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_principal_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', 'bform' ) );
		}

		$principal_forms     = $this->get_principal_forms();
		$principal_table_err = '';

		if ( is_wp_error( $principal_forms ) ) {
			$principal_table_err = $principal_forms->get_error_message();
			$principal_forms = array();
		}

		$principal_notice = isset( $_GET['bform_notice'] ) ? sanitize_text_field( wp_unslash( $_GET['bform_notice'] ) ) : '';

		$principal_page_url   = admin_url( 'admin.php?page=bform-principal' );
		$constructor_page_url = admin_url( 'admin.php?page=bform-constructor' );
		$logic_page_url       = admin_url( 'admin.php?page=bform-logica' );
		$analytics_page_url   = admin_url( 'admin.php?page=bform-analiticas' );
		require_once plugin_dir_path( __FILE__ ) . 'partials/bform-admin-display.php';
	}

	/**
	 * Render plugin constructor admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_constructor_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', 'bform' ) );
		}

		$constructor_form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
		$constructor_draft_id = isset( $_GET['draft_id'] ) ? sanitize_key( (string) wp_unslash( $_GET['draft_id'] ) ) : '';
		$constructor_form = $constructor_form_id > 0 ? $this->get_form_by_id( $constructor_form_id ) : null;

		$default_schema = $this->get_default_constructor_schema();
		$constructor_form_schema = $default_schema;

		if ( ! empty( $constructor_form['esquema_json'] ) ) {
			$decoded_schema = json_decode( $constructor_form['esquema_json'], true );
			if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded_schema ) ) {
				$constructor_form_schema = $decoded_schema;
			}
		}

		$constructor_form_name = ! empty( $constructor_form['nombre'] ) ? $constructor_form['nombre'] : __( 'Nuevo Formulario', 'bform' );

		$principal_page_url   = admin_url( 'admin.php?page=bform-principal' );
		$constructor_page_url = admin_url( 'admin.php?page=bform-constructor' );
		$logic_page_url       = admin_url( 'admin.php?page=bform-logica' );
		$analytics_page_url   = admin_url( 'admin.php?page=bform-analiticas' );
		require_once plugin_dir_path( __FILE__ ) . 'partials/bform-admin-constructor.php';
	}

	/**
	 * Save constructor schema via AJAX.
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_form_schema() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permisos insuficientes.', 'bform' ) ), 403 );
		}

		check_ajax_referer( 'bform_constructor_save', 'nonce' );

		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$form_name = isset( $_POST['form_name'] ) ? sanitize_text_field( wp_unslash( $_POST['form_name'] ) ) : '';
		$schema_raw = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : '';

		$decoded_schema = json_decode( $schema_raw, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded_schema ) ) {
			wp_send_json_error( array( 'message' => __( 'El esquema JSON no es válido.', 'bform' ) ), 400 );
		}

		$sanitized_schema = $this->sanitize_constructor_schema( $decoded_schema );
		if ( is_wp_error( $sanitized_schema ) ) {
			wp_send_json_error( array( 'message' => $sanitized_schema->get_error_message() ), 400 );
		}

		if ( '' === $form_name ) {
			$form_name = __( 'Formulario sin título', 'bform' );
		}

		$form_name = trim( preg_replace( '/\s+/', ' ', (string) $form_name ) );
		if ( '' === $form_name ) {
			$form_name = __( 'Formulario sin título', 'bform' );
		}

		global $wpdb;
		$this->ensure_plugin_tables_schema();
		$table_name = $this->get_forms_table_name();

		if ( $this->form_name_exists( $form_name, $form_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Ya existe un formulario con ese nombre. Usa un nombre diferente.', 'bform' ),
				),
				400
			);
		}

		$data = array(
			'nombre' => $form_name,
			'esquema_json' => wp_json_encode( $sanitized_schema ),
		);

		if ( $form_id > 0 ) {
			$updated = $wpdb->update(
				$table_name,
				$data,
				array( 'id' => $form_id ),
				array( '%s', '%s' ),
				array( '%d' )
			);

			if ( false === $updated ) {
				$wpdb_message = ! empty( $wpdb->last_error ) ? $wpdb->last_error : __( 'No se pudo actualizar el formulario en la base de datos.', 'bform' );
				wp_send_json_error( array( 'message' => $wpdb_message ), 500 );
			}

			wp_send_json_success(
				array(
					'form_id' => $form_id,
					'message' => __( 'Formulario actualizado correctamente.', 'bform' ),
				)
			);
		}

		$slug_base = sanitize_title( $form_name );
		$slug = $this->generate_unique_slug( $slug_base );

		$insert_data = array(
			'template_id' => null,
			'nombre' => $form_name,
			'esquema_json' => wp_json_encode( $sanitized_schema ),
			'slug_shortcode' => $slug,
			'activo' => 1,
			'fecha_creacion' => current_time( 'mysql' ),
			'fecha_actualizacion' => current_time( 'mysql' ),
		);

		$insert_format = array( '%d', '%s', '%s', '%s', '%d', '%s', '%s' );

		if ( $this->table_has_column( $table_name, 'form_key' ) ) {
			$insert_data['form_key'] = $this->generate_unique_form_key( $table_name );
			$insert_format[] = '%s';
		}

		$inserted = $wpdb->insert(
			$table_name,
			$insert_data,
			$insert_format
		);

		if ( false === $inserted ) {
			$wpdb_message = ! empty( $wpdb->last_error ) ? $wpdb->last_error : __( 'No se pudo crear el formulario en la base de datos.', 'bform' );
			wp_send_json_error( array( 'message' => $wpdb_message ), 500 );
		}

		$new_form_id = (int) $wpdb->insert_id;
		if ( $new_form_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'No se obtuvo un ID válido para el nuevo formulario.', 'bform' ) ), 500 );
		}

		wp_send_json_success(
			array(
				'form_id' => $new_form_id,
				'message' => __( 'Formulario creado correctamente.', 'bform' ),
			)
		);
	}

	/**
	 * Sanitize and validate constructor schema before persistence.
	 *
	 * @since    1.0.0
	 * @param    array $schema Constructor schema.
	 * @return   array|WP_Error
	 */
	private function sanitize_constructor_schema( $schema ) {
		$allowed_date_formats = array( 'DD/MM/YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD' );
		$allowed_link_targets = array( '_self', '_blank' );
		$allowed_number_presets = array( 'none', 'cedula_10', 'telefono_10', 'edad_1_150', 'decimal_2' );

		if ( empty( $schema['sections'] ) || ! is_array( $schema['sections'] ) ) {
			$schema['sections'] = array();
		}

		$sanitized_sections = array();

		foreach ( $schema['sections'] as $section_index => $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$section_settings = isset( $section['settings'] ) && is_array( $section['settings'] ) ? $section['settings'] : array();
			$allow_sequential_after_branch = ! empty( $section_settings['allow_sequential_after_branch'] );

			$sanitized_section = array(
				'id' => ! empty( $section['id'] ) ? sanitize_key( (string) $section['id'] ) : 'section_' . ( $section_index + 1 ),
				'title' => ! empty( $section['title'] ) ? sanitize_text_field( $section['title'] ) : sprintf( __( 'Sección %d', 'bform' ), $section_index + 1 ),
				'settings' => array(
					'allow_sequential_after_branch' => $allow_sequential_after_branch,
				),
				'fields' => array(),
			);

			$fields = isset( $section['fields'] ) && is_array( $section['fields'] ) ? $section['fields'] : array();

			foreach ( $fields as $field_index => $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}

				$type = ! empty( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
				$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();

				$sanitized_field = array(
					'id' => ! empty( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : 'field_' . ( $field_index + 1 ),
					'type' => $type,
					'label' => ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : __( 'Campo', 'bform' ),
					'placeholder' => ! empty( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
					'required' => ! empty( $field['required'] ),
					'settings' => $settings,
				);

				$description_enabled = ! empty( $settings['description_enabled'] );
				$description_text = isset( $settings['description_text'] ) ? sanitize_textarea_field( (string) $settings['description_text'] ) : '';
				$description_text = trim( preg_replace( '/\s+/', ' ', (string) $description_text ) );
				$description_text = substr( $description_text, 0, 280 );

				if ( ! $description_enabled ) {
					$description_text = '';
				}

				$sanitized_field['settings']['description_enabled'] = $description_enabled;
				$sanitized_field['settings']['description_text'] = $description_text;

				if ( 'date' === $type ) {
					$date_format = isset( $settings['date_format'] ) ? sanitize_text_field( $settings['date_format'] ) : '';
					if ( ! in_array( $date_format, $allowed_date_formats, true ) ) {
						return new WP_Error( 'invalid_date_format', __( 'Formato de fecha inválido. Solo se permite DD/MM/YYYY, MM/DD/YYYY o YYYY-MM-DD.', 'bform' ) );
					}
					$sanitized_field['settings']['date_format'] = $date_format;
				}

				if ( 'link' === $type ) {
					$link_url = isset( $settings['url'] ) ? esc_url_raw( $settings['url'] ) : '';
					$link_text = isset( $settings['text'] ) ? sanitize_text_field( $settings['text'] ) : '';
					$link_target = isset( $settings['target'] ) ? sanitize_text_field( $settings['target'] ) : '_self';
					if ( ! in_array( $link_target, $allowed_link_targets, true ) ) {
						$link_target = '_self';
					}
					$sanitized_field['settings']['url'] = $link_url;
					$sanitized_field['settings']['text'] = $link_text;
					$sanitized_field['settings']['target'] = $link_target;
				}

				if ( in_array( $type, array( 'radio', 'checkbox', 'select' ), true ) ) {
					$options_raw = isset( $settings['options'] ) && is_array( $settings['options'] ) ? $settings['options'] : array();
					$options_sanitized = array();

					foreach ( $options_raw as $option_item ) {
						$option_text = sanitize_text_field( (string) $option_item );
						if ( '' !== $option_text ) {
							$options_sanitized[] = $option_text;
						}
					}

					if ( empty( $options_sanitized ) ) {
						$options_sanitized = array( __( 'Opción 1', 'bform' ) );
					}

					$sanitized_field['settings']['options'] = array_values( $options_sanitized );
				}

				if ( 'canvas' === $type ) {
					$line_width = isset( $settings['line_width'] ) ? (float) $settings['line_width'] : 2;
					if ( $line_width < 1 ) {
						$line_width = 1;
					}
					if ( $line_width > 20 ) {
						$line_width = 20;
					}

					$stroke_color = isset( $settings['stroke_color'] ) ? sanitize_hex_color( $settings['stroke_color'] ) : '';
					if ( empty( $stroke_color ) ) {
						$stroke_color = '#1f2937';
					}

					$sanitized_field['settings']['line_width'] = $line_width;
					$sanitized_field['settings']['stroke_color'] = $stroke_color;
				}

				if ( 'file' === $type ) {
					$allowed_extensions_raw = isset( $settings['allowed_extensions'] ) && is_array( $settings['allowed_extensions'] ) ? $settings['allowed_extensions'] : array();
					$allowed_extensions = array();
					$extension_whitelist = array( 'pdf', 'jpg', 'jpeg', 'png' );

					foreach ( $allowed_extensions_raw as $extension_item ) {
						$extension = strtolower( sanitize_key( (string) $extension_item ) );
						if ( in_array( $extension, $extension_whitelist, true ) ) {
							$allowed_extensions[] = $extension;
						}
					}

					if ( empty( $allowed_extensions ) ) {
						$allowed_extensions = array( 'pdf', 'jpg', 'png' );
					}

					$sanitized_field['settings']['allowed_extensions'] = array_values( array_unique( $allowed_extensions ) );
				}

				if ( 'number' === $type ) {
					$number_preset = isset( $settings['number_preset'] ) ? sanitize_key( (string) $settings['number_preset'] ) : 'none';
					if ( ! in_array( $number_preset, $allowed_number_presets, true ) ) {
						$number_preset = 'none';
					}

					$sanitized_field['settings']['number_preset'] = $number_preset;
				}

				$sanitized_section['fields'][] = $sanitized_field;
			}

			$sanitized_sections[] = $sanitized_section;
		}

		$schema['sections'] = $sanitized_sections;

		if ( ! isset( $schema['branching_rules'] ) || ! is_array( $schema['branching_rules'] ) ) {
			$schema['branching_rules'] = array();
		}

		return $schema;
	}

	/**
	 * Default constructor schema.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_default_constructor_schema() {
		return array(
			'sections' => array(
				array(
					'id' => 'section_1',
					'title' => __( 'Sección 1', 'bform' ),
					'settings' => array(
						'allow_sequential_after_branch' => false,
					),
					'fields' => array(),
				),
			),
			'branching_rules' => array(),
		);
	}

	/**
	 * Render plugin logic admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_logic_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', 'bform' ) );
		}

		$logic_form_id = isset( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : 0;
		$logic_draft_id = isset( $_GET['draft_id'] ) ? sanitize_key( (string) wp_unslash( $_GET['draft_id'] ) ) : '';
		$logic_form = $logic_form_id > 0 ? $this->get_form_by_id( $logic_form_id ) : null;

		$logic_schema = $this->get_default_constructor_schema();
		if ( ! empty( $logic_form['esquema_json'] ) ) {
			$decoded = json_decode( $logic_form['esquema_json'], true );
			if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
				$logic_schema = $decoded;
			}
		}

		$logic_schema_json = wp_json_encode( $logic_schema );
		$logic_form_name = ! empty( $logic_form['nombre'] ) ? $logic_form['nombre'] : __( 'Formulario sin título', 'bform' );

		$logic_field_options = array();
		if ( ! empty( $logic_schema['sections'] ) && is_array( $logic_schema['sections'] ) ) {
			foreach ( $logic_schema['sections'] as $section ) {
				if ( empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
					continue;
				}
				foreach ( $section['fields'] as $field ) {
					if ( empty( $field['id'] ) ) {
						continue;
					}

					$field_type = ! empty( $field['type'] ) ? sanitize_key( (string) $field['type'] ) : 'text';
					$field_choices = array();
					if ( in_array( $field_type, array( 'radio', 'checkbox', 'select' ), true ) ) {
						$field_settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
						$options = isset( $field_settings['options'] ) && is_array( $field_settings['options'] ) ? $field_settings['options'] : array();

						foreach ( $options as $option_item ) {
							$option_text = sanitize_text_field( (string) $option_item );
							if ( '' !== $option_text ) {
								$field_choices[] = $option_text;
							}
						}
					}

					$logic_field_options[] = array(
						'id' => sanitize_key( (string) $field['id'] ),
						'label' => ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : sanitize_key( (string) $field['id'] ),
						'type' => $field_type,
						'choices' => array_values( $field_choices ),
					);
				}
			}
		}

		$logic_graph_map = $this->build_navigation_graph_map( $logic_schema );

		$principal_page_url   = admin_url( 'admin.php?page=bform-principal' );
		$constructor_page_url = admin_url( 'admin.php?page=bform-constructor' );
		$logic_page_url       = admin_url( 'admin.php?page=bform-logica' );
		$analytics_page_url   = admin_url( 'admin.php?page=bform-analiticas' );
		require_once plugin_dir_path( __FILE__ ) . 'partials/bform-admin-logic.php';
	}

	/**
	 * Save logic module data via AJAX.
	 *
	 * @since    1.0.0
	 */
	public function ajax_save_logic_schema() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permisos insuficientes.', 'bform' ) ), 403 );
		}

		check_ajax_referer( 'bform_logic_save', 'nonce' );

		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		$schema_raw = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : '';

		if ( $form_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Debes seleccionar un formulario válido.', 'bform' ) ), 400 );
		}

		$decoded_schema = json_decode( $schema_raw, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded_schema ) ) {
			wp_send_json_error( array( 'message' => __( 'El esquema de lógica no es válido.', 'bform' ) ), 400 );
		}

		$sanitized_schema = $this->sanitize_constructor_schema( $decoded_schema );
		if ( is_wp_error( $sanitized_schema ) ) {
			wp_send_json_error( array( 'message' => $sanitized_schema->get_error_message() ), 400 );
		}

		$sanitized_rules = $this->sanitize_logic_rules( $sanitized_schema );
		if ( is_wp_error( $sanitized_rules ) ) {
			wp_send_json_error( array( 'message' => $sanitized_rules->get_error_message() ), 400 );
		}

		$sanitized_schema['branching_rules'] = $sanitized_rules;

		$cycle_check = $this->validate_navigation_graph_no_cycles( $sanitized_schema );
		if ( is_wp_error( $cycle_check ) ) {
			wp_send_json_error( array( 'message' => $cycle_check->get_error_message() ), 400 );
		}

		global $wpdb;
		$wpdb->update(
			$this->get_forms_table_name(),
			array(
				'esquema_json' => wp_json_encode( $sanitized_schema ),
			),
			array( 'id' => $form_id ),
			array( '%s' ),
			array( '%d' )
		);

		wp_send_json_success(
			array(
				'message' => __( 'Lógica y secciones guardadas correctamente.', 'bform' ),
				'graph' => $this->build_navigation_graph_map( $sanitized_schema ),
			)
		);
	}

	/**
	 * Sanitize and validate logic rules.
	 *
	 * @since    1.0.0
	 * @param    array $schema Form schema.
	 * @return   array|WP_Error
	 */
	private function sanitize_logic_rules( $schema ) {
		$allowed_operators = array( 'equals', 'contains' );
		$allowed_actions = array( 'jump_section' );

		$rules = array();
		if ( isset( $schema['branching_rules'] ) && is_array( $schema['branching_rules'] ) ) {
			$rules = $schema['branching_rules'];
		}

		$section_ids = array();
		if ( ! empty( $schema['sections'] ) && is_array( $schema['sections'] ) ) {
			foreach ( $schema['sections'] as $section ) {
				if ( ! empty( $section['id'] ) ) {
					$section_ids[] = sanitize_key( (string) $section['id'] );
				}
			}
		}

		$sanitized = array();
		foreach ( $rules as $index => $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			$rule_operator = isset( $rule['operator'] ) ? sanitize_key( $rule['operator'] ) : 'equals';
			$rule_action = isset( $rule['action'] ) ? sanitize_key( $rule['action'] ) : 'jump_section';

			if ( ! in_array( $rule_operator, $allowed_operators, true ) ) {
				return new WP_Error( 'invalid_operator', __( 'Operador inválido en regla lógica.', 'bform' ) );
			}

			if ( ! in_array( $rule_action, $allowed_actions, true ) ) {
				return new WP_Error( 'invalid_action', __( 'Acción inválida en regla lógica.', 'bform' ) );
			}

			$source_section_id = isset( $rule['source_section_id'] ) ? sanitize_key( (string) $rule['source_section_id'] ) : '';
			$target_section_id = isset( $rule['target_section_id'] ) ? sanitize_key( (string) $rule['target_section_id'] ) : '';

			if ( '' !== $source_section_id && ! in_array( $source_section_id, $section_ids, true ) ) {
				return new WP_Error( 'invalid_source_section', __( 'Una regla apunta a una sección origen inexistente.', 'bform' ) );
			}

			if ( '' !== $target_section_id && ! in_array( $target_section_id, $section_ids, true ) ) {
				return new WP_Error( 'invalid_target_section', __( 'Una regla apunta a una sección destino inexistente.', 'bform' ) );
			}

			$sanitized[] = array(
				'id' => ! empty( $rule['id'] ) ? sanitize_key( (string) $rule['id'] ) : 'rule_' . ( $index + 1 ),
				'source_field_id' => isset( $rule['source_field_id'] ) ? sanitize_key( (string) $rule['source_field_id'] ) : '',
				'source_section_id' => $source_section_id,
				'operator' => $rule_operator,
				'value' => isset( $rule['value'] ) ? sanitize_text_field( (string) $rule['value'] ) : '',
				'action' => $rule_action,
				'target_section_id' => $target_section_id,
			);
		}

		return $sanitized;
	}

	/**
	 * Validate navigation graph has no cycles.
	 *
	 * @since    1.0.0
	 * @param    array $schema Form schema.
	 * @return   true|WP_Error
	 */
	private function validate_navigation_graph_no_cycles( $schema ) {
		$graph = array();
		$sections = isset( $schema['sections'] ) && is_array( $schema['sections'] ) ? $schema['sections'] : array();
		$rules = isset( $schema['branching_rules'] ) && is_array( $schema['branching_rules'] ) ? $schema['branching_rules'] : array();

		foreach ( $sections as $section ) {
			if ( ! empty( $section['id'] ) ) {
				$graph[ (string) $section['id'] ] = array();
			}
		}

		foreach ( $rules as $rule ) {
			if ( empty( $rule['source_section_id'] ) || empty( $rule['target_section_id'] ) ) {
				continue;
			}

			$source = (string) $rule['source_section_id'];
			$target = (string) $rule['target_section_id'];

			if ( ! isset( $graph[ $source ] ) ) {
				$graph[ $source ] = array();
			}

			$graph[ $source ][] = $target;
		}

		$visited = array();
		$stack = array();

		foreach ( array_keys( $graph ) as $node ) {
			if ( $this->graph_has_cycle_dfs( $node, $graph, $visited, $stack ) ) {
				return new WP_Error( 'logic_cycle', __( 'Se detectó un bucle infinito en el mapa lógico de secciones.', 'bform' ) );
			}
		}

		return true;
	}

	/**
	 * DFS helper for cycle detection.
	 *
	 * @since    1.0.0
	 * @param    string $node Current node.
	 * @param    array  $graph Graph adjacency.
	 * @param    array  $visited Visited map.
	 * @param    array  $stack Recursion stack.
	 * @return   bool
	 */
	private function graph_has_cycle_dfs( $node, $graph, &$visited, &$stack ) {
		if ( ! empty( $stack[ $node ] ) ) {
			return true;
		}

		if ( ! empty( $visited[ $node ] ) ) {
			return false;
		}

		$visited[ $node ] = true;
		$stack[ $node ] = true;

		$neighbors = isset( $graph[ $node ] ) && is_array( $graph[ $node ] ) ? $graph[ $node ] : array();
		foreach ( $neighbors as $neighbor ) {
			if ( $this->graph_has_cycle_dfs( (string) $neighbor, $graph, $visited, $stack ) ) {
				return true;
			}
		}

		$stack[ $node ] = false;
		return false;
	}

	/**
	 * Build graph map summary for UI.
	 *
	 * @since    1.0.0
	 * @param    array $schema Form schema.
	 * @return   array
	 */
	private function build_navigation_graph_map( $schema ) {
		$graph = array();
		$sections = isset( $schema['sections'] ) && is_array( $schema['sections'] ) ? $schema['sections'] : array();
		$rules = isset( $schema['branching_rules'] ) && is_array( $schema['branching_rules'] ) ? $schema['branching_rules'] : array();

		foreach ( $sections as $section ) {
			$section_id = ! empty( $section['id'] ) ? sanitize_key( (string) $section['id'] ) : '';
			if ( '' === $section_id ) {
				continue;
			}

			$graph[] = array(
				'id' => $section_id,
				'title' => ! empty( $section['title'] ) ? sanitize_text_field( (string) $section['title'] ) : $section_id,
				'targets' => array(),
			);
		}

		$indexed = array();
		foreach ( $graph as $idx => $item ) {
			$indexed[ $item['id'] ] = $idx;
		}

		foreach ( $rules as $rule ) {
			if ( empty( $rule['source_section_id'] ) || empty( $rule['target_section_id'] ) ) {
				continue;
			}
			$source = sanitize_key( (string) $rule['source_section_id'] );
			$target = sanitize_key( (string) $rule['target_section_id'] );
			if ( isset( $indexed[ $source ] ) ) {
				$graph[ $indexed[ $source ] ]['targets'][] = $target;
			}
		}

		return $graph;
	}

	/**
	 * Render plugin analytics admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_analytics_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'No tienes permisos para acceder a esta página.', 'bform' ) );
		}

		$analytics_filter_form_id = isset( $_GET['filter_form_id'] ) ? absint( $_GET['filter_form_id'] ) : 0;
		$analytics_search_term = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$analytics_page_number = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;

		$analytics_forms = $this->get_analytics_forms();
		$valid_form_ids = wp_list_pluck( $analytics_forms, 'id' );
		if ( $analytics_filter_form_id > 0 && ! in_array( $analytics_filter_form_id, $valid_form_ids, true ) ) {
			$analytics_filter_form_id = 0;
		}

		$analytics_per_page = 20;
		$analytics_metrics = $this->get_analytics_metrics( $analytics_filter_form_id, $analytics_search_term );
		$analytics_data = $this->get_analytics_forms_summary_page( $analytics_filter_form_id, $analytics_search_term, $analytics_page_number, $analytics_per_page );

		$analytics_rows = $analytics_data['rows'];
		$analytics_total_count = $analytics_data['total_count'];
		$analytics_total_pages = $analytics_data['total_pages'];
		$analytics_page_number = $analytics_data['current_page'];

		$analytics_base_params = array(
			'page' => 'bform-analiticas',
		);

		if ( $analytics_filter_form_id > 0 ) {
			$analytics_base_params['filter_form_id'] = $analytics_filter_form_id;
		}

		if ( '' !== $analytics_search_term ) {
			$analytics_base_params['s'] = $analytics_search_term;
		}

		$analytics_page_base_url = add_query_arg( $analytics_base_params, admin_url( 'admin.php' ) );
		$analytics_export_url = add_query_arg( array_merge( $analytics_base_params, array( 'export_csv' => 1 ) ), admin_url( 'admin.php' ) );

		$principal_page_url   = admin_url( 'admin.php?page=bform-principal' );
		$constructor_page_url = admin_url( 'admin.php?page=bform-constructor' );
		$logic_page_url       = admin_url( 'admin.php?page=bform-logica' );
		$analytics_page_url   = admin_url( 'admin.php?page=bform-analiticas' );
		require_once plugin_dir_path( __FILE__ ) . 'partials/bform-admin-analytics.php';
	}

	/**
	 * Get forms available for analytics filters.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	private function get_analytics_forms() {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) );
		if ( $table_exists !== $forms_table ) {
			return array();
		}

		$rows = $wpdb->get_results( "SELECT id, nombre FROM {$forms_table} ORDER BY fecha_creacion DESC", ARRAY_A );
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$forms = array();
		foreach ( $rows as $row ) {
			$forms[] = array(
				'id' => isset( $row['id'] ) ? absint( $row['id'] ) : 0,
				'nombre' => isset( $row['nombre'] ) ? sanitize_text_field( (string) $row['nombre'] ) : __( 'Formulario', 'bform' ),
			);
		}

		return $forms;
	}

	/**
	 * Build SQL WHERE clause and parameters for analytics filters.
	 *
	 * @since    1.0.0
	 * @param    int    $form_id Form ID filter.
	 * @param    string $search Search term.
	 * @return   array
	 */
	private function build_analytics_where( $form_id, $search ) {
		global $wpdb;

		$clauses = array( '1=1' );
		$params = array();

		if ( $form_id > 0 ) {
			$clauses[] = 'r.form_id = %d';
			$params[] = $form_id;
		}

		if ( '' !== $search ) {
			$like = '%' . $wpdb->esc_like( $search ) . '%';
			$clauses[] = '(f.nombre LIKE %s OR r.datos_usuario LIKE %s OR r.metadatos LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		return array(
			'sql' => ' WHERE ' . implode( ' AND ', $clauses ),
			'params' => $params,
		);
	}

	/**
	 * Get analytics response rows for current page.
	 *
	 * @since    1.0.0
	 * @param    int $form_id Form ID filter.
	 * @param    string $search Search term.
	 * @param    int $page Current page.
	 * @param    int $per_page Rows per page.
	 * @return   array
	 */
	private function get_analytics_responses_page( $form_id, $search, $page, $per_page ) {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();

		$forms_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) );
		$responses_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $responses_table ) );
		if ( $forms_exists !== $forms_table || $responses_exists !== $responses_table ) {
			return array(
				'rows' => array(),
				'total_count' => 0,
				'total_pages' => 1,
				'current_page' => 1,
			);
		}

		$per_page = max( 1, absint( $per_page ) );
		$page = max( 1, absint( $page ) );
		$offset = ( $page - 1 ) * $per_page;

		$where = $this->build_analytics_where( $form_id, $search );

		$count_sql = "SELECT COUNT(*) FROM {$responses_table} r LEFT JOIN {$forms_table} f ON f.id = r.form_id {$where['sql']}";
		$total_count = empty( $where['params'] )
			? (int) $wpdb->get_var( $count_sql )
			: (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $where['params'] ) );

		$total_pages = max( 1, (int) ceil( $total_count / $per_page ) );
		if ( $page > $total_pages ) {
			$page = $total_pages;
			$offset = ( $page - 1 ) * $per_page;
		}

		$query_params = $where['params'];
		$query_params[] = $per_page;
		$query_params[] = $offset;

		$data_sql = "SELECT r.id, r.form_id, r.datos_usuario, r.metadatos, r.fecha_creacion, f.nombre AS form_name FROM {$responses_table} r LEFT JOIN {$forms_table} f ON f.id = r.form_id {$where['sql']} ORDER BY r.fecha_creacion DESC LIMIT %d OFFSET %d";
		$prepared = $wpdb->prepare( $data_sql, $query_params );
		$raw_rows = $wpdb->get_results( $prepared, ARRAY_A );

		$rows = array();
		if ( is_array( $raw_rows ) ) {
			foreach ( $raw_rows as $raw_row ) {
				$datos = $this->decode_analytics_json( isset( $raw_row['datos_usuario'] ) ? $raw_row['datos_usuario'] : '' );
				$meta = $this->decode_analytics_json( isset( $raw_row['metadatos'] ) ? $raw_row['metadatos'] : '' );

				$status = $this->resolve_analytics_status( $datos, $meta );
				$status_map = $this->map_analytics_status_class( $status );

				$rows[] = array(
					'id' => isset( $raw_row['id'] ) ? absint( $raw_row['id'] ) : 0,
					'form_id' => isset( $raw_row['form_id'] ) ? absint( $raw_row['form_id'] ) : 0,
					'form_name' => isset( $raw_row['form_name'] ) && '' !== $raw_row['form_name'] ? sanitize_text_field( (string) $raw_row['form_name'] ) : __( 'Formulario', 'bform' ),
					'submitted_at' => isset( $raw_row['fecha_creacion'] ) ? (string) $raw_row['fecha_creacion'] : '',
					'applicant' => $this->resolve_analytics_applicant( $datos, $meta ),
					'email' => $this->resolve_analytics_email( $datos, $meta ),
					'status_label' => $status_map['label'],
					'status_class' => $status_map['class'],
					'has_signature' => $this->resolve_analytics_signature( $datos, $meta ),
				);
			}
		}

		return array(
			'rows' => $rows,
			'total_count' => $total_count,
			'total_pages' => $total_pages,
			'current_page' => $page,
		);
	}

	/**
	 * Get analytics summary rows grouped by form.
	 *
	 * @since    1.0.0
	 * @param    int    $form_id Form ID filter.
	 * @param    string $search Search term.
	 * @param    int    $page Current page.
	 * @param    int    $per_page Rows per page.
	 * @return   array
	 */
	private function get_analytics_forms_summary_page( $form_id, $search, $page, $per_page ) {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();

		$forms_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) );
		$responses_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $responses_table ) );
		if ( $forms_exists !== $forms_table || $responses_exists !== $responses_table ) {
			return array(
				'rows' => array(),
				'total_count' => 0,
				'total_pages' => 1,
				'current_page' => 1,
			);
		}

		$per_page = max( 1, absint( $per_page ) );
		$page = max( 1, absint( $page ) );
		$offset = ( $page - 1 ) * $per_page;

		$where = $this->build_analytics_where( $form_id, $search );

		$count_sql = "SELECT COUNT(*) FROM (SELECT f.id FROM {$forms_table} f LEFT JOIN {$responses_table} r ON r.form_id = f.id {$where['sql']} GROUP BY f.id HAVING COUNT(r.id) > 0) summary";
		$total_count = empty( $where['params'] )
			? (int) $wpdb->get_var( $count_sql )
			: (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $where['params'] ) );

		$total_pages = max( 1, (int) ceil( $total_count / $per_page ) );
		if ( $page > $total_pages ) {
			$page = $total_pages;
			$offset = ( $page - 1 ) * $per_page;
		}

		$query_params = $where['params'];
		$query_params[] = $per_page;
		$query_params[] = $offset;

		$data_sql = "SELECT f.id, f.nombre, f.fecha_creacion, COUNT(r.id) AS total_responses FROM {$forms_table} f LEFT JOIN {$responses_table} r ON r.form_id = f.id {$where['sql']} GROUP BY f.id, f.nombre, f.fecha_creacion HAVING COUNT(r.id) > 0 ORDER BY total_responses DESC, f.nombre ASC LIMIT %d OFFSET %d";
		$prepared = $wpdb->prepare( $data_sql, $query_params );
		$raw_rows = $wpdb->get_results( $prepared, ARRAY_A );

		$rows = array();
		if ( is_array( $raw_rows ) ) {
			foreach ( $raw_rows as $raw_row ) {
				$rows[] = array(
					'form_id' => isset( $raw_row['id'] ) ? absint( $raw_row['id'] ) : 0,
					'form_name' => isset( $raw_row['nombre'] ) && '' !== $raw_row['nombre'] ? sanitize_text_field( (string) $raw_row['nombre'] ) : __( 'Formulario', 'bform' ),
					'form_created_at' => isset( $raw_row['fecha_creacion'] ) ? sanitize_text_field( (string) $raw_row['fecha_creacion'] ) : '',
					'total_responses' => isset( $raw_row['total_responses'] ) ? absint( $raw_row['total_responses'] ) : 0,
				);
			}
		}

		return array(
			'rows' => $rows,
			'total_count' => $total_count,
			'total_pages' => $total_pages,
			'current_page' => $page,
		);
	}

	/**
	 * AJAX: Fetch analytics responses for a specific form.
	 *
	 * @since    1.0.0
	 */
	public function ajax_get_analytics_form_responses() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permisos insuficientes.', 'bform' ) ), 403 );
		}

		check_ajax_referer( 'bform_analytics_modal', 'nonce' );

		$form_id = isset( $_POST['form_id'] ) ? absint( $_POST['form_id'] ) : 0;
		if ( $form_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Formulario inválido.', 'bform' ) ), 400 );
		}

		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();

		$forms_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) );
		$responses_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $responses_table ) );
		if ( $forms_exists !== $forms_table || $responses_exists !== $responses_table ) {
			wp_send_json_success(
				array(
					'form_id' => $form_id,
					'form_name' => __( 'Formulario', 'bform' ),
					'rows' => array(),
				)
			);
		}

		$raw_rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT r.id, r.datos_usuario, r.metadatos, r.fecha_creacion, f.nombre AS form_name FROM {$responses_table} r LEFT JOIN {$forms_table} f ON f.id = r.form_id WHERE r.form_id = %d ORDER BY r.fecha_creacion DESC LIMIT %d",
				$form_id,
				200
			),
			ARRAY_A
		);

		$form_name = __( 'Formulario', 'bform' );
		$rows = array();

		if ( is_array( $raw_rows ) ) {
			foreach ( $raw_rows as $raw_row ) {
				$datos = $this->decode_analytics_json( isset( $raw_row['datos_usuario'] ) ? $raw_row['datos_usuario'] : '' );
				$meta = $this->decode_analytics_json( isset( $raw_row['metadatos'] ) ? $raw_row['metadatos'] : '' );

				$status = $this->resolve_analytics_status( $datos, $meta );
				$status_map = $this->map_analytics_status_class( $status );

				if ( isset( $raw_row['form_name'] ) && '' !== $raw_row['form_name'] ) {
					$form_name = sanitize_text_field( (string) $raw_row['form_name'] );
				}

				$rows[] = array(
					'id' => isset( $raw_row['id'] ) ? absint( $raw_row['id'] ) : 0,
					'applicant' => $this->resolve_analytics_applicant( $datos, $meta ),
					'email' => $this->resolve_analytics_email( $datos, $meta ),
					'faculty' => $this->resolve_analytics_faculty( $datos, $meta ),
					'submitted_at' => isset( $raw_row['fecha_creacion'] ) && '' !== $raw_row['fecha_creacion'] ? wp_date( 'Y-m-d', strtotime( (string) $raw_row['fecha_creacion'] ) ) : '-',
					'status_label' => $status_map['label'],
				);
			}
		}

		wp_send_json_success(
			array(
				'form_id' => $form_id,
				'form_name' => $form_name,
				'rows' => $rows,
			)
		);
	}

	/**
	 * Get analytics metrics.
	 *
	 * @since    1.0.0
	 * @param    int    $form_id Form ID filter.
	 * @param    string $search Search term.
	 * @return   array
	 */
	private function get_analytics_metrics( $form_id, $search ) {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();

		$metrics = array(
			'total_submissions' => 0,
			'forms_with_responses' => 0,
			'last_response' => '',
			'active_forms' => 0,
		);

		$forms_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $forms_table ) );
		$responses_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $responses_table ) );
		if ( $forms_exists !== $forms_table || $responses_exists !== $responses_table ) {
			return $metrics;
		}

		$where = $this->build_analytics_where( $form_id, $search );

		$totals_sql = "SELECT COUNT(*) AS total_submissions, COUNT(DISTINCT r.form_id) AS forms_with_responses, MAX(r.fecha_creacion) AS last_response FROM {$responses_table} r LEFT JOIN {$forms_table} f ON f.id = r.form_id {$where['sql']}";
		$totals_row = empty( $where['params'] )
			? $wpdb->get_row( $totals_sql, ARRAY_A )
			: $wpdb->get_row( $wpdb->prepare( $totals_sql, $where['params'] ), ARRAY_A );

		if ( is_array( $totals_row ) ) {
			$metrics['total_submissions'] = isset( $totals_row['total_submissions'] ) ? absint( $totals_row['total_submissions'] ) : 0;
			$metrics['forms_with_responses'] = isset( $totals_row['forms_with_responses'] ) ? absint( $totals_row['forms_with_responses'] ) : 0;
			$metrics['last_response'] = isset( $totals_row['last_response'] ) ? (string) $totals_row['last_response'] : '';
		}

		$metrics['active_forms'] = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$forms_table} WHERE activo = 1" );

		return $metrics;
	}

	/**
	 * Export analytics rows as CSV.
	 *
	 * @since    1.0.0
	 * @param    int    $form_id Form ID filter.
	 * @param    string $search Search term.
	 */
	private function export_analytics_csv( $form_id, $search ) {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();
		$where = $this->build_analytics_where( $form_id, $search );

		$data_sql = "SELECT r.id, r.form_id, r.datos_usuario, r.metadatos, r.fecha_creacion, f.nombre AS form_name FROM {$responses_table} r LEFT JOIN {$forms_table} f ON f.id = r.form_id {$where['sql']} ORDER BY r.fecha_creacion DESC LIMIT %d";
		$query_params = $where['params'];
		$query_params[] = 10000;

		$raw_rows = $wpdb->get_results( $wpdb->prepare( $data_sql, $query_params ), ARRAY_A );

		$rows = array();
		$dynamic_field_keys = array();

		if ( is_array( $raw_rows ) ) {
			foreach ( $raw_rows as $raw_row ) {
				$datos = $this->decode_analytics_json( isset( $raw_row['datos_usuario'] ) ? $raw_row['datos_usuario'] : '' );
				$meta = $this->decode_analytics_json( isset( $raw_row['metadatos'] ) ? $raw_row['metadatos'] : '' );

				$status = $this->resolve_analytics_status( $datos, $meta );
				$status_map = $this->map_analytics_status_class( $status );
				$field_map = $this->build_export_field_map( $datos );

				foreach ( array_keys( $field_map ) as $field_key ) {
					if ( ! in_array( $field_key, $dynamic_field_keys, true ) ) {
						$dynamic_field_keys[] = $field_key;
					}
				}

				$rows[] = array(
					'id' => isset( $raw_row['id'] ) ? absint( $raw_row['id'] ) : 0,
					'form_name' => isset( $raw_row['form_name'] ) ? sanitize_text_field( (string) $raw_row['form_name'] ) : __( 'Formulario', 'bform' ),
					'submitted_at' => isset( $raw_row['fecha_creacion'] ) ? sanitize_text_field( (string) $raw_row['fecha_creacion'] ) : '',
					'applicant' => $this->resolve_analytics_applicant( $datos, $meta ),
					'email' => $this->resolve_analytics_email( $datos, $meta ),
					'status_label' => $status_map['label'],
					'has_signature' => $this->resolve_analytics_signature( $datos, $meta ),
					'field_map' => $field_map,
				);
			}
		}

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="uleam-analytics-' . gmdate( 'Ymd-His' ) . '.csv"' );

		$output = fopen( 'php://output', 'w' );
		if ( false === $output ) {
			exit;
		}

		$headers = array( 'ID', 'Formulario', 'Fecha de envío', 'Solicitante', 'Correo', 'Estado', 'Firma' );
		foreach ( $dynamic_field_keys as $field_key ) {
			$headers[] = $this->format_export_field_header( $field_key );
		}

		fputcsv( $output, $headers );

		foreach ( $rows as $row ) {
			$line = array(
				isset( $row['id'] ) ? $row['id'] : '',
				isset( $row['form_name'] ) ? $row['form_name'] : '',
				isset( $row['submitted_at'] ) ? $row['submitted_at'] : '',
				isset( $row['applicant'] ) ? $row['applicant'] : '',
				isset( $row['email'] ) ? $row['email'] : '',
				isset( $row['status_label'] ) ? $row['status_label'] : '',
				! empty( $row['has_signature'] ) ? __( 'Sí', 'bform' ) : __( 'No', 'bform' ),
			);

			$field_map = isset( $row['field_map'] ) && is_array( $row['field_map'] ) ? $row['field_map'] : array();
			foreach ( $dynamic_field_keys as $field_key ) {
				$line[] = isset( $field_map[ $field_key ] ) ? $field_map[ $field_key ] : '';
			}

			fputcsv(
				$output,
				$line
			);
		}

		fclose( $output );
		exit;
	}

	/**
	 * Convert submitted payload to flat exportable key-value map.
	 *
	 * @since    1.0.0
	 * @param    mixed  $data Payload to flatten.
	 * @param    string $prefix Key prefix.
	 * @return   array
	 */
	private function build_export_field_map( $data, $prefix = '' ) {
		$result = array();

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$segment = is_string( $key ) ? $key : (string) $key;
				$path = '' !== $prefix ? $prefix . '.' . $segment : $segment;

				if ( is_array( $value ) ) {
					$scalar_items = array();
					$can_join = true;

					foreach ( $value as $nested_item ) {
						if ( is_scalar( $nested_item ) ) {
							$clean_item = sanitize_text_field( (string) $nested_item );
							if ( '' !== $clean_item ) {
								$scalar_items[] = $clean_item;
							}
						} else {
							$can_join = false;
							break;
						}
					}

					if ( $can_join ) {
						$result[ strtolower( $path ) ] = implode( ', ', $scalar_items );
						continue;
					}
				}

				$result = array_merge( $result, $this->build_export_field_map( $value, $path ) );
			}

			return $result;
		}

		if ( is_scalar( $data ) && '' !== $prefix ) {
			$result[ strtolower( $prefix ) ] = sanitize_text_field( (string) $data );
		}

		return $result;
	}

	/**
	 * Build readable CSV header label from payload field key.
	 *
	 * @since    1.0.0
	 * @param    string $field_key Raw payload key.
	 * @return   string
	 */
	private function format_export_field_header( $field_key ) {
		$field_key = strtolower( sanitize_key( (string) str_replace( '.', '_', $field_key ) ) );
		$field_key = preg_replace( '/^bform_/', '', $field_key );
		$field_key = str_replace( '_', ' ', $field_key );
		$field_key = trim( (string) $field_key );

		if ( '' === $field_key ) {
			return __( 'Campo', 'bform' );
		}

		return ucwords( $field_key );
	}

	/**
	 * Decode response JSON payload.
	 *
	 * @since    1.0.0
	 * @param    string $raw Raw JSON string.
	 * @return   array
	 */
	private function decode_analytics_json( $raw ) {
		$decoded = json_decode( (string) $raw, true );
		return is_array( $decoded ) ? $decoded : array();
	}

	/**
	 * Flatten nested payload into key-value scalar pairs.
	 *
	 * @since    1.0.0
	 * @param    mixed  $data Payload.
	 * @param    string $prefix Key prefix.
	 * @return   array
	 */
	private function flatten_analytics_payload( $data, $prefix = '' ) {
		$result = array();

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$segment = is_string( $key ) ? $key : (string) $key;
				$path = '' !== $prefix ? $prefix . '.' . $segment : $segment;
				$result = array_merge( $result, $this->flatten_analytics_payload( $value, $path ) );
			}
			return $result;
		}

		if ( is_scalar( $data ) ) {
			$result[] = array(
				'key' => strtolower( (string) $prefix ),
				'value' => sanitize_text_field( (string) $data ),
			);
		}

		return $result;
	}

	/**
	 * Resolve response applicant name.
	 *
	 * @since    1.0.0
	 * @param    array $datos User data payload.
	 * @param    array $meta Metadata payload.
	 * @return   string
	 */
	private function resolve_analytics_applicant( $datos, $meta ) {
		$flat = array_merge( $this->flatten_analytics_payload( $datos ), $this->flatten_analytics_payload( $meta ) );
		$hints = array( 'nombre', 'name', 'apellido', 'fullname', 'solicitante', 'usuario' );

		foreach ( $flat as $entry ) {
			$key = isset( $entry['key'] ) ? $entry['key'] : '';
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';
			if ( '' === $value ) {
				continue;
			}

			foreach ( $hints as $hint ) {
				if ( false !== strpos( $key, $hint ) ) {
					return $value;
				}
			}
		}

		foreach ( $flat as $entry ) {
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';
			if ( '' !== $value && false === filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				return $value;
			}
		}

		return __( 'Sin nombre', 'bform' );
	}

	/**
	 * Resolve response email.
	 *
	 * @since    1.0.0
	 * @param    array $datos User data payload.
	 * @param    array $meta Metadata payload.
	 * @return   string
	 */
	private function resolve_analytics_email( $datos, $meta ) {
		$flat = array_merge( $this->flatten_analytics_payload( $datos ), $this->flatten_analytics_payload( $meta ) );

		foreach ( $flat as $entry ) {
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';
			if ( '' === $value ) {
				continue;
			}

			if ( false !== filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				return sanitize_email( $value );
			}
		}

		return '-';
	}

	/**
	 * Resolve response faculty.
	 *
	 * @since    1.0.0
	 * @param    array $datos User data payload.
	 * @param    array $meta Metadata payload.
	 * @return   string
	 */
	private function resolve_analytics_faculty( $datos, $meta ) {
		$flat = array_merge( $this->flatten_analytics_payload( $datos ), $this->flatten_analytics_payload( $meta ) );
		$hints = array( 'facultad', 'faculty', 'carrera', 'departamento', 'department' );

		foreach ( $flat as $entry ) {
			$key = isset( $entry['key'] ) ? $entry['key'] : '';
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';
			if ( '' === $value ) {
				continue;
			}

			foreach ( $hints as $hint ) {
				if ( false !== strpos( $key, $hint ) ) {
					return $value;
				}
			}
		}

		return '-';
	}

	/**
	 * Resolve response status.
	 *
	 * @since    1.0.0
	 * @param    array $datos User data payload.
	 * @param    array $meta Metadata payload.
	 * @return   string
	 */
	private function resolve_analytics_status( $datos, $meta ) {
		$flat = array_merge( $this->flatten_analytics_payload( $meta ), $this->flatten_analytics_payload( $datos ) );

		foreach ( $flat as $entry ) {
			$key = isset( $entry['key'] ) ? $entry['key'] : '';
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';
			if ( '' === $value ) {
				continue;
			}

			if ( false !== strpos( $key, 'status' ) || false !== strpos( $key, 'estado' ) ) {
				return strtolower( $value );
			}
		}

		return 'completado';
	}

	/**
	 * Map raw status string to display label/class.
	 *
	 * @since    1.0.0
	 * @param    string $status Raw status value.
	 * @return   array
	 */
	private function map_analytics_status_class( $status ) {
		$status = strtolower( trim( (string) $status ) );

		if ( in_array( $status, array( 'pendiente', 'pending' ), true ) ) {
			return array(
				'label' => __( 'Pendiente', 'bform' ),
				'class' => 'bform-pill-pending',
			);
		}

		if ( in_array( $status, array( 'rechazado', 'rejected', 'error' ), true ) ) {
			return array(
				'label' => __( 'Rechazado', 'bform' ),
				'class' => 'bform-pill-rejected',
			);
		}

		return array(
			'label' => __( 'Completado', 'bform' ),
			'class' => 'is-published',
		);
	}

	/**
	 * Resolve signature presence.
	 *
	 * @since    1.0.0
	 * @param    array $datos User data payload.
	 * @param    array $meta Metadata payload.
	 * @return   bool
	 */
	private function resolve_analytics_signature( $datos, $meta ) {
		$flat = array_merge( $this->flatten_analytics_payload( $datos ), $this->flatten_analytics_payload( $meta ) );

		foreach ( $flat as $entry ) {
			$key = isset( $entry['key'] ) ? $entry['key'] : '';
			$value = isset( $entry['value'] ) ? trim( (string) $entry['value'] ) : '';

			if ( '' === $value ) {
				continue;
			}

			if ( false !== strpos( $key, 'firma' ) || false !== strpos( $key, 'signature' ) || false !== strpos( $key, 'canvas' ) ) {
				return true;
			}

			if ( 0 === strpos( $value, 'data:image/' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Handle secured CRUD actions for Principal view.
	 *
	 * @since    1.0.0
	 */
	public function handle_principal_actions() {

		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$page = isset( $_REQUEST['page'] ) ? sanitize_key( wp_unslash( $_REQUEST['page'] ) ) : '';

		if ( 'bform-analiticas' === $page ) {
			$analytics_export_csv = ! empty( $_GET['export_csv'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['export_csv'] ) );
			if ( $analytics_export_csv ) {
				$analytics_filter_form_id = isset( $_GET['filter_form_id'] ) ? absint( $_GET['filter_form_id'] ) : 0;
				$analytics_search_term = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
				$this->export_analytics_csv( $analytics_filter_form_id, $analytics_search_term );
				return;
			}
		}

		if ( 'bform-principal' !== $page ) {
			return;
		}

		if ( empty( $_REQUEST['bform_action'] ) ) {
			return;
		}

		$action = sanitize_key( wp_unslash( $_REQUEST['bform_action'] ) );
		$notice_key = 'action_done';

		switch ( $action ) {
			case 'toggle_status':
			case 'duplicate_form':
			case 'delete_form':
				$form_id = isset( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : 0;
				if ( $form_id <= 0 ) {
					wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_form' ) );
					exit;
				}

				$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';
				if ( ! wp_verify_nonce( $nonce, 'bform_' . $action . '_' . $form_id ) ) {
					wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_nonce' ) );
					exit;
				}

				if ( 'toggle_status' === $action ) {
					$this->handle_toggle_status_action( $form_id );
					$notice_key = 'form_updated';
				}

				if ( 'duplicate_form' === $action ) {
					$this->handle_duplicate_action( $form_id );
					$notice_key = 'form_created';
				}

				if ( 'delete_form' === $action ) {
					$clean_responses = ! empty( $_REQUEST['clean_responses'] ) && '1' === sanitize_text_field( wp_unslash( $_REQUEST['clean_responses'] ) );
					$this->handle_delete_action( $form_id, $clean_responses );
					$notice_key = 'form_deleted';
				}
				break;
			case 'save_template':
				$this->handle_save_template_action();
				break;
			case 'delete_template':
				$this->handle_delete_template_action();
				break;
			case 'merge_templates':
				$this->handle_merge_templates_action();
				break;
			default:
				return;
		}

		wp_safe_redirect( $this->get_principal_redirect_url( $notice_key ) );
		exit;
	}

	/**
	 * Handle template create/update action.
	 *
	 * @since    1.0.0
	 */
	private function handle_save_template_action() {
		global $wpdb;

		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'bform_save_template' ) ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_nonce' ) );
			exit;
		}

		$template_id = isset( $_POST['template_id'] ) ? absint( $_POST['template_id'] ) : 0;
		$nombre = isset( $_POST['template_nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['template_nombre'] ) ) : '';
		$categoria = isset( $_POST['template_categoria'] ) ? sanitize_text_field( wp_unslash( $_POST['template_categoria'] ) ) : '';
		$esquema_json = isset( $_POST['template_esquema_json'] ) ? wp_unslash( $_POST['template_esquema_json'] ) : '';

		$decoded = json_decode( $esquema_json, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_json' ) );
			exit;
		}

		$data = array(
			'nombre' => $nombre,
			'esquema_json' => wp_json_encode( $decoded ),
			'categoria' => $categoria,
		);

		$table_name = $this->get_templates_table_name();

		if ( $template_id > 0 ) {
			$wpdb->update( $table_name, $data, array( 'id' => $template_id ), array( '%s', '%s', '%s' ), array( '%d' ) );
			return;
		}

		$data['fecha_creacion'] = current_time( 'mysql' );
		$wpdb->insert( $table_name, $data, array( '%s', '%s', '%s', '%s' ) );
	}

	/**
	 * Handle template delete action.
	 *
	 * @since    1.0.0
	 */
	private function handle_delete_template_action() {
		global $wpdb;

		$template_id = isset( $_REQUEST['template_id'] ) ? absint( $_REQUEST['template_id'] ) : 0;
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

		if ( $template_id <= 0 || ! wp_verify_nonce( $nonce, 'bform_delete_template_' . $template_id ) ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_nonce' ) );
			exit;
		}

		$wpdb->delete( $this->get_templates_table_name(), array( 'id' => $template_id ), array( '%d' ) );
	}

	/**
	 * Handle templates merge action.
	 *
	 * @since    1.0.0
	 */
	private function handle_merge_templates_action() {
		global $wpdb;

		$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'bform_merge_templates' ) ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'invalid_nonce' ) );
			exit;
		}

		$selected = isset( $_POST['merge_template_ids'] ) && is_array( $_POST['merge_template_ids'] ) ? array_map( 'absint', wp_unslash( $_POST['merge_template_ids'] ) ) : array();
		$selected = array_values( array_filter( $selected ) );

		if ( count( $selected ) < 2 ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'merge_needs_two' ) );
			exit;
		}

		$table_name = $this->get_templates_table_name();
		$placeholders = implode( ',', array_fill( 0, count( $selected ), '%d' ) );
		$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id IN ({$placeholders})", $selected );
		$templates = $wpdb->get_results( $query, ARRAY_A );

		$merged_schema = $this->merge_template_schemas( $templates );
		if ( is_wp_error( $merged_schema ) ) {
			wp_safe_redirect( $this->get_principal_redirect_url( 'merge_error' ) );
			exit;
		}

		$merged_name = 'Plantilla Combinada ' . current_time( 'Y-m-d H:i' );
		$wpdb->insert(
			$table_name,
			array(
				'nombre' => $merged_name,
				'esquema_json' => wp_json_encode( $merged_schema ),
				'categoria' => 'Combinadas',
				'fecha_creacion' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Merge multiple template schemas.
	 *
	 * @since    1.0.0
	 * @param    array $templates Selected templates.
	 * @return   array|WP_Error
	 */
	private function merge_template_schemas( $templates ) {
		$merged_sections = array();
		$merged_branching = array();
		$used_field_ids = array();
		$section_counter = 0;

		foreach ( $templates as $template ) {
			$schema_raw = isset( $template['esquema_json'] ) ? $template['esquema_json'] : '';
			$decoded = json_decode( $schema_raw, true );

			if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
				return new WP_Error( 'invalid_schema', __( 'Una o más plantillas tienen un esquema_json inválido.', 'bform' ) );
			}

			$sections = $this->extract_sections_from_schema( $decoded );
			$section_map = array();
			$field_map = array();

			foreach ( $sections as $index => $section ) {
				$old_section_id = $this->get_section_identifier( $section, $index + 1 );
				$section_counter++;
				$section_map[ (string) $old_section_id ] = 'section_' . $section_counter;
			}

			foreach ( $sections as $index => $section ) {
				$old_section_id = $this->get_section_identifier( $section, $index + 1 );
				$new_section_id = $section_map[ (string) $old_section_id ];

				$section['id'] = $new_section_id;
				if ( isset( $section['section_id'] ) ) {
					$section['section_id'] = $new_section_id;
				}

				$section['titulo'] = 'Sección ' . $section_counter;
				if ( isset( $section['title'] ) ) {
					$section['title'] = 'Sección ' . $section_counter;
				}

				$fields = isset( $section['fields'] ) && is_array( $section['fields'] ) ? $section['fields'] : array();
				foreach ( $fields as $field_index => $field ) {
					$old_field_id = $this->get_field_identifier( $field, $field_index + 1 );
					$new_field_id = $this->generate_unique_field_identifier( $old_field_id, $used_field_ids );
					$field_map[ (string) $old_field_id ] = $new_field_id;

					$fields[ $field_index ]['id'] = $new_field_id;
					if ( isset( $fields[ $field_index ]['field_id'] ) ) {
						$fields[ $field_index ]['field_id'] = $new_field_id;
					}
					if ( isset( $fields[ $field_index ]['name'] ) ) {
						$fields[ $field_index ]['name'] = $new_field_id;
					}
				}

				$section['fields'] = $fields;
				$section = $this->replace_schema_references( $section, $field_map, $section_map );
				$merged_sections[] = $section;
			}

			$root_branching = array();
			if ( isset( $decoded['branching_rules'] ) && is_array( $decoded['branching_rules'] ) ) {
				$root_branching = $decoded['branching_rules'];
			}

			if ( isset( $decoded['branching'] ) && is_array( $decoded['branching'] ) ) {
				$root_branching = array_merge( $root_branching, $decoded['branching'] );
			}

			$root_branching = $this->replace_schema_references( $root_branching, $field_map, $section_map );
			$merged_branching = array_merge( $merged_branching, $root_branching );
		}

		$renumbered_sections = array();
		foreach ( $merged_sections as $idx => $section ) {
			$step_number = $idx + 1;
			$section['titulo'] = 'Sección ' . $step_number;
			if ( isset( $section['title'] ) ) {
				$section['title'] = 'Sección ' . $step_number;
			}
			$renumbered_sections[] = $section;
		}

		return array(
			'sections' => $renumbered_sections,
			'branching_rules' => $merged_branching,
		);
	}

	/**
	 * Extract sections from a schema payload.
	 *
	 * @since    1.0.0
	 * @param    array $schema Decoded schema.
	 * @return   array
	 */
	private function extract_sections_from_schema( $schema ) {
		if ( isset( $schema['sections'] ) && is_array( $schema['sections'] ) ) {
			return array_values( $schema['sections'] );
		}

		if ( $this->is_list_array( $schema ) ) {
			return array_values( $schema );
		}

		return array();
	}

	/**
	 * Determine list-style array compatibility for older PHP versions.
	 *
	 * @since    1.0.0
	 * @param    array $array Input array.
	 * @return   bool
	 */
	private function is_list_array( $array ) {
		if ( function_exists( 'array_is_list' ) ) {
			return array_is_list( $array );
		}

		$expected = 0;
		foreach ( array_keys( $array ) as $key ) {
			if ( $key !== $expected ) {
				return false;
			}
			$expected++;
		}

		return true;
	}

	/**
	 * Resolve section identifier.
	 *
	 * @since    1.0.0
	 * @param    array $section Section data.
	 * @param    int   $fallback Fallback index.
	 * @return   string
	 */
	private function get_section_identifier( $section, $fallback ) {
		if ( isset( $section['id'] ) && '' !== (string) $section['id'] ) {
			return (string) $section['id'];
		}

		if ( isset( $section['section_id'] ) && '' !== (string) $section['section_id'] ) {
			return (string) $section['section_id'];
		}

		return 'section_' . $fallback;
	}

	/**
	 * Resolve field identifier.
	 *
	 * @since    1.0.0
	 * @param    array $field Field data.
	 * @param    int   $fallback Fallback index.
	 * @return   string
	 */
	private function get_field_identifier( $field, $fallback ) {
		if ( isset( $field['id'] ) && '' !== (string) $field['id'] ) {
			return sanitize_key( (string) $field['id'] );
		}

		if ( isset( $field['field_id'] ) && '' !== (string) $field['field_id'] ) {
			return sanitize_key( (string) $field['field_id'] );
		}

		if ( isset( $field['name'] ) && '' !== (string) $field['name'] ) {
			return sanitize_key( (string) $field['name'] );
		}

		return 'campo_' . $fallback;
	}

	/**
	 * Generate unique field identifier in merged schema.
	 *
	 * @since    1.0.0
	 * @param    string $base_id Base field id.
	 * @param    array  $used_ids Reference of used IDs.
	 * @return   string
	 */
	private function generate_unique_field_identifier( $base_id, &$used_ids ) {
		$base = sanitize_key( $base_id );
		if ( '' === $base ) {
			$base = 'campo';
		}

		$candidate = $base;
		$counter = 2;

		while ( in_array( $candidate, $used_ids, true ) ) {
			$candidate = $base . '_' . $counter;
			$counter++;
		}

		$used_ids[] = $candidate;
		return $candidate;
	}

	/**
	 * Replace field/section references recursively.
	 *
	 * @since    1.0.0
	 * @param    mixed $data Data to process.
	 * @param    array $field_map Field replacements.
	 * @param    array $section_map Section replacements.
	 * @return   mixed
	 */
	private function replace_schema_references( $data, $field_map, $section_map ) {
		if ( is_array( $data ) ) {
			$processed = array();
			foreach ( $data as $key => $value ) {
				$processed[ $key ] = $this->replace_schema_references( $value, $field_map, $section_map );
			}
			return $processed;
		}

		if ( is_string( $data ) || is_numeric( $data ) ) {
			$lookup = (string) $data;
			if ( isset( $field_map[ $lookup ] ) ) {
				return $field_map[ $lookup ];
			}
			if ( isset( $section_map[ $lookup ] ) ) {
				return $section_map[ $lookup ];
			}
		}

		return $data;
	}

	/**
	 * Execute form status toggle.
	 *
	 * @since    1.0.0
	 * @param    int $form_id Form ID.
	 */
	private function handle_toggle_status_action( $form_id ) {
		global $wpdb;

		$table_name = $this->get_forms_table_name();
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $form_id ) );

		if ( ! $form ) {
			return;
		}

		$current_status = isset( $form->activo ) ? (int) $form->activo : 1;
		$new_status = $current_status ? 0 : 1;

		$wpdb->update(
			$table_name,
			array(
				'activo' => $new_status,
			),
			array( 'id' => $form_id ),
			array( '%d' ),
			array( '%d' )
		);
	}

	/**
	 * Execute deep form duplication.
	 *
	 * @since    1.0.0
	 * @param    int $form_id Form ID.
	 */
	private function handle_duplicate_action( $form_id ) {
		global $wpdb;

		$table_name = $this->get_forms_table_name();
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $form_id ), ARRAY_A );

		if ( empty( $form ) ) {
			return;
		}

		unset( $form['id'] );

		$original_name = isset( $form['nombre'] ) ? $form['nombre'] : 'Formulario';
		$form['nombre'] = $this->generate_unique_form_name( $original_name . ' (Copia)' );

		$base_slug = sanitize_title( $form['nombre'] );
		$form['slug_shortcode'] = $this->generate_unique_slug( $base_slug );
		$form['fecha_creacion'] = current_time( 'mysql' );
		$form['fecha_actualizacion'] = current_time( 'mysql' );

		if ( $this->table_has_column( $table_name, 'form_key' ) ) {
			$form['form_key'] = $this->generate_unique_form_key( $table_name );
		}

		if ( ! isset( $form['activo'] ) ) {
			$form['activo'] = 1;
		}

		$format = array();
		foreach ( $form as $key => $value ) {
			$format[] = is_numeric( $value ) ? '%d' : '%s';
		}

		$wpdb->insert( $table_name, $form, $format );
	}

	/**
	 * Execute delete action and optional responses cleanup.
	 *
	 * @since    1.0.0
	 * @param    int  $form_id          Form ID.
	 * @param    bool $clean_responses  Remove linked responses.
	 */
	private function handle_delete_action( $form_id, $clean_responses ) {
		global $wpdb;

		$forms_table = $this->get_forms_table_name();
		$responses_table = $this->get_responses_table_name();

		$wpdb->delete( $forms_table, array( 'id' => $form_id ), array( '%d' ) );

		if ( $clean_responses ) {
			$wpdb->delete( $responses_table, array( 'form_id' => $form_id ), array( '%d' ) );
		}
	}

	/**
	 * Retrieve forms for Principal listing.
	 *
	 * @since    1.0.0
	 * @return   array|WP_Error
	 */
	private function get_principal_forms() {
		global $wpdb;

		$this->ensure_plugin_tables_schema();

		$table_name = $this->get_forms_table_name();

		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );
		if ( $table_exists !== $table_name ) {
			return new WP_Error( 'missing_table', __( 'La tabla plugin_uleam_forms no existe. Reactiva el plugin para crearla.', 'bform' ) );
		}

		$order_by = $this->table_has_column( $table_name, 'fecha_creacion' ) ? 'fecha_creacion DESC' : 'id DESC';
		$sql = "SELECT * FROM {$table_name} ORDER BY {$order_by}";
		$forms = $wpdb->get_results( $sql, ARRAY_A );

		return is_array( $forms ) ? $forms : array();
	}

	/**
	 * Retrieve templates for Principal library.
	 *
	 * @since    1.0.0
	 * @return   array|WP_Error
	 */
	private function get_principal_templates() {
		global $wpdb;

		$table_name = $this->get_templates_table_name();
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( $table_exists !== $table_name ) {
			return new WP_Error( 'missing_templates_table', __( 'La tabla plugin_uleam_templates no existe. Reactiva el plugin para crearla.', 'bform' ) );
		}

		$sql = "SELECT * FROM plugin_uleam_templates ORDER BY fecha_creacion DESC";
		$templates = $wpdb->get_results( $sql, ARRAY_A );

		return is_array( $templates ) ? $templates : array();
	}

	/**
	 * Get one template by ID.
	 *
	 * @since    1.0.0
	 * @param    int $template_id Template ID.
	 * @return   array|null
	 */
	private function get_template_by_id( $template_id ) {
		global $wpdb;

		if ( $template_id <= 0 ) {
			return null;
		}

		$template = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->get_templates_table_name()} WHERE id = %d", $template_id ), ARRAY_A );
		return is_array( $template ) ? $template : null;
	}

	/**
	 * Get one form by ID.
	 *
	 * @since    1.0.0
	 * @param    int $form_id Form ID.
	 * @return   array|null
	 */
	private function get_form_by_id( $form_id ) {
		global $wpdb;

		if ( $form_id <= 0 ) {
			return null;
		}

		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->get_forms_table_name()} WHERE id = %d", $form_id ), ARRAY_A );
		return is_array( $form ) ? $form : null;
	}

	/**
	 * Build principal page redirect URL with notice.
	 *
	 * @since    1.0.0
	 * @param    string $notice Notice key.
	 * @return   string
	 */
	private function get_principal_redirect_url( $notice ) {
		return add_query_arg(
			array(
				'page' => 'bform-principal',
				'bform_notice' => sanitize_key( $notice ),
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Generate unique slug for duplicated forms.
	 *
	 * @since    1.0.0
	 * @param    string $base_slug Base slug.
	 * @return   string
	 */
	private function generate_unique_slug( $base_slug ) {
		global $wpdb;

		$table_name = $this->get_forms_table_name();
		$candidate = $base_slug;
		$counter = 1;

		while ( true ) {
			$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE slug_shortcode = %s LIMIT 1", $candidate ) );

			if ( empty( $exists ) ) {
				return $candidate;
			}

			$candidate = $base_slug . '-' . $counter;
			$counter ++;
		}
	}

	/**
	 * Generate unique form key for legacy schemas.
	 *
	 * @since    1.0.0
	 * @param    string $table_name Forms table name.
	 * @return   string
	 */
	private function generate_unique_form_key( $table_name ) {
		global $wpdb;

		$attempt = 0;
		do {
			$attempt++;
			$candidate = 'bf_' . wp_generate_password( 20, false, false );
			$exists = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM {$table_name} WHERE form_key = %s LIMIT 1",
					$candidate
				)
			);
		} while ( ! empty( $exists ) && $attempt < 20 );

		if ( empty( $exists ) ) {
			return $candidate;
		}

		return 'bf_' . wp_generate_uuid4();
	}

	/**
	 * Check if a form name already exists.
	 *
	 * @since    1.0.0
	 * @param    string $form_name       Form name.
	 * @param    int    $exclude_form_id Optional form ID to exclude.
	 * @return   bool
	 */
	private function form_name_exists( $form_name, $exclude_form_id = 0 ) {
		global $wpdb;

		$normalized_name = trim( preg_replace( '/\s+/', ' ', (string) $form_name ) );
		if ( '' === $normalized_name ) {
			return false;
		}

		$table_name = $this->get_forms_table_name();
		$exclude_form_id = absint( $exclude_form_id );

		if ( $exclude_form_id > 0 ) {
			$query = $wpdb->prepare(
				"SELECT id FROM {$table_name} WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(%s)) AND id <> %d LIMIT 1",
				$normalized_name,
				$exclude_form_id
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT id FROM {$table_name} WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(%s)) LIMIT 1",
				$normalized_name
			);
		}

		$exists = $wpdb->get_var( $query );
		return ! empty( $exists );
	}

	/**
	 * Generate unique form name using incremental suffix.
	 *
	 * @since    1.0.0
	 * @param    string $base_name Base form name.
	 * @return   string
	 */
	private function generate_unique_form_name( $base_name ) {
		$normalized_base = trim( preg_replace( '/\s+/', ' ', (string) $base_name ) );
		if ( '' === $normalized_base ) {
			$normalized_base = __( 'Formulario sin título', 'bform' );
		}

		if ( ! $this->form_name_exists( $normalized_base ) ) {
			return $normalized_base;
		}

		$counter = 2;
		while ( $counter < 10000 ) {
			$candidate = $normalized_base . ' (' . $counter . ')';
			if ( ! $this->form_name_exists( $candidate ) ) {
				return $candidate;
			}
			$counter++;
		}

		return $normalized_base . ' (' . time() . ')';
	}

	/**
	 * Get forms table name.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_forms_table_name() {
		return 'plugin_uleam_forms';
	}

	/**
	 * Ensure required plugin tables and columns exist.
	 *
	 * @since    1.0.0
	 */
	private function ensure_plugin_tables_schema() {
		if ( ! class_exists( 'Bform_Activator' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bform-activator.php';
		}

		if ( class_exists( 'Bform_Activator' ) ) {
			Bform_Activator::activate();
		}
	}

	/**
	 * Check whether a table has a specific column.
	 *
	 * @since    1.0.0
	 * @param    string $table_name  Table name.
	 * @param    string $column_name Column name.
	 * @return   bool
	 */
	private function table_has_column( $table_name, $column_name ) {
		global $wpdb;

		$column_name = sanitize_key( (string) $column_name );
		if ( '' === $column_name ) {
			return false;
		}

		$exists = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$table_name} LIKE %s", $column_name ) );

		return ! empty( $exists );
	}

	/**
	 * Get templates table name.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_templates_table_name() {
		return 'plugin_uleam_templates';
	}

	/**
	 * Get responses table name.
	 *
	 * @since    1.0.0
	 * @return   string
	 */
	private function get_responses_table_name() {
		return 'plugin_uleam_respuestas';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook_suffix ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bform_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bform_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		$style_path = plugin_dir_path( __FILE__ ) . 'css/bform-admin.css';
		$style_version = file_exists( $style_path ) ? (string) filemtime( $style_path ) : $this->version;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bform-admin.css', array(), $style_version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook_suffix ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bform_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bform_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! $this->is_plugin_screen( $hook_suffix ) ) {
			return;
		}

		$script_path = plugin_dir_path( __FILE__ ) . 'js/bform-admin.js';
		$script_version = file_exists( $script_path ) ? (string) filemtime( $script_path ) : $this->version;

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bform-admin.js', array( 'jquery' ), $script_version, false );

		wp_localize_script(
			$this->plugin_name,
			'bformAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'logicPageUrl' => admin_url( 'admin.php?page=bform-logica' ),
				'saveNonce' => wp_create_nonce( 'bform_constructor_save' ),
				'logicSaveNonce' => wp_create_nonce( 'bform_logic_save' ),
				'analyticsModalNonce' => wp_create_nonce( 'bform_analytics_modal' ),
				'currentUserId' => get_current_user_id(),
				'allowedDateFormats' => array( 'DD/MM/YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD' ),
				'logicOperators' => array( 'equals', 'contains' ),
				'noticeMessages' => array(
					'form_created' => __( 'Formulario creado correctamente.', 'bform' ),
					'form_updated' => __( 'Formulario actualizado correctamente.', 'bform' ),
					'form_deleted' => __( 'Formulario eliminado correctamente.', 'bform' ),
					'action_done' => __( 'Acción ejecutada correctamente.', 'bform' ),
				),
			)
		);

	}

	/**
	 * Determine whether current admin screen belongs to this plugin.
	 *
	 * @since    1.0.0
	 * @param    string $hook_suffix Current admin page hook suffix.
	 * @return   bool
	 */
	private function is_plugin_screen( $hook_suffix ) {

		if ( empty( $hook_suffix ) ) {
			return false;
		}

		if ( ! empty( $this->page_hook_suffix ) && $hook_suffix === $this->page_hook_suffix ) {
			return true;
		}

		if ( ! empty( $this->principal_hook_suffix ) && $hook_suffix === $this->principal_hook_suffix ) {
			return true;
		}

		if ( ! empty( $this->constructor_hook_suffix ) && $hook_suffix === $this->constructor_hook_suffix ) {
			return true;
		}

		if ( ! empty( $this->logic_hook_suffix ) && $hook_suffix === $this->logic_hook_suffix ) {
			return true;
		}

		if ( ! empty( $this->analytics_hook_suffix ) && $hook_suffix === $this->analytics_hook_suffix ) {
			return true;
		}

		return false;

	}

}
