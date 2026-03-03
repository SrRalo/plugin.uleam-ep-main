<?php

/**
 * Provide analytics admin view for the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin/partials
 */
?>
<div class="wrap bform-admin-wrap bform-analytics-wrap">
	<nav class="bform-view-nav" aria-label="<?php esc_attr_e( 'Navegación de vistas', 'bform' ); ?>">
		<a href="<?php echo esc_url( $principal_page_url ); ?>"><?php esc_html_e( 'Principal', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Constructor', 'bform' ); ?></a>
		<a class="is-active" href="<?php echo esc_url( $analytics_page_url ); ?>"><?php esc_html_e( 'Analíticas', 'bform' ); ?></a>
	</nav>

	<header class="bform-analytics-topbar">
		<div class="bform-brand">
			<span class="bform-brand-icon">▣</span>
			<div>
				<h1><?php esc_html_e( 'Analíticas de Formularios', 'bform' ); ?></h1>
				<p><?php esc_html_e( 'Revisa resultados y respuestas en tiempo real.', 'bform' ); ?></p>
			</div>
		</div>
		<form method="get" class="bform-analytics-actions">
			<input type="hidden" name="page" value="bform-analiticas" />
			<?php if ( ! empty( $analytics_filter_form_id ) ) : ?>
				<input type="hidden" name="filter_form_id" value="<?php echo esc_attr( (string) $analytics_filter_form_id ); ?>" />
			<?php endif; ?>
			<input type="search" name="s" value="<?php echo esc_attr( $analytics_search_term ); ?>" placeholder="<?php esc_attr_e( 'Buscar formularios o respuestas...', 'bform' ); ?>" />
			<button class="button bform-analytics-search-btn" type="submit" aria-label="<?php esc_attr_e( 'Buscar', 'bform' ); ?>">
				<span class="dashicons dashicons-search" aria-hidden="true"></span>
			</button>
			<a class="button button-primary bform-cta" href="<?php echo esc_url( $analytics_export_url ); ?>"><?php esc_html_e( 'Descargar respuestas', 'bform' ); ?></a>
		</form>
	</header>

	<section class="bform-analytics-headline">
		<div>
			<h2><?php esc_html_e( 'Resumen general', 'bform' ); ?></h2>
			<p><?php esc_html_e( 'Mira cómo avanzan tus formularios y sus respuestas.', 'bform' ); ?></p>
			<?php if ( ! empty( $analytics_filter_form_id ) ) : ?>
				<p><strong><?php echo esc_html( sprintf( __( 'Mostrando: Formulario #%d', 'bform' ), $analytics_filter_form_id ) ); ?></strong></p>
			<?php endif; ?>
		</div>
		<form method="get" class="bform-analytics-selector">
			<input type="hidden" name="page" value="bform-analiticas" />
			<?php if ( '' !== $analytics_search_term ) : ?>
				<input type="hidden" name="s" value="<?php echo esc_attr( $analytics_search_term ); ?>" />
			<?php endif; ?>
			<label><?php esc_html_e( 'Seleccionar formulario', 'bform' ); ?></label>
			<select name="filter_form_id" onchange="this.form.submit()">
				<option value="0"><?php esc_html_e( 'Todos los formularios', 'bform' ); ?></option>
				<?php foreach ( $analytics_forms as $analytics_form_option ) : ?>
					<option value="<?php echo esc_attr( (string) $analytics_form_option['id'] ); ?>" <?php selected( (int) $analytics_filter_form_id, (int) $analytics_form_option['id'] ); ?>><?php echo esc_html( $analytics_form_option['nombre'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</form>
	</section>

	<section class="bform-metrics bform-analytics-metrics" aria-label="<?php esc_attr_e( 'Métricas de analíticas', 'bform' ); ?>">
		<article class="bform-metric-card">
			<h3><?php esc_html_e( 'Total de Envíos', 'bform' ); ?></h3>
			<p class="bform-value"><?php echo esc_html( number_format_i18n( (int) $analytics_metrics['total_submissions'] ) ); ?></p>
			<p class="bform-kpi-sub"><?php esc_html_e( 'Respuestas registradas', 'bform' ); ?></p>
		</article>
		<article class="bform-metric-card">
			<h3><?php esc_html_e( 'Formularios con respuestas', 'bform' ); ?></h3>
			<p class="bform-value"><?php echo esc_html( number_format_i18n( (int) $analytics_metrics['forms_with_responses'] ) ); ?></p>
			<p class="bform-kpi-sub"><?php esc_html_e( 'Con actividad reciente', 'bform' ); ?></p>
		</article>
		<article class="bform-metric-card">
			<h3><?php esc_html_e( 'Última Respuesta', 'bform' ); ?></h3>
			<p class="bform-value"><?php echo ! empty( $analytics_metrics['last_response'] ) ? esc_html( wp_date( 'd M Y', strtotime( $analytics_metrics['last_response'] ) ) ) : esc_html__( 'Sin datos', 'bform' ); ?></p>
			<p class="bform-kpi-sub"><?php echo ! empty( $analytics_metrics['last_response'] ) ? esc_html( wp_date( 'H:i', strtotime( $analytics_metrics['last_response'] ) ) ) : esc_html__( '—', 'bform' ); ?></p>
		</article>
		<article class="bform-metric-card">
			<h3><?php esc_html_e( 'Formularios activos', 'bform' ); ?></h3>
			<p class="bform-value"><?php echo esc_html( number_format_i18n( (int) $analytics_metrics['active_forms'] ) ); ?></p>
			<p class="bform-kpi-sub"><?php esc_html_e( 'Disponibles para publicar', 'bform' ); ?></p>
		</article>
	</section>

	<section class="bform-panel bform-analytics-panel">
		<header class="bform-panel-header">
			<div>
				<h2><?php esc_html_e( 'Visor de Respuestas', 'bform' ); ?></h2>
				<?php if ( '' !== $analytics_search_term ) : ?>
					<p><?php echo esc_html( sprintf( __( 'Filtro activo: %s', 'bform' ), $analytics_search_term ) ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<table class="widefat fixed striped bform-table bform-analytics-summary-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Número', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Nombre de Tabla / Formulario', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Fecha creación', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Total Respuestas', 'bform' ); ?></th>
					<th><?php esc_html_e( 'Visualización Rápida', 'bform' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $analytics_rows ) ) : ?>
					<tr>
						<td colspan="5"><?php esc_html_e( 'No hay formularios con respuestas para los filtros seleccionados.', 'bform' ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $analytics_rows as $analytics_index => $analytics_row ) : ?>
						<?php $display_row_number = ( ( $analytics_page_number - 1 ) * $analytics_per_page ) + ( $analytics_index + 1 ); ?>
						<tr>
							<td>
								<span class="bform-row-number">#<?php echo esc_html( str_pad( (string) $display_row_number, 3, '0', STR_PAD_LEFT ) ); ?></span>
							</td>
							<td><strong><?php echo esc_html( $analytics_row['form_name'] ); ?></strong></td>
							<td>
								<?php
								if ( ! empty( $analytics_row['form_created_at'] ) ) {
									echo esc_html( wp_date( 'd M Y', strtotime( $analytics_row['form_created_at'] ) ) );
								} else {
									echo esc_html__( 'Sin fecha', 'bform' );
								}
								?>
							</td>
							<td><span class="bform-badge-responses"><?php echo esc_html( number_format_i18n( (int) $analytics_row['total_responses'] ) ); ?></span></td>
							<td>
								<button
									type="button"
									class="button bform-btn-view bform-open-analytics-modal"
									data-form-id="<?php echo esc_attr( (string) $analytics_row['form_id'] ); ?>"
									data-form-name="<?php echo esc_attr( $analytics_row['form_name'] ); ?>"
								>
									<?php esc_html_e( 'Ver Datos', 'bform' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<footer class="bform-analytics-footer">
			<p>
				<?php
				if ( $analytics_total_count > 0 ) {
					$start = ( ( $analytics_page_number - 1 ) * $analytics_per_page ) + 1;
					$end = min( $analytics_total_count, $analytics_page_number * $analytics_per_page );
					echo esc_html( sprintf( __( 'Mostrando %1$d a %2$d de %3$d resultados', 'bform' ), $start, $end, $analytics_total_count ) );
				} else {
					esc_html_e( 'No hay resultados para mostrar.', 'bform' );
				}
				?>
			</p>
			<div class="bform-pagination">
				<?php if ( $analytics_total_pages > 1 ) : ?>
					<?php for ( $page_item = 1; $page_item <= $analytics_total_pages; $page_item++ ) : ?>
						<?php $page_url = add_query_arg( 'paged', $page_item, $analytics_page_base_url ); ?>
						<a class="button <?php echo $page_item === (int) $analytics_page_number ? 'button-primary' : ''; ?>" href="<?php echo esc_url( $page_url ); ?>"><?php echo esc_html( (string) $page_item ); ?></a>
					<?php endfor; ?>
				<?php endif; ?>
			</div>
		</footer>

		<div class="bform-modal-overlay" id="bformAnalyticsModal" hidden>
			<div class="bform-modal-content" role="dialog" aria-modal="true" aria-labelledby="bformAnalyticsModalTitle">
				<div class="bform-excel-header">
					<h3 id="bformAnalyticsModalTitle"><?php esc_html_e( 'Datos del Formulario', 'bform' ); ?></h3>
					<button type="button" class="button button-primary bform-close-analytics-modal"><?php esc_html_e( 'Cerrar Vista', 'bform' ); ?></button>
				</div>

				<div class="bform-excel-table-wrapper">
					<table class="widefat fixed striped bform-table bform-excel-table">
						<thead>
							<tr>
								<th><?php esc_html_e( 'ID', 'bform' ); ?></th>
								<th><?php esc_html_e( 'Nombres Completos', 'bform' ); ?></th>
								<th><?php esc_html_e( 'Correo Institucional', 'bform' ); ?></th>
								<th><?php esc_html_e( 'Facultad', 'bform' ); ?></th>
								<th><?php esc_html_e( 'Fecha Registro', 'bform' ); ?></th>
								<th><?php esc_html_e( 'Estado', 'bform' ); ?></th>
							</tr>
						</thead>
						<tbody class="bform-analytics-modal-body">
							<tr>
								<td colspan="6"><?php esc_html_e( 'Selecciona un formulario para visualizar respuestas.', 'bform' ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>
