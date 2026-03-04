<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin/partials
 */
?>
<?php
$total_forms  = count( $principal_forms );
$active_forms = 0;

foreach ( $principal_forms as $principal_form_item ) {
	if ( isset( $principal_form_item['activo'] ) && (int) $principal_form_item['activo'] === 1 ) {
		$active_forms++;
	}
}
?>
<div class="wrap bform-admin-wrap bform-admin-wrap--full bform-admin-wrap--principal">
	<nav class="bform-view-nav" aria-label="<?php esc_attr_e( 'Navegación de vistas', 'bform' ); ?>">
		<a class="is-active" href="<?php echo esc_url( $principal_page_url ); ?>"><?php esc_html_e( 'Principal', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Constructor', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $templates_page_url ); ?>"><?php esc_html_e( 'Plantillas', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $analytics_page_url ); ?>"><?php esc_html_e( 'Analíticas', 'bform' ); ?></a>
	</nav>

	<div class="bform-header">
		<div class="bform-brand">
			<span class="bform-brand-icon">▣</span>
			<div>
				<h1><?php esc_html_e( 'Panel de Formularios', 'bform' ); ?></h1>
				<p><?php esc_html_e( 'Crea, activa y organiza tus formularios en un solo lugar.', 'bform' ); ?></p>
			</div>
		</div>
		<div class="bform-header-actions">
			<input type="search" placeholder="<?php esc_attr_e( 'Buscar formularios...', 'bform' ); ?>" />
			<a class="button button-primary bform-cta" href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Crear Nuevo Formulario', 'bform' ); ?></a>
		</div>
	</div>

	<?php if ( ! empty( $principal_table_err ) ) : ?>
		<div class="notice notice-error">
			<p><?php echo esc_html( $principal_table_err ); ?></p>
		</div>
	<?php endif; ?>

	<section class="bform-metrics" aria-label="<?php esc_attr_e( 'Métricas', 'bform' ); ?>">
		<article class="bform-metric-card">
			<h2><?php esc_html_e( 'Formularios Totales', 'bform' ); ?></h2>
			<p class="bform-value"><?php echo esc_html( (string) $total_forms ); ?></p>
		</article>
		<article class="bform-metric-card">
			<h2><?php esc_html_e( 'Formularios Activos', 'bform' ); ?></h2>
			<p class="bform-value"><?php echo esc_html( (string) $active_forms ); ?> <span class="is-muted">/<?php echo esc_html( (string) $total_forms ); ?></span></p>
		</article>
		<article class="bform-metric-card">
			<h2><?php esc_html_e( 'Listos para usar', 'bform' ); ?></h2>
			<p class="bform-value"><span class="is-muted"><?php esc_html_e( 'Publicables en tu sitio', 'bform' ); ?></span></p>
		</article>
		<article class="bform-metric-card">
			<h2><?php esc_html_e( 'Estado del panel', 'bform' ); ?></h2>
			<p class="bform-value"><span class="is-up"><?php esc_html_e( 'Operativo', 'bform' ); ?></span></p>
		</article>
	</section>

	<section class="bform-panel bform-panel--principal-list">
		<header class="bform-panel-header bform-table-header">
			<h2><?php esc_html_e( 'Listado de Formularios', 'bform' ); ?></h2>
			<a class="button button-primary bform-btn-primary-uleam" href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Ir al Constructor', 'bform' ); ?></a>
		</header>

		<div class="bform-table-card">
			<table class="widefat fixed striped bform-table bform-principal-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Código', 'bform' ); ?></th>
						<th><?php esc_html_e( 'Nombre del Formulario', 'bform' ); ?></th>
						<th><?php esc_html_e( 'Estado', 'bform' ); ?></th>
						<th><?php esc_html_e( 'Insertar en página', 'bform' ); ?></th>
						<th><?php esc_html_e( 'Fecha', 'bform' ); ?></th>
						<th class="bform-col-actions"><?php esc_html_e( 'Acciones', 'bform' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $principal_forms ) ) : ?>
						<tr>
							<td colspan="6" class="bform-empty-row"><?php esc_html_e( 'Todavía no has creado formularios.', 'bform' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $principal_forms as $principal_form ) : ?>
							<?php
							$form_id = isset( $principal_form['id'] ) ? absint( $principal_form['id'] ) : 0;
							$form_name = isset( $principal_form['nombre'] ) ? $principal_form['nombre'] : '';
							$form_date = ! empty( $principal_form['fecha_creacion'] ) ? $principal_form['fecha_creacion'] : '';
							$form_active = isset( $principal_form['activo'] ) ? (int) $principal_form['activo'] === 1 : true;
							$shortcode = '[uleam_form id="' . $form_id . '"]';

							$duplicate_url = wp_nonce_url(
								add_query_arg(
									array(
										'page' => 'bform-principal',
										'bform_action' => 'duplicate_form',
										'form_id' => $form_id,
									),
									admin_url( 'admin.php' )
								),
								'bform_duplicate_form_' . $form_id
							);

							$delete_url = wp_nonce_url(
								add_query_arg(
									array(
										'page' => 'bform-principal',
										'bform_action' => 'delete_form',
										'form_id' => $form_id,
									),
									admin_url( 'admin.php' )
								),
								'bform_delete_form_' . $form_id
							);

							$delete_clean_url = wp_nonce_url(
								add_query_arg(
									array(
										'page' => 'bform-principal',
										'bform_action' => 'delete_form',
										'form_id' => $form_id,
										'clean_responses' => 1,
									),
									admin_url( 'admin.php' )
								),
								'bform_delete_form_' . $form_id
							);

							$edit_url = add_query_arg(
								array(
									'page' => 'bform-constructor',
									'form_id' => $form_id,
								),
								admin_url( 'admin.php' )
							);

							$responses_url = add_query_arg(
								array(
									'page' => 'bform-analiticas',
									'filter_form_id' => $form_id,
								),
								admin_url( 'admin.php' )
							);
							?>
							<tr>
								<td><span class="bform-code-id"><?php echo esc_html( (string) $form_id ); ?></span></td>
								<td><span class="bform-form-name"><?php echo esc_html( $form_name ); ?></span></td>
								<td>
									<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=bform-principal' ) ); ?>" class="bform-toggle-form">
										<input type="hidden" name="bform_action" value="toggle_status" />
										<input type="hidden" name="form_id" value="<?php echo esc_attr( (string) $form_id ); ?>" />
										<?php wp_nonce_field( 'bform_toggle_status_' . $form_id ); ?>
										<label class="bform-switch">
											<input type="checkbox" <?php checked( $form_active ); ?> onchange="this.form.submit()" />
											<span class="bform-slider"></span>
										</label>
									</form>
								</td>
								<td>
									<button type="button" class="button bform-copy-shortcode bform-btn-copy" data-shortcode="<?php echo esc_attr( $shortcode ); ?>">
										<span class="dashicons dashicons-editor-code" aria-hidden="true"></span>
										<?php esc_html_e( 'Copiar código', 'bform' ); ?>
									</button>
								</td>
								<td><span class="bform-date-badge"><?php echo esc_html( $form_date ); ?></span></td>
								<td class="bform-row-actions">
									<a class="button bform-btn-action bform-btn-edit" href="<?php echo esc_url( $edit_url ); ?>"><?php esc_html_e( 'Editar', 'bform' ); ?></a>
									<a class="button bform-btn-action" href="<?php echo esc_url( $duplicate_url ); ?>"><?php esc_html_e( 'Duplicar', 'bform' ); ?></a>
									<a class="button bform-btn-action" href="<?php echo esc_url( $responses_url ); ?>"><?php esc_html_e( 'Respuestas', 'bform' ); ?></a>
									<a class="button bform-btn-action bform-btn-delete bform-confirm-action" href="<?php echo esc_url( $delete_url ); ?>" data-confirm-message="<?php echo esc_attr( __( '¿Estás seguro?', 'bform' ) ); ?>"><?php esc_html_e( 'Eliminar', 'bform' ); ?></a>
									<a class="button bform-btn-action bform-btn-delete bform-confirm-action" href="<?php echo esc_url( $delete_clean_url ); ?>" data-confirm-message="<?php echo esc_attr( __( '¿Estás seguro? Se eliminará el formulario y sus respuestas.', 'bform' ) ); ?>"><?php esc_html_e( 'Eliminar + Respuestas', 'bform' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>
