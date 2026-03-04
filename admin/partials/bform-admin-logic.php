<?php

/**
 * Provide logic designer admin view for the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin/partials
 */
?>
<div class="wrap bform-admin-wrap bform-logic-wrap bform-logic-app" data-form-id="<?php echo esc_attr( (string) $logic_form_id ); ?>" data-draft-id="<?php echo esc_attr( (string) $logic_draft_id ); ?>">
	<script type="application/json" class="bform-logic-initial-schema"><?php echo wp_json_encode( $logic_schema ); ?></script>
	<script type="application/json" class="bform-logic-field-options"><?php echo wp_json_encode( $logic_field_options ); ?></script>
	<script type="application/json" class="bform-logic-graph-map"><?php echo wp_json_encode( $logic_graph_map ); ?></script>
	<?php $constructor_nav_url = $constructor_page_url; ?>
	<?php if ( ! empty( $logic_form_id ) ) : ?>
		<?php $constructor_nav_url = add_query_arg( array( 'form_id' => absint( $logic_form_id ) ), $constructor_page_url ); ?>
	<?php elseif ( ! empty( $logic_draft_id ) ) : ?>
		<?php $constructor_nav_url = add_query_arg( array( 'draft_id' => sanitize_key( (string) $logic_draft_id ) ), $constructor_page_url ); ?>
	<?php endif; ?>
	<?php $logic_close_url = $constructor_page_url; ?>
	<?php if ( ! empty( $logic_form_id ) ) : ?>
		<?php $logic_close_url = add_query_arg( array( 'form_id' => absint( $logic_form_id ) ), $constructor_page_url ); ?>
	<?php elseif ( ! empty( $logic_draft_id ) ) : ?>
		<?php $logic_close_url = add_query_arg( array( 'draft_id' => sanitize_key( (string) $logic_draft_id ) ), $constructor_page_url ); ?>
	<?php endif; ?>

	<nav class="bform-view-nav" aria-label="<?php esc_attr_e( 'Navegación de vistas', 'bform' ); ?>">
		<a href="<?php echo esc_url( $principal_page_url ); ?>"><?php esc_html_e( 'Principal', 'bform' ); ?></a>
		<a class="is-active" href="<?php echo esc_url( $constructor_nav_url ); ?>"><?php esc_html_e( 'Constructor', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $templates_page_url ); ?>"><?php esc_html_e( 'Plantillas', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $analytics_page_url ); ?>"><?php esc_html_e( 'Analíticas', 'bform' ); ?></a>
	</nav>

	<header class="bform-logic-header">
		<div class="bform-title-section">
			<h1><?php esc_html_e( 'Reglas del Formulario', 'bform' ); ?></h1>
			<p><?php esc_html_e( 'Define qué paso mostrar según las respuestas del usuario.', 'bform' ); ?></p>
			<p><strong><?php esc_html_e( 'Formulario:', 'bform' ); ?></strong> <?php echo esc_html( $logic_form_name ); ?></p>
		</div>
		<div class="bform-logic-header-actions">
			<a class="button bform-btn bform-btn-outline" href="<?php echo esc_url( $logic_close_url ); ?>"><?php esc_html_e( 'Volver al Constructor', 'bform' ); ?></a>
		</div>
	</header>

	<div class="bform-logic-status" aria-live="polite"></div>

	<div class="bform-logic-grid">
		<section class="bform-logic-card">
			<span class="bform-card-title"><?php esc_html_e( 'Crear regla', 'bform' ); ?></span>

			<div class="bform-logic-row bform-logic-row-3">
				<div class="bform-logic-field">
					<label><?php esc_html_e( 'Si el campo', 'bform' ); ?></label>
					<select class="bform-logic-source-field"></select>
				</div>
				<div class="bform-logic-field">
					<label><?php esc_html_e( 'Condición', 'bform' ); ?></label>
					<select class="bform-logic-operator">
						<option value="equals"><?php esc_html_e( 'Igual a', 'bform' ); ?></option>
						<option value="contains"><?php esc_html_e( 'Contiene', 'bform' ); ?></option>
					</select>
				</div>
				<div class="bform-logic-field">
					<label><?php esc_html_e( 'Valor', 'bform' ); ?></label>
					<input type="text" class="bform-logic-value" value="" />
					<select class="bform-logic-value-select" hidden="hidden"></select>
				</div>
			</div>

			<div class="bform-logic-then">
				<span><?php esc_html_e( 'ENTONCES', 'bform' ); ?></span>
				<div class="bform-logic-row bform-logic-row-3">
					<div class="bform-logic-field">
						<label><?php esc_html_e( 'Sección Origen', 'bform' ); ?></label>
						<select class="bform-logic-source-section"></select>
					</div>
					<div class="bform-logic-field">
						<label><?php esc_html_e( 'Acción', 'bform' ); ?></label>
						<select class="bform-logic-action">
							<option value="jump_section"><?php esc_html_e( 'Saltar a sección', 'bform' ); ?></option>
						</select>
					</div>
					<div class="bform-logic-field">
						<label><?php esc_html_e( 'Destino', 'bform' ); ?></label>
						<select class="bform-logic-target-section"></select>
					</div>
				</div>
			</div>

			<div class="bform-logic-actions-top">
				<button type="button" class="button bform-btn bform-btn-success bform-logic-add"><?php esc_html_e( 'Añadir regla', 'bform' ); ?></button>
			</div>

			<span class="bform-card-title bform-card-title-sections"><?php esc_html_e( 'Organizar pasos', 'bform' ); ?></span>
			<div class="bform-logic-sections-manager"></div>
			<button type="button" class="button bform-btn bform-btn-outline bform-logic-add-section"><?php esc_html_e( 'Crear Sección', 'bform' ); ?></button>
		</section>

		<section class="bform-flow-card">
			<span class="bform-card-title"><?php esc_html_e( 'Recorrido del formulario', 'bform' ); ?></span>
			<div class="bform-flow-map bform-logic-graph-view"></div>
		</section>
	</div>

	<section class="bform-logic-table-card">
		<span class="bform-card-title"><?php esc_html_e( 'Reglas guardadas', 'bform' ); ?></span>
		<table class="bform-table bform-logic-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Regla', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Condición', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Acción', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Acciones', 'bform' ); ?></th>
				</tr>
			</thead>
			<tbody class="bform-logic-rules-body"></tbody>
		</table>
	</section>

	<footer class="bform-logic-footer">
		<a class="button bform-btn bform-btn-outline" href="<?php echo esc_url( $logic_close_url ); ?>"><?php esc_html_e( 'Descartar', 'bform' ); ?></a>
		<button type="button" class="button bform-btn bform-btn-primary bform-save-logic"><?php esc_html_e( 'Guardar cambios', 'bform' ); ?></button>
	</footer>
</div>
