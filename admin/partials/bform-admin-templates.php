<?php

/**
 * Provide templates admin view for the plugin.
 *
 * @link       https://google.com
 * @since      1.0.0
 *
 * @package    Bform
 * @subpackage Bform/admin/partials
 */
?>
<div class="wrap bform-admin-wrap bform-admin-wrap--full bform-templates-wrap">
	<nav class="bform-view-nav" aria-label="<?php esc_attr_e( 'Navegación de vistas', 'bform' ); ?>">
		<a href="<?php echo esc_url( $principal_page_url ); ?>"><?php esc_html_e( 'Principal', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $constructor_page_url ); ?>"><?php esc_html_e( 'Constructor', 'bform' ); ?></a>
		<a class="is-active" href="<?php echo esc_url( $templates_page_url ); ?>"><?php esc_html_e( 'Plantillas', 'bform' ); ?></a>
		<a href="<?php echo esc_url( $analytics_page_url ); ?>"><?php esc_html_e( 'Analíticas', 'bform' ); ?></a>
	</nav>

	<header class="bform-template-library-header">
		<div class="bform-template-library-title">
			<h1><?php esc_html_e( 'Plantillas de Formulario', 'bform' ); ?></h1>
			<p><?php esc_html_e( 'Gestiona y combina módulos predefinidos', 'bform' ); ?></p>
		</div>
	</header>

	<form method="get" class="bform-template-library-search" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="bform-plantillas" />
		<label class="screen-reader-text" for="bform-template-search"><?php esc_html_e( 'Buscar plantilla', 'bform' ); ?></label>
		<input id="bform-template-search" type="search" name="s" value="<?php echo esc_attr( $templates_search_term ); ?>" placeholder="<?php esc_attr_e( 'Buscar por nombre o categoría...', 'bform' ); ?>" />
		<button type="submit" class="button button-primary bform-template-search-btn" aria-label="<?php esc_attr_e( 'Buscar', 'bform' ); ?>">
			<span class="dashicons dashicons-search" aria-hidden="true"></span>
		</button>
	</form>

	<?php if ( ! empty( $templates_table_err ) ) : ?>
		<div class="notice notice-error">
			<p><?php echo esc_html( $templates_table_err ); ?></p>
		</div>
	<?php endif; ?>

	<div class="bform-template-library-grid" aria-label="<?php esc_attr_e( 'Listado de plantillas', 'bform' ); ?>">
		<?php if ( empty( $templates_items ) ) : ?>
			<article class="bform-template-library-empty">
				<h2><?php esc_html_e( 'No se encontraron plantillas', 'bform' ); ?></h2>
				<p><?php esc_html_e( 'Cuando crees plantillas, aparecerán aquí para seleccionarlas, usarlas o combinarlas.', 'bform' ); ?></p>
			</article>
		<?php else : ?>
			<?php foreach ( $templates_items as $template_index => $template_item ) : ?>
				<?php
				$template_id = isset( $template_item['id'] ) ? absint( $template_item['id'] ) : 0;
				$template_name = isset( $template_item['nombre'] ) ? $template_item['nombre'] : '';
				$template_category = isset( $template_item['categoria'] ) ? $template_item['categoria'] : '';
				$template_desc = isset( $template_item['descripcion'] ) ? $template_item['descripcion'] : '';
				$template_fields_total = isset( $template_item['campos_total'] ) ? absint( $template_item['campos_total'] ) : 0;

				$template_category_lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( (string) $template_category, 'UTF-8' ) : strtolower( (string) $template_category );
				$template_icon_class = 'dashicons-media-text';
				if ( false !== strpos( $template_category_lower, 'médic' ) || false !== strpos( $template_category_lower, 'medic' ) || false !== strpos( $template_category_lower, 'salud' ) ) {
					$template_icon_class = 'dashicons-heart';
				} elseif ( false !== strpos( $template_category_lower, 'legal' ) || false !== strpos( $template_category_lower, 'consent' ) || false !== strpos( $template_category_lower, 'firma' ) ) {
					$template_icon_class = 'dashicons-edit-page';
				} elseif ( false !== strpos( $template_category_lower, 'personal' ) || false !== strpos( $template_category_lower, 'estudiant' ) ) {
					$template_icon_class = 'dashicons-id';
				}

				$template_card_class = 0 === ( $template_index % 3 ) ? 'is-icon-style-a' : ( 1 === ( $template_index % 3 ) ? 'is-icon-style-b' : 'is-icon-style-c' );
				$template_edit_url = '';
				$template_duplicate_url = '';
				$template_delete_url = '';

				if ( $template_id > 0 ) {
					$template_edit_url = add_query_arg(
						array(
							'page' => 'bform-constructor',
							'template_id' => $template_id,
						),
						admin_url( 'admin.php' )
					);

					$template_duplicate_url = wp_nonce_url(
						add_query_arg(
							array(
								'page' => 'bform-plantillas',
								'bform_action' => 'duplicate_template',
								'template_id' => $template_id,
							),
							admin_url( 'admin.php' )
						),
						'bform_duplicate_template_' . $template_id
					);

					$template_delete_url = wp_nonce_url(
						add_query_arg(
							array(
								'page' => 'bform-plantillas',
								'bform_action' => 'delete_template',
								'template_id' => $template_id,
							),
							admin_url( 'admin.php' )
						),
						'bform_delete_template_' . $template_id
					);
				}
				?>
				<article class="bform-template-library-card <?php echo esc_attr( $template_card_class ); ?>" data-template-id="<?php echo esc_attr( (string) $template_id ); ?>" tabindex="0" role="button" aria-pressed="false">
					<button type="button" class="bform-template-selection-check" aria-label="<?php esc_attr_e( 'Seleccionar plantilla', 'bform' ); ?>">
						<span class="dashicons dashicons-yes" aria-hidden="true"></span>
					</button>
					<div class="bform-template-library-icon" aria-hidden="true">
						<span class="dashicons <?php echo esc_attr( $template_icon_class ); ?>"></span>
					</div>
					<h2 class="bform-template-library-card-title"><?php echo esc_html( $template_name ); ?></h2>
					<p class="bform-template-library-card-desc"><?php echo esc_html( $template_desc ); ?></p>
					<div class="bform-template-card-actions">
						<span class="bform-template-field-count"><?php echo esc_html( sprintf( _n( '%d CAMPO', '%d CAMPOS', $template_fields_total, 'bform' ), $template_fields_total ) ); ?></span>
						<div class="bform-template-action-icons">
							<button type="button" class="bform-template-card-action" data-action="edit" data-action-url="<?php echo esc_url( $template_edit_url ); ?>" aria-label="<?php esc_attr_e( 'Editar plantilla', 'bform' ); ?>" title="<?php esc_attr_e( 'Editar', 'bform' ); ?>">
								<span class="dashicons dashicons-edit" aria-hidden="true"></span>
							</button>
							<button type="button" class="bform-template-card-action" data-action="duplicate" data-action-url="<?php echo esc_url( $template_duplicate_url ); ?>" aria-label="<?php esc_attr_e( 'Duplicar plantilla', 'bform' ); ?>" title="<?php esc_attr_e( 'Duplicar', 'bform' ); ?>">
								<span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
							</button>
							<button type="button" class="bform-template-card-action" data-action="delete" data-action-url="<?php echo esc_url( $template_delete_url ); ?>" aria-label="<?php esc_attr_e( 'Eliminar plantilla', 'bform' ); ?>" title="<?php esc_attr_e( 'Eliminar', 'bform' ); ?>">
								<span class="dashicons dashicons-trash" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<div class="bform-template-selection-bar" hidden>
		<div class="bform-template-selection-summary">
			<span class="bform-template-selection-count">0</span>
			<span><?php esc_html_e( 'Plantillas seleccionadas', 'bform' ); ?></span>
		</div>
		<div class="bform-template-selection-actions">
			<button type="button" class="button bform-template-selection-btn bform-template-use-btn" disabled><?php esc_html_e( 'Usar plantilla', 'bform' ); ?></button>
			<button type="button" class="button bform-template-selection-btn bform-template-combine-btn" disabled><?php esc_html_e( 'Combinar en nuevo Formulario', 'bform' ); ?></button>
		</div>
	</div>

	<form id="bform-template-use-form" class="bform-template-hidden-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="bform-plantillas" />
		<input type="hidden" name="bform_action" value="use_template" />
		<input type="hidden" name="template_id" value="" class="bform-template-use-input" />
		<?php wp_nonce_field( 'bform_use_template' ); ?>
	</form>

	<form id="bform-template-combine-form" class="bform-template-hidden-form" method="post" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<input type="hidden" name="page" value="bform-plantillas" />
		<input type="hidden" name="bform_action" value="combine_templates_to_form" />
		<div class="bform-template-combine-inputs"></div>
		<?php wp_nonce_field( 'bform_combine_templates_to_form' ); ?>
	</form>
</div>
