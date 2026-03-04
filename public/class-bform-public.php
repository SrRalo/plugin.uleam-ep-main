<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bform
 * @subpackage Bform/public
 * @author     Sr.Ralo <adreloa@gmail.com>
 */
class Bform_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bform-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bform-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register plugin shortcode.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'uleam_form', array( $this, 'render_form_shortcode' ) );
	}

	/**
	 * Render form shortcode output.
	 *
	 * @since    1.0.0
	 * @param    array $atts Shortcode attributes.
	 * @return   string
	 */
	public function render_form_shortcode( $atts ) {
		global $wpdb;

		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'uleam_form'
		);

		$form_id = absint( $atts['id'] );
		if ( $form_id <= 0 ) {
			return '';
		}

		$table_name = 'plugin_uleam_forms';
		$form = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $form_id ) );

		if ( ! $form ) {
			return '<div class="bform-unavailable-message">' . esc_html__( 'Formulario no disponible', 'bform' ) . '</div>';
		}

		$is_active = isset( $form->activo ) ? (int) $form->activo === 1 : true;

		if ( ! $is_active ) {
			return '<div class="bform-unavailable-message">' . esc_html__( 'Formulario no disponible', 'bform' ) . '</div>';
		}

		$form_name = ! empty( $form->nombre ) ? $form->nombre : __( 'Formulario', 'bform' );
		$schema    = json_decode( isset( $form->esquema_json ) ? $form->esquema_json : '', true );

		if ( ! is_array( $schema ) ) {
			$schema = array(
				'sections'        => array(),
				'branching_rules' => array(),
			);
		}

		$sections = isset( $schema['sections'] ) && is_array( $schema['sections'] ) ? $schema['sections'] : array();
		if ( empty( $sections ) ) {
			return '<div class="bform-render-placeholder"><h3>' . esc_html( $form_name ) . '</h3><p>' . esc_html__( 'Este formulario está listo para usarse.', 'bform' ) . '</p></div>';
		}

		$schema_json = wp_json_encode( $schema );
		if ( false === $schema_json ) {
			$schema_json = '{}';
		}

		$submitted_flag = isset( $_GET['bform_submitted'] ) ? sanitize_text_field( wp_unslash( $_GET['bform_submitted'] ) ) : '';
		$submitted_form_id = isset( $_GET['bform_form_id'] ) ? absint( $_GET['bform_form_id'] ) : 0;
		$show_success_notice = ( '1' === $submitted_flag && $submitted_form_id === $form_id );

		$action_url = admin_url( 'admin-post.php' );

		$html  = '';
		if ( $show_success_notice ) {
			$html .= '<div class="bform-success-message" role="status" aria-live="polite">' . esc_html__( 'Formulario enviado correctamente.', 'bform' ) . '</div>';
		}

		$html .= '<form class="bform-runtime-form" data-bform-schema="' . esc_attr( $schema_json ) . '" method="post" action="' . esc_url( $action_url ) . '" enctype="multipart/form-data">';
		$html .= '<h3>' . esc_html( $form_name ) . '</h3>';
		$html .= '<input type="hidden" name="action" value="bform_submit_form" />';
		$html .= '<input type="hidden" name="bform_form_id" value="' . esc_attr( (string) $form_id ) . '" />';
		$html .= wp_nonce_field( 'bform_submit_' . $form_id, 'bform_submission_nonce', true, false );

		foreach ( $sections as $section ) {
			if ( ! is_array( $section ) ) {
				continue;
			}

			$section_id    = isset( $section['id'] ) ? sanitize_key( $section['id'] ) : '';
			$section_title = isset( $section['title'] ) ? $section['title'] : __( 'Sección', 'bform' );
			$fields        = isset( $section['fields'] ) && is_array( $section['fields'] ) ? $section['fields'] : array();

			if ( '' === $section_id ) {
				continue;
			}

			$html .= '<section class="bform-runtime-section" data-section-id="' . esc_attr( $section_id ) . '">';
			$html .= '<h4>' . esc_html( $section_title ) . '</h4>';

			foreach ( $fields as $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				$html .= $this->render_public_field( $field );
			}

			$html .= '</section>';
		}

		$html .= '<div class="bform-runtime-actions">';
		$html .= '<button type="button" class="bform-runtime-back" disabled="disabled">' . esc_html__( 'Regresar', 'bform' ) . '</button>';
		$html .= '<button type="button" class="bform-runtime-next" disabled="disabled">' . esc_html__( 'Siguiente', 'bform' ) . '</button>';
		$html .= '<button type="submit" class="bform-runtime-submit" hidden="hidden" disabled="disabled">' . esc_html__( 'Enviar formulario', 'bform' ) . '</button>';
		$html .= '</div>';

		$html .= '</form>';

		return $html;
	}

	/**
	 * Persist public form submission into responses table.
	 *
	 * @since    1.0.0
	 */
	public function handle_form_submission() {
		global $wpdb;

		if ( 'POST' !== strtoupper( isset( $_SERVER['REQUEST_METHOD'] ) ? (string) $_SERVER['REQUEST_METHOD'] : '' ) ) {
			wp_die( esc_html__( 'Método no permitido.', 'bform' ), 405 );
		}

		$form_id = isset( $_POST['bform_form_id'] ) ? absint( $_POST['bform_form_id'] ) : 0;
		$nonce = isset( $_POST['bform_submission_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['bform_submission_nonce'] ) ) : '';

		if ( $form_id <= 0 || ! wp_verify_nonce( $nonce, 'bform_submit_' . $form_id ) ) {
			wp_die( esc_html__( 'No se pudo validar el envío del formulario.', 'bform' ), 400 );
		}

		$forms_table = 'plugin_uleam_forms';
		$responses_table = 'plugin_uleam_respuestas';

		$form = $wpdb->get_row( $wpdb->prepare( "SELECT id, activo, esquema_json FROM {$forms_table} WHERE id = %d", $form_id ), ARRAY_A );
		if ( ! is_array( $form ) ) {
			wp_die( esc_html__( 'Formulario no encontrado.', 'bform' ), 404 );
		}

		$is_active = isset( $form['activo'] ) ? (int) $form['activo'] === 1 : true;
		if ( ! $is_active ) {
			wp_die( esc_html__( 'Formulario no disponible.', 'bform' ), 403 );
		}

		$schema = json_decode( isset( $form['esquema_json'] ) ? (string) $form['esquema_json'] : '', true );
		$sections = isset( $schema['sections'] ) && is_array( $schema['sections'] ) ? $schema['sections'] : array();
		$client_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '';
		$client_ip = is_string( $client_ip ) && filter_var( $client_ip, FILTER_VALIDATE_IP ) ? $client_ip : '';

		$datos_usuario = array();
		$missing_required_fields = array();
		foreach ( $sections as $section ) {
			if ( ! is_array( $section ) || empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
				continue;
			}

			foreach ( $section['fields'] as $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}

				$field_id = isset( $field['id'] ) ? sanitize_key( $field['id'] ) : '';
				if ( '' === $field_id ) {
					continue;
				}

				$field_type = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
				$input_name = 'bform_' . $field_id;
				$field_options = $this->get_field_option_values( $field );
				$field_is_required = $this->is_field_required( $field );
				$field_is_display_only_textarea = $this->is_textarea_display_only( $field );
				$radio_other_enabled = $this->is_radio_other_option_enabled( $field );
				$radio_other_value = '__bform_other__';
				$radio_other_input_name = $input_name . '_other';
				$field_label = isset( $field['label'] ) ? sanitize_text_field( (string) $field['label'] ) : __( 'Campo', 'bform' );

				if ( $field_is_display_only_textarea ) {
					continue;
				}

				if ( 'file' === $field_type ) {
					$clean_file_value = $this->sanitize_uploaded_file_value( $input_name, $field );
					if ( '' !== $clean_file_value ) {
						$datos_usuario[ $field_id ] = $clean_file_value;
					} elseif ( $field_is_required ) {
						$missing_required_fields[] = $field_label;
					}
					continue;
				}

				$raw_value = isset( $_POST[ $input_name ] ) ? wp_unslash( $_POST[ $input_name ] ) : null;

				if ( null === $raw_value ) {
					if ( $field_is_required ) {
						$missing_required_fields[] = $field_label;
					}
					continue;
				}

				if ( is_array( $raw_value ) ) {
					if ( 'checkbox' !== $field_type ) {
						continue;
					}

					$clean_values = array();
					foreach ( $raw_value as $item ) {
						$clean_item = sanitize_text_field( (string) $item );
						if ( ! empty( $field_options ) && ! in_array( $clean_item, $field_options, true ) ) {
							continue;
						}
						if ( '' !== $clean_item ) {
							$clean_values[] = $clean_item;
						}
					}

					if ( ! empty( $clean_values ) ) {
						$datos_usuario[ $field_id ] = $clean_values;
					} elseif ( $field_is_required ) {
						$missing_required_fields[] = $field_label;
					}
					continue;
				}

				$raw_value = (string) $raw_value;
				$clean_value = '';

				switch ( $field_type ) {
					case 'email':
						$candidate = sanitize_email( $raw_value );
						$clean_value = is_email( $candidate ) ? $candidate : '';
						break;
					case 'textarea':
						$clean_value = substr( sanitize_textarea_field( $raw_value ), 0, 5000 );
						break;
					case 'number':
						$clean_value = $this->sanitize_number_value( $raw_value, $field );
						break;
					case 'date':
						$clean_value = $this->sanitize_date_value( $raw_value, $field );
						break;
					case 'radio':
						$candidate = sanitize_text_field( $raw_value );
						if ( $radio_other_enabled && $radio_other_value === $candidate ) {
							$raw_other_value = isset( $_POST[ $radio_other_input_name ] ) ? wp_unslash( $_POST[ $radio_other_input_name ] ) : '';
							$clean_other_value = substr( sanitize_text_field( (string) $raw_other_value ), 0, 255 );
							$clean_value = '' !== $clean_other_value ? $clean_other_value : '';
							break;
						}

						if ( ! empty( $field_options ) ) {
							$clean_value = in_array( $candidate, $field_options, true ) ? $candidate : '';
						}
						break;
					case 'select':
						$candidate = sanitize_text_field( $raw_value );
						if ( ! empty( $field_options ) ) {
							$clean_value = in_array( $candidate, $field_options, true ) ? $candidate : '';
						}
						break;
					case 'link':
						$candidate = esc_url_raw( $raw_value );
						$clean_value = wp_http_validate_url( $candidate ) ? $candidate : '';
						break;
					case 'canvas':
						$clean_value = $this->sanitize_canvas_value( $raw_value );
						break;
					default:
						$clean_value = substr( sanitize_text_field( $raw_value ), 0, 255 );
						break;
				}

				if ( '' !== $clean_value ) {
					$datos_usuario[ $field_id ] = $clean_value;
				} elseif ( $field_is_required ) {
					$missing_required_fields[] = $field_label;
				}
			}
		}

		if ( ! empty( $missing_required_fields ) ) {
			wp_die( esc_html__( 'Completa los campos obligatorios antes de enviar el formulario.', 'bform' ), 400 );
		}

		$metadatos = array(
			'submitted_from' => esc_url_raw( wp_get_referer() ),
			'ip' => $client_ip,
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			'status' => 'submitted',
			'user_id' => get_current_user_id(),
		);

		$wpdb->insert(
			$responses_table,
			array(
				'form_id' => $form_id,
				'datos_usuario' => wp_json_encode( $datos_usuario ),
				'metadatos' => wp_json_encode( $metadatos ),
			),
			array( '%d', '%s', '%s' )
		);

		$redirect_url = wp_get_referer();
		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/' );
		}

		$redirect_url = add_query_arg(
			array(
				'bform_submitted' => '1',
				'bform_form_id' => $form_id,
			),
			$redirect_url
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Render a public field from schema definition.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   string
	 */
	private function render_public_field( $field ) {
		$field_id    = isset( $field['id'] ) ? sanitize_key( $field['id'] ) : '';
		$field_type  = isset( $field['type'] ) ? sanitize_key( $field['type'] ) : 'text';
		$field_label = isset( $field['label'] ) && '' !== $field['label'] ? $field['label'] : __( 'Campo', 'bform' );
		$settings    = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();

		if ( '' === $field_id ) {
			return '';
		}

		$input_name = 'bform_' . $field_id;
		$textarea_display_only = $this->is_textarea_display_only( $field );
		$field_label_plain = sanitize_text_field( wp_strip_all_tags( (string) $field_label ) );
		if ( '' === $field_label_plain ) {
			$field_label_plain = __( 'Campo', 'bform' );
		}
		$field_label_html = esc_html( $field_label_plain );
		if ( 'textarea' === $field_type ) {
			$label_markup = $this->sanitize_textarea_label_markup( (string) $field_label );
			if ( '' !== trim( wp_strip_all_tags( $label_markup ) ) ) {
				$field_label_html = $label_markup;
			}
		}

		$description_enabled = ! empty( $settings['description_enabled'] );
		$description_text = isset( $settings['description_text'] ) ? $this->sanitize_field_description_markup( (string) $settings['description_text'] ) : '';
		$description_has_content = '' !== trim( wp_strip_all_tags( $description_text ) );
		$default_placeholder = esc_attr__( 'Escriba aqui...', 'bform' );
		$is_required = $this->is_field_required( $field );
		$required_data_attr = ( $is_required && ! $textarea_display_only ) ? '1' : '0';
		$field_wrapper_class = 'bform-runtime-field';
		if ( $textarea_display_only ) {
			$field_wrapper_class .= ' bform-runtime-field--textarea-display-only';
		}
		$html       = '<div class="' . esc_attr( $field_wrapper_class ) . '">';
		$html      .= '<label for="' . esc_attr( $input_name ) . '">' . $field_label_html . '</label>';
		if ( $description_enabled && $description_has_content ) {
			$html .= '<p class="bform-runtime-field-description">' . $description_text . '</p>';
		}

			switch ( $field_type ) {
			case 'textarea':
				$textarea_attrs = ' id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '"';
				if ( $textarea_display_only ) {
					$textarea_attrs .= ' disabled="disabled" aria-disabled="true" class="bform-runtime-textarea-display-only"';
				}
				$html .= '<textarea' . $textarea_attrs . '></textarea>';
				break;
			case 'email':
				$html .= '<input type="email" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				break;
			case 'number':
				$number_preset = $this->get_number_preset( $field );
				if ( in_array( $number_preset, array( 'cedula_10', 'telefono_10' ), true ) ) {
					$title_message = 'cedula_10' === $number_preset
						? __( 'Ingresa exactamente 10 dígitos para la cédula.', 'bform' )
						: __( 'Ingresa exactamente 10 dígitos para el teléfono.', 'bform' );

					$html .= '<input type="text" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . $default_placeholder . '" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" title="' . esc_attr( $title_message ) . '" data-number-preset="' . esc_attr( $number_preset ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				} elseif ( 'edad_1_150' === $number_preset ) {
					$html .= '<input type="number" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . $default_placeholder . '" min="1" max="150" step="1" title="' . esc_attr__( 'Ingresa una edad válida entre 1 y 150 años.', 'bform' ) . '" data-number-preset="' . esc_attr( $number_preset ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				} elseif ( 'decimal_2' === $number_preset ) {
					$html .= '<input type="number" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . $default_placeholder . '" step="0.01" inputmode="decimal" title="' . esc_attr__( 'Ingresa un número con máximo 2 decimales.', 'bform' ) . '" data-number-preset="' . esc_attr( $number_preset ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				} else {
					$html .= '<input type="number" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . $default_placeholder . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				}
				break;
				case 'radio':
				$options = isset( $settings['options'] ) && is_array( $settings['options'] ) ? $settings['options'] : array();
				if ( empty( $options ) ) {
					$options = array( __( 'Opción', 'bform' ) );
				}
				foreach ( $options as $option_index => $option_label ) {
					$option_value = sanitize_text_field( (string) $option_label );
					$option_id = $input_name . '_' . ( $option_index + 1 );
					$html .= '<label class="bform-runtime-choice" for="' . esc_attr( $option_id ) . '">';
						$html .= '<input type="radio" id="' . esc_attr( $option_id ) . '" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $option_value ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" /> ';
					$html .= esc_html( $option_value );
					$html .= '</label>';
				}

					if ( $this->is_radio_other_option_enabled( $field ) ) {
						$other_option_value = '__bform_other__';
						$other_option_id = $input_name . '_other';
						$other_text_input_id = $input_name . '_other_text';

						$html .= '<label class="bform-runtime-choice bform-runtime-choice--other" for="' . esc_attr( $other_option_id ) . '">';
						$html .= '<input type="radio" id="' . esc_attr( $other_option_id ) . '" name="' . esc_attr( $input_name ) . '" value="' . esc_attr( $other_option_value ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" data-other-radio="1" data-other-input-id="' . esc_attr( $other_text_input_id ) . '" /> ';
						$html .= esc_html__( 'Otros', 'bform' );
						$html .= '</label>';
						$html .= '<input type="text" id="' . esc_attr( $other_text_input_id ) . '" name="' . esc_attr( $input_name . '_other' ) . '" class="bform-runtime-choice-other-input" placeholder="' . esc_attr__( 'Especifica tu respuesta', 'bform' ) . '" data-other-for-field-id="' . esc_attr( $field_id ) . '" disabled="disabled" />';
					}
					break;
				case 'checkbox':
					$options = isset( $settings['options'] ) && is_array( $settings['options'] ) ? $settings['options'] : array();
					if ( empty( $options ) ) {
						$options = array( __( 'Opción', 'bform' ) );
					}
					foreach ( $options as $option_index => $option_label ) {
						$option_value = sanitize_text_field( (string) $option_label );
						$option_id = $input_name . '_' . ( $option_index + 1 );
						$html .= '<label class="bform-runtime-choice" for="' . esc_attr( $option_id ) . '">';
						$html .= '<input type="checkbox" id="' . esc_attr( $option_id ) . '" name="' . esc_attr( $input_name . '[]' ) . '" value="' . esc_attr( $option_value ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" /> ';
						$html .= esc_html( $option_value );
						$html .= '</label>';
					}
				break;
			case 'select':
				$options = isset( $settings['options'] ) && is_array( $settings['options'] ) ? $settings['options'] : array();
				$html .= '<select id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '">';
				foreach ( $options as $option_label ) {
					$option_value = sanitize_text_field( (string) $option_label );
					$html .= '<option value="' . esc_attr( $option_value ) . '">' . esc_html( $option_value ) . '</option>';
				}
				$html .= '</select>';
				break;
			case 'date':
				$html .= '<input type="date" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				break;
			case 'file':
				$file_extensions = $this->get_allowed_file_extensions( $field );
				$accept_values = array();
				foreach ( $file_extensions as $extension ) {
					if ( 'jpg' === $extension ) {
						$accept_values[] = '.jpg';
						$accept_values[] = '.jpeg';
						continue;
					}
					$accept_values[] = '.' . $extension;
				}
				$accept_attr = implode( ',', array_values( array_unique( $accept_values ) ) );
				$html .= '<input type="file" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" accept="' . esc_attr( $accept_attr ) . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				break;
			case 'link':
				$link_url = isset( $settings['url'] ) ? esc_url( $settings['url'] ) : '';
				$link_text = isset( $settings['text'] ) && '' !== $settings['text'] ? sanitize_text_field( $settings['text'] ) : __( 'Abrir enlace', 'bform' );
				$link_target = isset( $settings['target'] ) && '_blank' === $settings['target'] ? '_blank' : '_self';
				if ( '' !== $link_url ) {
					$html .= '<a class="bform-runtime-link" href="' . esc_url( $link_url ) . '" target="' . esc_attr( $link_target ) . '" rel="noopener noreferrer">' . esc_html( $link_text ) . '</a>';
				}
				break;
			case 'canvas':
				$html .= '<canvas class="bform-runtime-canvas" data-field-id="' . esc_attr( $field_id ) . '" width="320" height="120"></canvas>';
				break;
			default:
				$html .= '<input type="text" id="' . esc_attr( $input_name ) . '" name="' . esc_attr( $input_name ) . '" placeholder="' . $default_placeholder . '" data-field-id="' . esc_attr( $field_id ) . '" data-required="' . esc_attr( $required_data_attr ) . '" />';
				break;
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Extract and sanitize allowed option values for choice fields.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   array
	 */
	private function get_field_option_values( $field ) {
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$options = isset( $settings['options'] ) && is_array( $settings['options'] ) ? $settings['options'] : array();

		$clean_options = array();
		foreach ( $options as $option ) {
			$clean_option = sanitize_text_field( (string) $option );
			if ( '' !== $clean_option ) {
				$clean_options[] = $clean_option;
			}
		}

		return array_values( array_unique( $clean_options ) );
	}

	/**
	 * Resolve number preset from field settings.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   string
	 */
	private function get_number_preset( $field ) {
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$preset = isset( $settings['number_preset'] ) ? sanitize_key( (string) $settings['number_preset'] ) : 'none';

		if ( ! in_array( $preset, array( 'none', 'cedula_10', 'telefono_10', 'edad_1_150', 'decimal_2' ), true ) ) {
			$preset = 'none';
		}

		return $preset;
	}

	/**
	 * Sanitize and validate number value based on preset.
	 *
	 * @since    1.0.0
	 * @param    string $raw_value Raw submitted value.
	 * @param    array  $field     Field definition.
	 * @return   string
	 */
	private function sanitize_number_value( $raw_value, $field ) {
		$candidate = trim( sanitize_text_field( (string) $raw_value ) );
		$preset = $this->get_number_preset( $field );

		if ( in_array( $preset, array( 'cedula_10', 'telefono_10' ), true ) ) {
			return preg_match( '/^\d{10}$/', $candidate ) ? $candidate : '';
		}

		if ( 'edad_1_150' === $preset ) {
			if ( ! preg_match( '/^\d+$/', $candidate ) ) {
				return '';
			}

			$age = (int) $candidate;
			if ( $age < 1 || $age > 150 ) {
				return '';
			}

			return (string) $age;
		}

		if ( 'decimal_2' === $preset ) {
			return preg_match( '/^\d+(?:\.\d{1,2})?$/', $candidate ) ? $candidate : '';
		}

		return preg_match( '/^-?\d+(?:\.\d+)?$/', $candidate ) ? $candidate : '';
	}

	/**
	 * Sanitize and validate date values based on configured format.
	 *
	 * @since    1.0.0
	 * @param    string $raw_value Raw submitted value.
	 * @param    array  $field     Field definition.
	 * @return   string
	 */
	private function sanitize_date_value( $raw_value, $field ) {
		$candidate = trim( sanitize_text_field( (string) $raw_value ) );
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$format = isset( $settings['format'] ) ? sanitize_text_field( (string) $settings['format'] ) : 'YYYY-MM-DD';

		if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $candidate, $matches ) ) {
			$year = (int) $matches[1];
			$month = (int) $matches[2];
			$day = (int) $matches[3];

			if ( checkdate( $month, $day, $year ) ) {
				return sprintf( '%04d-%02d-%02d', $year, $month, $day );
			}
		}

		if ( 'DD/MM/YYYY' === $format && preg_match( '/^(\d{2})\/(\d{2})\/(\d{4})$/', $candidate, $matches ) ) {
			$day = (int) $matches[1];
			$month = (int) $matches[2];
			$year = (int) $matches[3];

			if ( checkdate( $month, $day, $year ) ) {
				return sprintf( '%02d/%02d/%04d', $day, $month, $year );
			}
		}

		if ( 'MM/DD/YYYY' === $format && preg_match( '/^(\d{2})\/(\d{2})\/(\d{4})$/', $candidate, $matches ) ) {
			$month = (int) $matches[1];
			$day = (int) $matches[2];
			$year = (int) $matches[3];

			if ( checkdate( $month, $day, $year ) ) {
				return sprintf( '%02d/%02d/%04d', $month, $day, $year );
			}
		}

		return '';
	}

	/**
	 * Get allowed file extensions from field settings.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   array
	 */
	private function get_allowed_file_extensions( $field ) {
		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		$extensions_raw = isset( $settings['allowed_extensions'] ) && is_array( $settings['allowed_extensions'] ) ? $settings['allowed_extensions'] : array();
		$extension_whitelist = array( 'pdf', 'jpg', 'jpeg', 'png' );
		$extensions = array();

		foreach ( $extensions_raw as $extension_item ) {
			$extension = strtolower( sanitize_key( (string) $extension_item ) );
			if ( in_array( $extension, $extension_whitelist, true ) ) {
				$extensions[] = $extension;
			}
		}

		if ( empty( $extensions ) ) {
			$extensions = array( 'pdf', 'jpg', 'png' );
		}

		return array_values( array_unique( $extensions ) );
	}

	/**
	 * Sanitize and upload a submitted file field.
	 *
	 * @since    1.0.0
	 * @param    string $input_name Input field name.
	 * @param    array  $field      Field definition.
	 * @return   string
	 */
	private function sanitize_uploaded_file_value( $input_name, $field ) {
		if ( ! isset( $_FILES[ $input_name ] ) || ! is_array( $_FILES[ $input_name ] ) ) {
			return '';
		}

		$file_data = $_FILES[ $input_name ];
		if ( empty( $file_data['name'] ) || empty( $file_data['tmp_name'] ) ) {
			return '';
		}

		$upload_error = isset( $file_data['error'] ) ? (int) $file_data['error'] : UPLOAD_ERR_NO_FILE;
		if ( UPLOAD_ERR_OK !== $upload_error ) {
			return '';
		}

		$allowed_extensions = $this->get_allowed_file_extensions( $field );
		$allowed_mimes = array();
		if ( in_array( 'pdf', $allowed_extensions, true ) ) {
			$allowed_mimes['pdf'] = 'application/pdf';
		}
		if ( in_array( 'jpg', $allowed_extensions, true ) || in_array( 'jpeg', $allowed_extensions, true ) ) {
			$allowed_mimes['jpg|jpeg'] = 'image/jpeg';
		}
		if ( in_array( 'png', $allowed_extensions, true ) ) {
			$allowed_mimes['png'] = 'image/png';
		}

		if ( empty( $allowed_mimes ) ) {
			return '';
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$overrides = array(
			'test_form' => false,
			'mimes' => $allowed_mimes,
		);

		$uploaded = wp_handle_upload( $file_data, $overrides );
		if ( ! is_array( $uploaded ) || ! empty( $uploaded['error'] ) || empty( $uploaded['file'] ) || empty( $uploaded['url'] ) ) {
			return '';
		}

		$file_path = (string) $uploaded['file'];
		$file_name = basename( $file_path );
		$verified_filetype = wp_check_filetype_and_ext( $file_path, $file_name, $allowed_mimes );

		$verified_ext = isset( $verified_filetype['ext'] ) ? strtolower( sanitize_key( (string) $verified_filetype['ext'] ) ) : '';
		$verified_type = isset( $verified_filetype['type'] ) ? sanitize_text_field( (string) $verified_filetype['type'] ) : '';

		$mime_values = array_values( $allowed_mimes );
		if ( '' === $verified_ext || ! in_array( $verified_ext, $allowed_extensions, true ) || '' === $verified_type || ! in_array( $verified_type, $mime_values, true ) ) {
			@unlink( $file_path );
			return '';
		}

		return esc_url_raw( (string) $uploaded['url'] );
	}

	/**
	 * Sanitize canvas base64 image payload.
	 *
	 * @since    1.0.0
	 * @param    string $raw_value Raw submitted value.
	 * @return   string
	 */
	private function sanitize_canvas_value( $raw_value ) {
		$candidate = trim( wp_kses_no_null( (string) $raw_value ) );
		if ( '' === $candidate ) {
			return '';
		}

		if ( ! preg_match( '#^data:image/(png|jpeg|jpg|webp);base64,([A-Za-z0-9+/=\r\n]+)$#', $candidate, $matches ) ) {
			return '';
		}

		$payload = preg_replace( '/\s+/', '', $matches[2] );
		if ( ! is_string( $payload ) || '' === $payload ) {
			return '';
		}

		$max_bytes = 2 * 1024 * 1024;
		$estimated_bytes = (int) floor( strlen( $payload ) * 3 / 4 );
		if ( $estimated_bytes <= 0 || $estimated_bytes > $max_bytes ) {
			return '';
		}

		$decoded = base64_decode( $payload, true );
		if ( false === $decoded || strlen( $decoded ) > $max_bytes ) {
			return '';
		}

		return 'data:image/' . strtolower( $matches[1] ) . ';base64,' . $payload;
	}

	/**
	 * Resolve whether textarea field is configured as display-only.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   bool
	 */
	private function is_textarea_display_only( $field ) {
		if ( ! is_array( $field ) ) {
			return false;
		}

		$field_type = isset( $field['type'] ) ? sanitize_key( (string) $field['type'] ) : '';
		if ( 'textarea' !== $field_type ) {
			return false;
		}

		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		return ! empty( $settings['display_only'] );
	}

	/**
	 * Resolve whether radio field allows custom "Otros" option.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   bool
	 */
	private function is_radio_other_option_enabled( $field ) {
		if ( ! is_array( $field ) ) {
			return false;
		}

		$field_type = isset( $field['type'] ) ? sanitize_key( (string) $field['type'] ) : '';
		if ( 'radio' !== $field_type ) {
			return false;
		}

		$settings = isset( $field['settings'] ) && is_array( $field['settings'] ) ? $field['settings'] : array();
		return ! empty( $settings['allow_other_option'] );
	}

	/**
	 * Sanitize textarea label markup allowing only inline format tags.
	 *
	 * @since    1.0.0
	 * @param    string $raw_label Raw label value.
	 * @return   string
	 */
	private function sanitize_textarea_label_markup( $raw_label ) {
		$allowed_tags = array(
			'strong' => array(),
			'b' => array(),
			'em' => array(),
			'i' => array(),
			'u' => array(),
			'br' => array(),
		);

		$label = wp_kses_no_null( (string) $raw_label );
		$label = wp_kses( $label, $allowed_tags );
		$label = trim( $label );

		if ( '' === $label ) {
			return '';
		}

		if ( strlen( $label ) > 1200 ) {
			$label = substr( $label, 0, 1200 );
		}

		return $label;
	}

	/**
	 * Sanitize field description markup allowing only inline format tags.
	 *
	 * @since    1.0.0
	 * @param    string $raw_description Raw description value.
	 * @return   string
	 */
	private function sanitize_field_description_markup( $raw_description ) {
		$allowed_tags = array(
			'strong' => array(),
			'b' => array(),
			'em' => array(),
			'i' => array(),
			'u' => array(),
			'br' => array(),
		);

		$description = wp_kses_no_null( (string) $raw_description );
		$description = str_replace( array( "\r\n", "\r" ), "\n", $description );
		$description = wp_kses( $description, $allowed_tags );
		$description = trim( $description );

		if ( '' === $description ) {
			return '';
		}

		$description = nl2br( $description, false );

		return wp_kses( $description, $allowed_tags );
	}

	/**
	 * Resolve whether a field is required.
	 *
	 * @since    1.0.0
	 * @param    array $field Field definition.
	 * @return   bool
	 */
	private function is_field_required( $field ) {
		if ( ! is_array( $field ) ) {
			return true;
		}

		if ( $this->is_textarea_display_only( $field ) ) {
			return false;
		}

		if ( ! array_key_exists( 'required', $field ) ) {
			return true;
		}

		return ! empty( $field['required'] );
	}

}
