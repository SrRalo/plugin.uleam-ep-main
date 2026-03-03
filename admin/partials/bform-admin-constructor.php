<?php

/**
 * Provide constructor admin view for the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin/partials
 */
?>
<div class="wrap bform-admin-wrap bform-constructor-wrap bform-constructor-app" data-form-id="<?php echo esc_attr( (string) $constructor_form_id ); ?>" data-draft-id="<?php echo esc_attr( (string) $constructor_draft_id ); ?>">
	<script type="application/json" class="bform-constructor-initial-schema"><?php echo wp_json_encode( $constructor_form_schema ); ?></script>

	<nav class="bform-view-nav" aria-label="<?php esc_attr_e( 'Navegación de vistas', 'bform' ); ?>">
		<a href="<?php echo esc_url( $principal_page_url ); ?>"><?php esc_html_e( 'Principal', 'bform' ); ?></a>
		<a class="is-active" href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Constructor', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $analytics_page_url ); ?>"><?php esc_html_e( 'Analíticas', 'bform' ); ?></a>
	</nav>

	<header class="bform-builder-topbar">
		<div class="bform-builder-brand">
			<span class="bform-brand-icon">▣</span>
			<div>
				<h1><?php esc_html_e( 'Constructor de Formularios', 'bform' ); ?></h1>
				<p><?php esc_html_e( 'Crea tu formulario de forma visual y rápida.', 'bform' ); ?></p>
			</div>
		</div>
		<div class="bform-builder-actions">
			<input type="text" class="bform-form-name-input" value="<?php echo esc_attr( $constructor_form_name ); ?>" placeholder="<?php esc_attr_e( 'agregue un nombre', 'bform' ); ?>" />
			<button type="button" class="button button-primary bform-cta bform-save-constructor bform-save-constructor--large"><?php esc_html_e( 'Guardar formulario', 'bform' ); ?></button>
		</div>
	</header>

	<div class="bform-constructor-status" aria-live="polite"></div>

	<div class="bform-builder-layout">
		<aside class="bform-builder-sidebar">
			<h2><?php esc_html_e( 'Campos disponibles', 'bform' ); ?></h2>
			<p><?php esc_html_e( 'Arrastra y suelta al lienzo', 'bform' ); ?></p>
			<ul>
				<li class="bform-draggable-field" data-field-type="text" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M5 7h14M12 7v10M8 17h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Texto Corto', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="textarea" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Área de Texto', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="email" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="2"/><path d="M4 8l8 6 8-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Correo Electrónico', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="number" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M7 7v10M17 7v10M4 10h16M4 14h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Número', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="radio" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="7" cy="8" r="3" stroke="currentColor" stroke-width="2"/><circle cx="7" cy="16" r="3" stroke="currentColor" stroke-width="2"/><path d="M13 8h7M13 16h7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Selección Única', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="checkbox" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="6" height="6" rx="1" stroke="currentColor" stroke-width="2"/><rect x="4" y="13" width="6" height="6" rx="1" stroke="currentColor" stroke-width="2"/><path d="M14 8h6M14 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Selección Múltiple', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="select" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/><path d="M9 11l3 3 3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Lista Desplegable', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="date" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/><path d="M8 3v4M16 3v4M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Fecha', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="file" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M14 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 3v5h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 15h6M9 18h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Archivo', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="link" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M10 14l4-4M7 17a3 3 0 010-4l3-3a3 3 0 014 4l-1 1M17 7a3 3 0 010 4l-3 3a3 3 0 11-4-4l1-1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Enlace', 'bform' ); ?></span></li>
				<li class="bform-draggable-field" data-field-type="canvas" draggable="true"><span class="bform-field-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 16l9-9 3 3-9 9-4 1 1-4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 6l2-2 4 4-2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span><span class="bform-field-label"><?php esc_html_e( 'Firma Digital', 'bform' ); ?></span></li>
			</ul>
			<button type="button" class="button bform-sidebar-step bform-add-section"><span class="bform-field-icon" aria-hidden="true"></span><span class="bform-field-label"><?php esc_html_e( 'Nuevo Paso / Sección', 'bform' ); ?></span></button>
		</aside>

		<main class="bform-builder-canvas">
			<div class="bform-constructor-sections"></div>
			<button type="button" class="button bform-next-step bform-add-section"><?php esc_html_e( 'Añadir Siguiente Paso', 'bform' ); ?></button>
		</main>

		<aside class="bform-builder-props bform-builder-props--uleam">
			<h2><?php esc_html_e( 'Propiedades del Campo', 'bform' ); ?></h2>
			<p class="bform-props-empty"><?php esc_html_e( 'Arrastra un campo al lienzo para editar sus propiedades.', 'bform' ); ?></p>
			<div class="bform-props-content" hidden>
				<div class="bform-prop-item">
					<span><?php esc_html_e( 'Tipo de Campo', 'bform' ); ?></span>
					<strong class="bform-prop-type"><?php esc_html_e( 'Sin selección', 'bform' ); ?></strong>
				</div>
				<div class="bform-prop-item">
					<label><?php esc_html_e( 'Título del campo', 'bform' ); ?></label>
					<input type="text" class="bform-prop-label" value="" />
				</div>

				<div class="bform-prop-item">
					<label class="bform-prop-checkbox-label">
						<input type="checkbox" class="bform-prop-required-toggle" value="1" />
						<span><?php esc_html_e( 'Deshabilitar obligatoriedad de la pregunta', 'bform' ); ?></span>
					</label>
				</div>

				<div class="bform-prop-item">
					<label class="bform-prop-checkbox-label">
						<input type="checkbox" class="bform-prop-description-toggle" value="1" />
						<span><?php esc_html_e( 'Mostrar descripción / ayuda del campo', 'bform' ); ?></span>
					</label>
				</div>

				<div class="bform-prop-item bform-prop-description-text" hidden>
					<label><?php esc_html_e( 'Descripción del campo', 'bform' ); ?></label>
					<textarea class="bform-prop-description-input" rows="3" placeholder="<?php esc_attr_e( 'Ej: Ingresa tu número de cédula sin guiones.', 'bform' ); ?>"></textarea>
				</div>

				<div class="bform-prop-item bform-prop-date-format" hidden>
					<label><?php esc_html_e( 'Formato de Fecha', 'bform' ); ?></label>
					<select class="bform-prop-date-format-select">
						<option value="DD/MM/YYYY">DD/MM/YYYY</option>
						<option value="MM/DD/YYYY">MM/DD/YYYY</option>
						<option value="YYYY-MM-DD">YYYY-MM-DD</option>
					</select>
				</div>

				<div class="bform-prop-item bform-prop-number-preset" hidden>
					<label><?php esc_html_e( 'Preset de validación', 'bform' ); ?></label>
					<select class="bform-prop-number-preset-select">
						<option value="none"><?php esc_html_e( 'Número libre', 'bform' ); ?></option>
						<option value="cedula_10"><?php esc_html_e( 'Cédula (10 dígitos)', 'bform' ); ?></option>
						<option value="telefono_10"><?php esc_html_e( 'Teléfono (10 dígitos)', 'bform' ); ?></option>
						<option value="edad_1_150"><?php esc_html_e( 'Edad (1 a 150 años)', 'bform' ); ?></option>
						<option value="decimal_2"><?php esc_html_e( 'Decimal (máx. 2 decimales)', 'bform' ); ?></option>
					</select>
				</div>

				<div class="bform-prop-item bform-prop-link-url" hidden>
					<label><?php esc_html_e( 'URL del Enlace', 'bform' ); ?></label>
					<input type="url" class="bform-prop-link-url-input" value="" placeholder="https://" />
				</div>
				<div class="bform-prop-item bform-prop-link-text" hidden>
					<label><?php esc_html_e( 'Texto del Enlace', 'bform' ); ?></label>
					<input type="text" class="bform-prop-link-text-input" value="" />
				</div>
				<div class="bform-prop-item bform-prop-link-target" hidden>
					<label><?php esc_html_e( 'Destino del enlace', 'bform' ); ?></label>
					<select class="bform-prop-link-target-select">
						<option value="_self"><?php esc_html_e( 'Misma pestaña', 'bform' ); ?></option>
						<option value="_blank"><?php esc_html_e( 'Nueva pestaña', 'bform' ); ?></option>
					</select>
				</div>

				<div class="bform-prop-item bform-prop-choice-options" hidden>
					<label><?php esc_html_e( 'Opciones', 'bform' ); ?></label>
					<div class="bform-choice-add-row">
						<input type="text" class="bform-choice-option-input" value="" placeholder="<?php esc_attr_e( 'Escribe una opción', 'bform' ); ?>" />
						<button type="button" class="button bform-choice-add-option"><?php esc_html_e( 'Agregar', 'bform' ); ?></button>
					</div>
					<ul class="bform-choice-options-list"></ul>
				</div>

				<div class="bform-prop-item bform-prop-canvas-width" hidden>
					<label><?php esc_html_e( 'Grosor de línea', 'bform' ); ?></label>
					<input type="number" min="1" max="20" class="bform-prop-canvas-width-input" value="2" />
				</div>
				<div class="bform-prop-item bform-prop-canvas-color" hidden>
					<label><?php esc_html_e( 'Color del trazo', 'bform' ); ?></label>
					<input type="color" class="bform-prop-canvas-color-input" value="#1f2937" />
				</div>
				<div class="bform-prop-actions">
					<button type="button" class="button bform-duplicate-field"><?php esc_html_e( 'Duplicar', 'bform' ); ?></button>
					<button type="button" class="button button-secondary bform-remove-field"><?php esc_html_e( 'Eliminar', 'bform' ); ?></button>
				</div>
			</div>
		</aside>
	</div>
</div>
