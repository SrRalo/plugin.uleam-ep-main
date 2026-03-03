(function( $ ) {
	'use strict';

	function copyShortcode() {
		$(document).on('click', '.bform-copy-shortcode', function() {
			var $button = $(this);
			var shortcode = $button.data('shortcode');

			if (!shortcode) {
				return;
			}

			if (navigator && navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(shortcode);
			} else {
				var tempInput = document.createElement('input');
				tempInput.value = shortcode;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
			}

			$button.addClass('is-copied');
			setTimeout(function() {
				$button.removeClass('is-copied');
			}, 1200);
		});
	}

	function getActionIconMarkup(action) {
		var icons = {
			save: '<svg viewBox="0 0 24 24"><path d="M5 4h12l3 3v13H5z"></path><path d="M8 4v6h8V4"></path><path d="M8 20v-6h8v6"></path></svg>',
			preview: '<svg viewBox="0 0 24 24"><path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
			download: '<svg viewBox="0 0 24 24"><path d="M12 3v12"></path><path d="M7 10l5 5 5-5"></path><path d="M4 21h16"></path></svg>',
			add: '<svg viewBox="0 0 24 24"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>',
			edit: '<svg viewBox="0 0 24 24"><path d="M4 20l4-1 9-9-3-3-9 9-1 4z"></path><path d="M14 7l3 3"></path></svg>',
			delete: '<svg viewBox="0 0 24 24"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M6 6l1 14h10l1-14"></path></svg>',
			back: '<svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"></path></svg>',
			close: '<svg viewBox="0 0 24 24"><path d="M6 6l12 12"></path><path d="M18 6L6 18"></path></svg>'
		};

		if (!icons[action]) {
			return '';
		}

		return '<span class="bform-action-icon" aria-hidden="true">' + icons[action] + '</span>';
	}

	function resolveActionFromText(text) {
		if (!text) {
			return '';
		}

		if (/guardar|aplicar/i.test(text)) {
			return 'save';
		}
		if (/previsual|visualizar/i.test(text)) {
			return 'preview';
		}
		if (/descargar|exportar/i.test(text)) {
			return 'download';
		}
		if (/agregar|añadir|nuevo|crear/i.test(text)) {
			return 'add';
		}
		if (/editar/i.test(text)) {
			return 'edit';
		}
		if (/eliminar|quitar/i.test(text)) {
			return 'delete';
		}
		if (/regresar|volver/i.test(text)) {
			return 'back';
		}
		if (/cerrar/i.test(text)) {
			return 'close';
		}

		return '';
	}

	function decorateActionButtons($scope) {
		var $root = ($scope && $scope.length) ? $scope : $(document);
		$root.find('.bform-admin-wrap .button, .bform-admin-wrap button').each(function() {
			var $button = $(this);
			if ($button.find('.bform-action-icon').length) {
				return;
			}

			var text = $button.text().trim();
			var action = resolveActionFromText(text);
			if (!action) {
				return;
			}

			var iconMarkup = getActionIconMarkup(action);
			if (!iconMarkup) {
				return;
			}

			$button.prepend(iconMarkup);
		});
	}

	function getNoticeMessageByKey(key) {
		if (!key) {
			return '';
		}

		if (window.bformAdmin && window.bformAdmin.noticeMessages && window.bformAdmin.noticeMessages[key]) {
			return window.bformAdmin.noticeMessages[key];
		}

		return '';
	}

	function getAdminToastContainer() {
		var $container = $('.bform-admin-toast-stack');
		if ($container.length) {
			return $container;
		}

		$container = $('<div class="bform-admin-toast-stack" aria-live="polite" aria-atomic="true"></div>');
		$('body').append($container);
		return $container;
	}

	function showAdminToast(message, type) {
		if (!message) {
			return;
		}

		var level = type || 'success';
		var variant = level === 'error' ? 'action' : 'info';
		var title = variant === 'action' ? 'Acción requerida' : 'Acción completada';
		var iconClass = variant === 'action' ? 'dashicons-warning' : 'dashicons-yes-alt';
		var $container = getAdminToastContainer();
		var $toast = $('<div class="bform-admin-toast"></div>');
		var $header = $('<div class="bform-admin-toast-header"></div>');
		var $icon = $('<span class="dashicons bform-admin-toast-icon" aria-hidden="true"></span>');
		var $title = $('<span class="bform-admin-toast-title"></span>');
		var $close = $('<button type="button" class="bform-admin-toast-close" aria-label="Cerrar notificación"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>');
		var $message = $('<p class="bform-admin-toast-message"></p>');

		$toast.addClass('is-' + variant);
		$icon.addClass(iconClass);
		$title.text(title);
		$message.text(message);
		$header.append($icon, $title, $close);
		$toast.append($header, $message);

		$container.append($toast);

		window.setTimeout(function() {
			$toast.addClass('is-visible');
		}, 20);

		function closeToast() {
			$toast.removeClass('is-visible');
			window.setTimeout(function() {
				$toast.remove();
			}, 220);
		}

		$close.on('click', function() {
			closeToast();
		});

		window.setTimeout(function() {
			closeToast();
		}, 3200);
	}

	function initAdminToasts() {
		var search = window.location && window.location.search ? window.location.search : '';
		if (!search) {
			return;
		}

		var params = new URLSearchParams(search);
		var noticeKey = params.get('bform_notice') || '';
		if (!noticeKey) {
			return;
		}

		var message = getNoticeMessageByKey(noticeKey);
		if (!message) {
			message = getNoticeMessageByKey('action_done');
		}

		showAdminToast(message, 'success');
	}

	function showConfirmNotification(message, onConfirm) {
		var $container = getAdminToastContainer();
		var $card = $('<div class="bform-admin-toast bform-admin-toast--confirm is-action"></div>');
		var $header = $('<div class="bform-admin-toast-header"></div>');
		var $icon = $('<span class="dashicons dashicons-warning bform-admin-toast-icon" aria-hidden="true"></span>');
		var $title = $('<span class="bform-admin-toast-title">¿Eliminar elemento?</span>');
		var $close = $('<button type="button" class="bform-admin-toast-close" aria-label="Cerrar notificación"><span class="dashicons dashicons-no-alt" aria-hidden="true"></span></button>');
		var $message = $('<p class="bform-admin-toast-message"></p>');
		var $actions = $('<div class="bform-admin-toast-actions"></div>');
		var $cancel = $('<button type="button" class="button">Cancelar</button>');
		var $confirm = $('<button type="button" class="button button-primary">Eliminar</button>');

		$message.text(message || '¿Estás seguro?');
		$header.append($icon, $title, $close);
		$actions.append($cancel, $confirm);
		$card.append($header, $message, $actions);
		$container.append($card);

		window.setTimeout(function() {
			$card.addClass('is-visible');
		}, 20);

		function closeCard() {
			$card.removeClass('is-visible');
			window.setTimeout(function() {
				$card.remove();
			}, 220);
		}

		$cancel.on('click', function() {
			closeCard();
		});

		$close.on('click', function() {
			closeCard();
		});

		$confirm.on('click', function() {
			closeCard();
			if (typeof onConfirm === 'function') {
				onConfirm();
			}
		});
	}

	function initConfirmNotifications() {
		$(document).on('click', '.bform-confirm-action', function(event) {
			event.preventDefault();

			var $link = $(this);
			var url = $link.attr('href') || '';
			if (!url) {
				return;
			}

			var message = $link.attr('data-confirm-message') || '¿Estás seguro?';
			showConfirmNotification(message, function() {
				window.location.href = url;
			});
		});
	}

	function initAnalyticsModal() {
		var $modal = $('#bformAnalyticsModal');
		if (!$modal.length) {
			return;
		}

		var $modalBody = $modal.find('.bform-analytics-modal-body');
		var $modalTitle = $modal.find('#bformAnalyticsModalTitle');

		function escapeHtml(text) {
			return $('<div>').text(text || '').html();
		}

		function openModal() {
			$modal.removeAttr('hidden').addClass('is-open');
			$('body').addClass('bform-modal-open');
		}

		function closeModal() {
			$modal.removeClass('is-open').attr('hidden', 'hidden');
			$('body').removeClass('bform-modal-open');
		}

		function renderRows(rows) {
			if (!Array.isArray(rows) || !rows.length) {
				$modalBody.html('<tr><td colspan="6">No hay respuestas para este formulario.</td></tr>');
				return;
			}

			var html = '';
			rows.forEach(function(row) {
				html += '<tr>';
				html += '<td>' + escapeHtml(row.id) + '</td>';
				html += '<td>' + escapeHtml(row.applicant) + '</td>';
				html += '<td>' + escapeHtml(row.email) + '</td>';
				html += '<td>' + escapeHtml(row.faculty) + '</td>';
				html += '<td>' + escapeHtml(row.submitted_at) + '</td>';
				html += '<td>' + escapeHtml(row.status_label) + '</td>';
				html += '</tr>';
			});

			$modalBody.html(html);
		}

		function loadFormRows(formId, formName) {
			if (!window.bformAdmin || !window.bformAdmin.ajaxUrl || !window.bformAdmin.analyticsModalNonce) {
				$modalBody.html('<tr><td colspan="6">No fue posible inicializar la carga de datos.</td></tr>');
				openModal();
				return;
			}

			$modalTitle.text('Datos del Formulario: ' + (formName || 'Formulario'));
			$modalBody.html('<tr><td colspan="6">Cargando respuestas...</td></tr>');
			openModal();

			$.post(window.bformAdmin.ajaxUrl, {
				action: 'bform_get_analytics_form_responses',
				nonce: window.bformAdmin.analyticsModalNonce,
				form_id: formId
			})
				.done(function(response) {
					if (!response || !response.success || !response.data) {
						$modalBody.html('<tr><td colspan="6">No se pudo cargar la información del formulario.</td></tr>');
						return;
					}

					if (response.data.form_name) {
						$modalTitle.text('Datos del Formulario: ' + response.data.form_name);
					}

					renderRows(response.data.rows || []);
				})
				.fail(function() {
					$modalBody.html('<tr><td colspan="6">Hubo un problema al cargar las respuestas. Intenta nuevamente.</td></tr>');
				});
		}

		$(document).on('click', '.bform-open-analytics-modal', function() {
			var $button = $(this);
			var formId = parseInt($button.attr('data-form-id'), 10);
			var formName = $button.attr('data-form-name') || 'Formulario';

			if (isNaN(formId) || formId <= 0) {
				return;
			}

			loadFormRows(formId, formName);
		});

		$modal.on('click', '.bform-close-analytics-modal', function() {
			closeModal();
		});

		$modal.on('click', function(event) {
			if (event.target === this) {
				closeModal();
			}
		});

		$(document).on('keydown', function(event) {
			if ('Escape' === event.key && $modal.hasClass('is-open')) {
				closeModal();
			}
		});
	}

	function getCurrentUserIdForDraft() {
		if (window.bformAdmin && window.bformAdmin.currentUserId) {
			return String(window.bformAdmin.currentUserId);
		}

		return '0';
	}

	function supportsDraftStorage() {
		try {
			return !!window.localStorage;
		} catch (error) {
			return false;
		}
	}

	function getDraftStorageKey(draftId) {
		if (!draftId) {
			return '';
		}

		return 'bform_draft_' + getCurrentUserIdForDraft() + '_' + String(draftId);
	}

	function readDraftPayload(draftId) {
		if (!supportsDraftStorage()) {
			return null;
		}

		var key = getDraftStorageKey(draftId);
		if (!key) {
			return null;
		}

		try {
			var raw = window.localStorage.getItem(key);
			if (!raw) {
				return null;
			}

			var parsed = JSON.parse(raw);
			if (!parsed || typeof parsed !== 'object') {
				return null;
			}

			return parsed;
		} catch (error) {
			return null;
		}
	}

	function writeDraftPayload(draftId, payload) {
		if (!supportsDraftStorage()) {
			return;
		}

		var key = getDraftStorageKey(draftId);
		if (!key) {
			return;
		}

		try {
			window.localStorage.setItem(key, JSON.stringify(payload || {}));
		} catch (error) {}
	}

	function removeDraftPayload(draftId) {
		if (!supportsDraftStorage()) {
			return;
		}

		var key = getDraftStorageKey(draftId);
		if (!key) {
			return;
		}

		try {
			window.localStorage.removeItem(key);
		} catch (error) {}
	}

	function initConstructor() {
		var $app = $('.bform-constructor-app');
		if (!$app.length) {
			return;
		}

		var $initialSchemaNode = $app.find('.bform-constructor-initial-schema');
		var schema = { sections: [{ id: 'section_1', title: 'Sección 1', fields: [] }], branching_rules: [] };
		try {
			var parsed = JSON.parse($initialSchemaNode.text() || '{}');
			if (parsed && parsed.sections && Array.isArray(parsed.sections)) {
				schema = parsed;
			}
		} catch (e) {}

		if (!schema.sections || !Array.isArray(schema.sections) || !schema.sections.length) {
			schema.sections = [{ id: 'section_1', title: 'Sección 1', fields: [] }];
		}

		var selected = { sectionIndex: 0, fieldIndex: null };
		var draggedType = '';
		var formId = parseInt($app.attr('data-form-id') || '0', 10) || 0;
		var draftId = ($app.attr('data-draft-id') || '').toString().trim();
		var dragAutoScroll = {
			active: false,
			speed: 0,
			rafId: 0
		};

		function syncConstructorDraftUrl() {
			if (!window.history || !window.history.replaceState || !window.location) {
				return;
			}

			try {
				var url = new URL(window.location.href);
				if (draftId && !formId) {
					url.searchParams.set('draft_id', draftId);
				} else {
					url.searchParams.delete('draft_id');
				}
				window.history.replaceState({}, '', url.toString());
			} catch (error) {}
		}

		function ensureDraftId() {
			if (formId) {
				return '';
			}

			if (!draftId) {
				draftId = randomId('draft');
				$app.attr('data-draft-id', draftId);
			}

			syncConstructorDraftUrl();
			return draftId;
		}

		function persistConstructorDraft() {
			if (formId) {
				return;
			}

			var activeDraftId = ensureDraftId();
			if (!activeDraftId) {
				return;
			}

			writeDraftPayload(activeDraftId, {
				form_name: $app.find('.bform-form-name-input').val() || '',
				schema: schema,
				updated_at: Date.now()
			});
		}

		if (!formId) {
			ensureDraftId();
			var draftPayload = readDraftPayload(draftId);
			if (draftPayload && draftPayload.schema && Array.isArray(draftPayload.schema.sections)) {
				schema = draftPayload.schema;
			}
			if (draftPayload && typeof draftPayload.form_name === 'string' && draftPayload.form_name !== '') {
				$app.find('.bform-form-name-input').val(draftPayload.form_name);
			}
		}

		function updateDragAutoScrollSpeed(clientY) {
			if (typeof clientY !== 'number') {
				dragAutoScroll.speed = 0;
				return;
			}

			var edgeDistance = 120;
			var maxSpeed = 18;
			var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
			var speed = 0;

			if (clientY < edgeDistance) {
				speed = -Math.ceil(((edgeDistance - clientY) / edgeDistance) * maxSpeed);
			} else if (clientY > (viewportHeight - edgeDistance)) {
				speed = Math.ceil(((clientY - (viewportHeight - edgeDistance)) / edgeDistance) * maxSpeed);
			}

			dragAutoScroll.speed = speed;
		}

		function runDragAutoScroll() {
			if (!dragAutoScroll.active) {
				dragAutoScroll.rafId = 0;
				return;
			}

			if (dragAutoScroll.speed !== 0) {
				window.scrollBy(0, dragAutoScroll.speed);
			}

			dragAutoScroll.rafId = window.requestAnimationFrame(runDragAutoScroll);
		}

		function startDragAutoScroll() {
			dragAutoScroll.active = true;
			if (!dragAutoScroll.rafId) {
				dragAutoScroll.rafId = window.requestAnimationFrame(runDragAutoScroll);
			}
		}

		function stopDragAutoScroll() {
			dragAutoScroll.active = false;
			dragAutoScroll.speed = 0;
			if (dragAutoScroll.rafId) {
				window.cancelAnimationFrame(dragAutoScroll.rafId);
				dragAutoScroll.rafId = 0;
			}
		}

		function randomId(prefix) {
			return prefix + '_' + Date.now().toString(36) + '_' + Math.floor(Math.random() * 1000);
		}

		function fieldDefaults(type) {
			var labelMap = {
				text: 'Texto',
				textarea: 'Área de texto',
				email: 'Correo Electrónico',
				number: 'Número',
				radio: 'Selección Única',
				checkbox: 'Selección Múltiple',
				select: 'Lista Desplegable',
				date: 'Fecha',
				file: 'Archivo',
				link: 'Enlace',
				canvas: 'Firma Digital'
			};

			var field = {
				id: randomId('field'),
				type: type,
				label: '',
				placeholder: '',
				required: true,
				settings: {
					description_enabled: false,
					description_text: ''
				}
			};

			if (type === 'date') {
				field.settings.date_format = 'DD/MM/YYYY';
			}
			if (type === 'link') {
				field.settings.url = '';
				field.settings.text = '';
				field.settings.target = '_self';
			}
			if (type === 'radio' || type === 'checkbox' || type === 'select') {
				field.settings.options = ['Opción 1', 'Opción 2'];
			}
			if (type === 'canvas') {
				field.settings.line_width = 2;
				field.settings.stroke_color = '#1f2937';
			}
			if (type === 'file') {
				field.settings.allowed_extensions = ['pdf', 'jpg', 'png'];
			}
			if (type === 'number') {
				field.settings.number_preset = 'none';
			}

			return field;
		}

		function getNumberPresetLabel(preset) {
			if (preset === 'cedula_10') {
				return 'Cédula (10 dígitos)';
			}
			if (preset === 'telefono_10') {
				return 'Teléfono (10 dígitos)';
			}
			if (preset === 'edad_1_150') {
				return 'Edad (1 a 150 años)';
			}
			if (preset === 'decimal_2') {
				return 'Decimal (máx. 2 decimales)';
			}

			return '';
		}

		function currentField() {
			if (selected.fieldIndex === null) {
				return null;
			}
			var section = schema.sections[selected.sectionIndex];
			if (!section || !section.fields || !section.fields[selected.fieldIndex]) {
				return null;
			}
			return section.fields[selected.fieldIndex];
		}

		function ensureChoiceOptions(field) {
			if (!field) {
				return [];
			}
			field.settings = field.settings || {};
			if (!Array.isArray(field.settings.options)) {
				field.settings.options = [];
			}
			return field.settings.options;
		}

		function getLabelSuggestion(fieldType) {
			var suggestions = {
				text: 'Ej: Nombres completos',
				textarea: 'Ej: Cuéntanos tu experiencia',
				email: 'Ej: Correo electrónico',
				number: 'Ej: Edad',
				radio: 'Ej: Selecciona una opción',
				checkbox: 'Ej: Selecciona las opciones',
				select: 'Ej: Elige una opción',
				date: 'Ej: Fecha de nacimiento',
				file: 'Ej: Adjunta tu documento',
				link: 'Ej: Ver términos y condiciones',
				canvas: 'Ej: Firma del solicitante'
			};

			return suggestions[fieldType] || 'Ej: Nombre del campo';
		}

		function renderChoiceOptions(field) {
			var $list = $app.find('.bform-choice-options-list');
			$list.empty();

			if (!field) {
				return;
			}

			var options = ensureChoiceOptions(field);
			if (!options.length) {
				$list.append('<li class="is-empty">Sin opciones. Agrega al menos una.</li>');
				return;
			}

			options.forEach(function(option, index) {
				var row = '';
				row += '<li class="bform-choice-option-row">';
				row += '<input type="text" class="bform-choice-option-text" data-option-index="' + index + '" value="' + $('<div/>').text(option).html() + '" />';
				row += '<button type="button" class="button button-secondary bform-choice-remove-option" data-option-index="' + index + '">Quitar</button>';
				row += '</li>';
				$list.append(row);
			});
		}

		function renderProperties() {
			var field = currentField();
			var $emptyState = $app.find('.bform-props-empty');
			var $content = $app.find('.bform-props-content');
			var $type = $app.find('.bform-prop-type');
			var $label = $app.find('.bform-prop-label');

			$app.find('.bform-prop-date-format, .bform-prop-number-preset, .bform-prop-link-url, .bform-prop-link-text, .bform-prop-link-target, .bform-prop-canvas-width, .bform-prop-canvas-color, .bform-prop-choice-options, .bform-prop-description-text').attr('hidden', true);

			if (!field) {
				$emptyState.attr('hidden', false);
				$content.attr('hidden', true);
				$type.text('Sin selección');
				$label.val('');
				$label.attr('placeholder', 'Ej: Nombre del campo');
				renderChoiceOptions(null);
				return;
			}

			$emptyState.attr('hidden', true);
			$content.attr('hidden', false);

			$type.text(field.type || 'campo');
			$label.val(field.label || '');
			$label.attr('placeholder', getLabelSuggestion(field.type));

			field.settings = field.settings || {};
			if (field.required === undefined) {
				field.required = true;
			}
			$app.find('.bform-prop-required-toggle').prop('checked', !field.required);
			$app.find('.bform-prop-description-toggle').prop('checked', !!field.settings.description_enabled);
			$app.find('.bform-prop-description-input').val(field.settings.description_text || '');
			if (field.settings.description_enabled) {
				$app.find('.bform-prop-description-text').attr('hidden', false);
			}

			if (field.type === 'date') {
				$app.find('.bform-prop-date-format').attr('hidden', false);
				$app.find('.bform-prop-date-format-select').val((field.settings && field.settings.date_format) || 'DD/MM/YYYY');
			}

			if (field.type === 'number') {
				field.settings = field.settings || {};
				$app.find('.bform-prop-number-preset').attr('hidden', false);
				$app.find('.bform-prop-number-preset-select').val(field.settings.number_preset || 'none');
			}

			if (field.type === 'link') {
				$app.find('.bform-prop-link-url, .bform-prop-link-text, .bform-prop-link-target').attr('hidden', false);
				$app.find('.bform-prop-link-url-input').val((field.settings && field.settings.url) || '');
				$app.find('.bform-prop-link-text-input').val((field.settings && field.settings.text) || '');
				$app.find('.bform-prop-link-target-select').val((field.settings && field.settings.target) || '_self');
			}

			if (field.type === 'radio' || field.type === 'checkbox' || field.type === 'select') {
				$app.find('.bform-prop-choice-options').attr('hidden', false);
				renderChoiceOptions(field);
			}

			if (field.type === 'canvas') {
				$app.find('.bform-prop-canvas-width, .bform-prop-canvas-color').attr('hidden', false);
				$app.find('.bform-prop-canvas-width-input').val((field.settings && field.settings.line_width) || 2);
				$app.find('.bform-prop-canvas-color-input').val((field.settings && field.settings.stroke_color) || '#1f2937');
			}
		}

		function renderSections() {
			var $sectionsWrap = $app.find('.bform-constructor-sections');
			$sectionsWrap.empty();

			schema.sections.forEach(function(section, sectionIndex) {
				var $section = $('<section class="bform-step-card"></section>');
				var title = section.title || ('Sección ' + (sectionIndex + 1));
				var headerHtml = '<header><h3>' + title + '</h3>';
				if (sectionIndex > 0) {
					headerHtml += '<button type="button" class="bform-step-remove-section" data-section-index="' + sectionIndex + '" data-tooltip="Eliminar" aria-label="Eliminar sección" title="Eliminar sección"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M6 6l1 14h10l1-14"></path></svg></button>';
				}
				headerHtml += '</header>';
				$section.append(headerHtml);

				var fields = Array.isArray(section.fields) ? section.fields : [];
				fields.forEach(function(field, fieldIndex) {
					var $field = $('<div class="bform-field-group"></div>');
					$field.attr('data-section-index', sectionIndex);
					$field.attr('data-field-index', fieldIndex);
					if (selected.sectionIndex === sectionIndex && selected.fieldIndex === fieldIndex) {
						$field.addClass('is-selected');
					}
					var labelText = (field.label || field.type || 'Campo');
					$field.append('<label>' + $('<div/>').text(labelText).html() + '</label>');
					var helperText = field.placeholder || ('Tipo: ' + (field.type || 'campo'));
					if (field.type === 'radio' || field.type === 'checkbox' || field.type === 'select') {
						var options = Array.isArray(field.settings && field.settings.options) ? field.settings.options : [];
						helperText = options.length ? ('Opciones: ' + options.join(', ')) : 'Sin opciones';
					}
					if (field.type === 'file') {
						helperText = 'Tipos permitidos: PDF, JPG, PNG';
					}
					$field.append('<div class="bform-input-placeholder">' + $('<div/>').text(helperText).html() + '</div>');

						if (field.type === 'radio' || field.type === 'select') {
							$field.append('<div class="bform-branching-action"><button type="button" class="button button-secondary bform-open-branching" data-section-index="' + sectionIndex + '" data-field-index="' + fieldIndex + '">Configurar ramificación</button></div>');
						}
					$section.append($field);
				});

				var $dropzone = $('<div class="bform-dropzone bform-dropzone-active"></div>');
				$dropzone.append('<div class="bform-dropzone-hint">Arrastra un componente aquí</div>');
				$dropzone.append(buildSectionQuickMenu(sectionIndex));
				$dropzone.attr('data-section-index', sectionIndex);
				$section.append($dropzone);
				$sectionsWrap.append($section);
			});
			renderProperties();
			decorateActionButtons($app);
			persistConstructorDraft();
		}

		function buildSectionQuickMenu(sectionIndex) {
			var $menu = $('<div class="bform-section-quick-menu"></div>');
			var $toggle = $('<button type="button" class="bform-section-quick-toggle" data-section-index="' + sectionIndex + '" aria-label="Abrir menú de componentes" title="Abrir menú de componentes"><span aria-hidden="true">▼</span></button>');
			var $panel = $('<div class="bform-section-quick-panel" hidden></div>');

			$app.find('.bform-builder-sidebar .bform-draggable-field').each(function() {
				var $field = $(this);
				var fieldType = ($field.attr('data-field-type') || '').toString().trim();
				if (!fieldType) {
					return;
				}

				var label = ($field.find('.bform-field-label').text() || fieldType).toString().trim();
				var iconHtml = $field.find('.bform-field-icon').first().html() || '';
				var safeLabel = $('<div/>').text(label).html();
				var $item = $('<button type="button" class="bform-quick-menu-item bform-quick-menu-item-field" data-section-index="' + sectionIndex + '" data-field-type="' + fieldType + '" aria-label="' + safeLabel + '"></button>');
				$item.append('<span class="bform-field-icon" aria-hidden="true">' + iconHtml + '</span>');
				$item.append('<span class="bform-quick-menu-label">' + safeLabel + '</span>');
				$panel.append($item);
			});

			$menu.append($toggle);
			$menu.append($panel);
			return $menu;
		}

		function syncSelectedFieldPreview() {
			var field = currentField();
			if (!field || selected.fieldIndex === null) {
				return;
			}

			var selector = '.bform-field-group[data-section-index="' + selected.sectionIndex + '"][data-field-index="' + selected.fieldIndex + '"]';
			var $fieldCard = $app.find(selector).first();
			if (!$fieldCard.length) {
				return;
			}

			var labelText = (field.label || field.type || 'Campo');
			var helperText = field.placeholder || ('Tipo: ' + (field.type || 'campo'));
			if (field.type === 'radio' || field.type === 'checkbox' || field.type === 'select') {
				var options = Array.isArray(field.settings && field.settings.options) ? field.settings.options : [];
				helperText = options.length ? ('Opciones: ' + options.join(', ')) : 'Sin opciones';
			}
			if (field.type === 'file') {
				helperText = 'Tipos permitidos: PDF, JPG, PNG';
			}
			if (field.type === 'number') {
				var numberPreset = (field.settings && field.settings.number_preset) ? field.settings.number_preset : 'none';
				var presetLabel = getNumberPresetLabel(numberPreset);
				if (presetLabel) {
					helperText = 'Validación: ' + presetLabel;
				}
			}

			$fieldCard.find('label').first().text(labelText);
			$fieldCard.find('.bform-input-placeholder').first().text(helperText);
			persistConstructorDraft();
		}

		function addFieldToSection(sectionIndex, fieldType) {
			if (!schema.sections[sectionIndex]) {
				return;
			}
			if (!Array.isArray(schema.sections[sectionIndex].fields)) {
				schema.sections[sectionIndex].fields = [];
			}
			schema.sections[sectionIndex].fields.push(fieldDefaults(fieldType));
			selected.sectionIndex = sectionIndex;
			selected.fieldIndex = schema.sections[sectionIndex].fields.length - 1;
			renderSections();
		}

		function addNewSection() {
			var sectionNumber = schema.sections.length + 1;
			schema.sections.push({
				id: 'section_' + sectionNumber,
				title: 'Sección ' + sectionNumber,
				fields: []
			});
			renderSections();
		}

		function removeSectionAt(sectionIndex) {
			if (isNaN(sectionIndex) || sectionIndex <= 0 || !schema.sections[sectionIndex]) {
				return;
			}

			var removedSection = schema.sections[sectionIndex];
			var removedSectionId = removedSection.id || '';

			schema.sections.splice(sectionIndex, 1);

			if (Array.isArray(schema.branching_rules) && removedSectionId) {
				schema.branching_rules = schema.branching_rules.filter(function(rule) {
					return rule.source_section_id !== removedSectionId && rule.target_section_id !== removedSectionId;
				});
			}

			if (!schema.sections.length) {
				schema.sections = [{ id: 'section_1', title: 'Sección 1', fields: [] }];
			}

			if (selected.sectionIndex === sectionIndex) {
				selected.sectionIndex = Math.max(0, sectionIndex - 1);
				selected.fieldIndex = null;
			} else if (selected.sectionIndex > sectionIndex) {
				selected.sectionIndex -= 1;
			}

			renderSections();
		}

		function saveConstructorSchema() {
			if (!window.bformAdmin || !window.bformAdmin.ajaxUrl) {
				return;
			}

			var payload = {
				action: 'bform_save_form_schema',
				nonce: window.bformAdmin.saveNonce,
				form_id: formId,
				form_name: $app.find('.bform-form-name-input').val() || '',
				schema_json: JSON.stringify(schema)
			};

			var $status = $app.find('.bform-constructor-status');
			$status.text('Guardando...');

			$.post(window.bformAdmin.ajaxUrl, payload)
				.done(function(response) {
					if (response && response.success) {
						formId = parseInt(response.data.form_id || formId, 10);
						$app.attr('data-form-id', formId);
						if (draftId) {
							removeDraftPayload(draftId);
							draftId = '';
							$app.attr('data-draft-id', '');
							syncConstructorDraftUrl();
						}
						$status.text(response.data.message || 'Guardado correctamente');
						showAdminToast(response.data.message || 'Formulario guardado correctamente.', 'success');
					} else {
						$status.text((response && response.data && response.data.message) ? response.data.message : 'No se pudo guardar');
					}
				})
				.fail(function() {
					$status.text('Error al guardar el formulario');
				});
		}

		$app.on('dragstart', '.bform-draggable-field', function(event) {
			draggedType = $(this).data('field-type');
			if (event.originalEvent && event.originalEvent.dataTransfer) {
				event.originalEvent.dataTransfer.setData('text/plain', draggedType);
			}
			startDragAutoScroll();
		});

		$app.on('dragend', '.bform-draggable-field', function() {
			draggedType = '';
			stopDragAutoScroll();
			$app.find('.bform-dropzone-active').removeClass('is-over');
		});

		$(document)
			.off('dragover.bformConstructorAutoScroll drop.bformConstructorAutoScroll dragend.bformConstructorAutoScroll')
			.on('dragover.bformConstructorAutoScroll', function(event) {
				if (!draggedType) {
					return;
				}

				var originalEvent = event.originalEvent || event;
				updateDragAutoScrollSpeed(originalEvent.clientY);
				startDragAutoScroll();
			})
			.on('drop.bformConstructorAutoScroll dragend.bformConstructorAutoScroll', function() {
				stopDragAutoScroll();
				draggedType = '';
			});

		$app.on('dragover', '.bform-dropzone-active', function(event) {
			event.preventDefault();
			var originalEvent = event.originalEvent || event;
			updateDragAutoScrollSpeed(originalEvent.clientY);
			$(this).addClass('is-over');
		});

		$app.on('dragleave', '.bform-dropzone-active', function() {
			$(this).removeClass('is-over');
		});

		$app.on('drop', '.bform-dropzone-active', function(event) {
			event.preventDefault();
			$(this).removeClass('is-over');
			var sectionIndex = parseInt($(this).data('section-index'), 10) || 0;
			var fieldType = draggedType;
			if (event.originalEvent && event.originalEvent.dataTransfer) {
				fieldType = event.originalEvent.dataTransfer.getData('text/plain') || draggedType;
			}
			if (fieldType) {
				addFieldToSection(sectionIndex, fieldType);
			}
			draggedType = '';
			stopDragAutoScroll();
		});

		$app.on('click', '.bform-field-group', function() {
			selected.sectionIndex = parseInt($(this).data('section-index'), 10) || 0;
			selected.fieldIndex = parseInt($(this).data('field-index'), 10);
			renderSections();
		});

		$app.on('click', '.bform-add-section', function() {
			addNewSection();
		});

		$app.on('click', '.bform-step-remove-section', function(event) {
			event.preventDefault();
			var sectionIndex = parseInt($(this).attr('data-section-index'), 10);
			if (isNaN(sectionIndex) || sectionIndex <= 0 || !schema.sections[sectionIndex]) {
				return;
			}

			var section = schema.sections[sectionIndex];
			var fields = Array.isArray(section.fields) ? section.fields : [];
			if (!fields.length) {
				removeSectionAt(sectionIndex);
				showAdminToast('Sección eliminada.', 'success');
				return;
			}

			var sectionLabel = (section.title || ('Sección ' + (sectionIndex + 1))).toString();
			showConfirmNotification('La ' + sectionLabel + ' contiene campos. ¿Deseas eliminarla?', function() {
				removeSectionAt(sectionIndex);
				showAdminToast('Sección eliminada correctamente.', 'success');
			});
		});

		$app.on('click', '.bform-section-quick-toggle', function(event) {
			event.preventDefault();
			event.stopPropagation();

			var $menu = $(this).closest('.bform-section-quick-menu');
			var $panel = $menu.find('.bform-section-quick-panel').first();
			var wasHidden = $panel.is('[hidden]');

			$app.find('.bform-section-quick-panel').attr('hidden', true);
			$app.find('.bform-section-quick-menu').removeClass('is-open');

			if (wasHidden) {
				$panel.removeAttr('hidden');
				$menu.addClass('is-open');
			}
		});

		$app.on('click', '.bform-quick-menu-item-field', function(event) {
			event.preventDefault();
			var sectionIndex = parseInt($(this).attr('data-section-index'), 10);
			var fieldType = ($(this).attr('data-field-type') || '').toString().trim();
			if (isNaN(sectionIndex) || !fieldType) {
				return;
			}
			addFieldToSection(sectionIndex, fieldType);
		});

		$app.on('click', function(event) {
			if ($(event.target).closest('.bform-section-quick-menu').length) {
				return;
			}
			$app.find('.bform-section-quick-panel').attr('hidden', true);
			$app.find('.bform-section-quick-menu').removeClass('is-open');
		});

		$app.on('input change', '.bform-form-name-input', function() {
			persistConstructorDraft();
		});

		$app.on('input change', '.bform-prop-label', function() {
			var field = currentField();
			if (!field) { return; }
			field.label = $(this).val();
			syncSelectedFieldPreview();
		});

		$app.on('change', '.bform-prop-required-toggle', function() {
			var field = currentField();
			if (!field) { return; }
			field.required = !$(this).is(':checked');
			persistConstructorDraft();
		});

		$app.on('change', '.bform-prop-date-format-select', function() {
			var field = currentField();
			if (!field || field.type !== 'date') { return; }
			field.settings = field.settings || {};
			field.settings.date_format = $(this).val();
			persistConstructorDraft();
		});

		$app.on('change', '.bform-prop-description-toggle', function() {
			var field = currentField();
			if (!field) { return; }
			field.settings = field.settings || {};
			field.settings.description_enabled = $(this).is(':checked');
			$app.find('.bform-prop-description-text').attr('hidden', !field.settings.description_enabled);
			persistConstructorDraft();
		});

		$app.on('input change', '.bform-prop-description-input', function() {
			var field = currentField();
			if (!field) { return; }
			field.settings = field.settings || {};
			field.settings.description_text = $(this).val() || '';
			persistConstructorDraft();
		});

		$app.on('change', '.bform-prop-number-preset-select', function() {
			var field = currentField();
			if (!field || field.type !== 'number') { return; }
			field.settings = field.settings || {};
			var value = $(this).val() || 'none';
			if (value !== 'cedula_10' && value !== 'telefono_10' && value !== 'edad_1_150' && value !== 'decimal_2') {
				value = 'none';
			}
			field.settings.number_preset = value;
			syncSelectedFieldPreview();
		});

		$app.on('input change', '.bform-prop-link-url-input', function() {
			var field = currentField();
			if (!field || field.type !== 'link') { return; }
			field.settings = field.settings || {};
			field.settings.url = $(this).val();
			persistConstructorDraft();
		});

		$app.on('input change', '.bform-prop-link-text-input', function() {
			var field = currentField();
			if (!field || field.type !== 'link') { return; }
			field.settings = field.settings || {};
			field.settings.text = $(this).val();
			persistConstructorDraft();
		});

		$app.on('change', '.bform-prop-link-target-select', function() {
			var field = currentField();
			if (!field || field.type !== 'link') { return; }
			field.settings = field.settings || {};
			field.settings.target = $(this).val() === '_blank' ? '_blank' : '_self';
			persistConstructorDraft();
		});

		$app.on('click', '.bform-choice-add-option', function() {
			var field = currentField();
			if (!field || (field.type !== 'radio' && field.type !== 'checkbox' && field.type !== 'select')) {
				return;
			}
			var $input = $app.find('.bform-choice-option-input');
			var optionText = ($input.val() || '').trim();
			if (!optionText) {
				return;
			}
			var options = ensureChoiceOptions(field);
			options.push(optionText);
			$input.val('');
			renderChoiceOptions(field);
			renderSections();
		});

		$app.on('input', '.bform-choice-option-text', function() {
			var field = currentField();
			if (!field) {
				return;
			}
			var options = ensureChoiceOptions(field);
			var optionIndex = parseInt($(this).attr('data-option-index'), 10);
			if (isNaN(optionIndex) || optionIndex < 0 || optionIndex >= options.length) {
				return;
			}
			options[optionIndex] = $(this).val() || '';
			syncSelectedFieldPreview();
		});

		$app.on('change', '.bform-choice-option-text', function() {
			syncSelectedFieldPreview();
		});

		$app.on('click', '.bform-choice-remove-option', function() {
			var field = currentField();
			if (!field) {
				return;
			}
			var options = ensureChoiceOptions(field);
			var optionIndex = parseInt($(this).attr('data-option-index'), 10);
			if (isNaN(optionIndex) || optionIndex < 0 || optionIndex >= options.length) {
				return;
			}
			options.splice(optionIndex, 1);
			renderChoiceOptions(field);
			renderSections();
		});

		$app.on('input change', '.bform-prop-canvas-width-input', function() {
			var field = currentField();
			if (!field || field.type !== 'canvas') { return; }
			field.settings = field.settings || {};
			field.settings.line_width = parseFloat($(this).val() || '2');
			persistConstructorDraft();
		});

		$app.on('input change', '.bform-prop-canvas-color-input', function() {
			var field = currentField();
			if (!field || field.type !== 'canvas') { return; }
			field.settings = field.settings || {};
			field.settings.stroke_color = $(this).val();
			persistConstructorDraft();
		});

		$app.on('click', '.bform-remove-field', function() {
			if (selected.fieldIndex === null) { return; }
			var section = schema.sections[selected.sectionIndex];
			if (!section || !Array.isArray(section.fields)) { return; }
			section.fields.splice(selected.fieldIndex, 1);
			selected.fieldIndex = null;
			renderSections();
		});

		$app.on('click', '.bform-duplicate-field', function() {
			var field = currentField();
			if (!field) { return; }
			var clone = JSON.parse(JSON.stringify(field));
			clone.id = randomId('field');
			schema.sections[selected.sectionIndex].fields.push(clone);
			selected.fieldIndex = schema.sections[selected.sectionIndex].fields.length - 1;
			renderSections();
		});

		$app.on('click', '.bform-open-branching', function() {
			if (!window.bformAdmin || !window.bformAdmin.logicPageUrl) {
				return;
			}

			var sectionIndex = parseInt($(this).attr('data-section-index'), 10);
			var fieldIndex = parseInt($(this).attr('data-field-index'), 10);
			var section = schema.sections[sectionIndex] || {};
			var field = (section.fields && section.fields[fieldIndex]) ? section.fields[fieldIndex] : {};

			if (!field || (field.type !== 'radio' && field.type !== 'select')) {
				showAdminToast('La ramificación solo está disponible para campos de selección única.', 'error');
				return;
			}

			var params = new URLSearchParams();
			if (formId) {
				params.set('form_id', String(formId));
			} else {
				var activeDraftId = ensureDraftId();
				persistConstructorDraft();
				if (activeDraftId) {
					params.set('draft_id', String(activeDraftId));
				}
			}
			if (section.id) {
				params.set('source_section_id', String(section.id));
			}
			if (field.id) {
				params.set('source_field_id', String(field.id));
			}

			window.location.href = window.bformAdmin.logicPageUrl + '&' + params.toString();
		});

		$app.on('click', '.bform-save-constructor', saveConstructorSchema);

		renderSections();
	}

	function initLogicEditor() {
		var $app = $('.bform-logic-app');
		if (!$app.length) {
			return;
		}

		var formId = parseInt($app.attr('data-form-id') || '0', 10) || 0;
		var draftId = ($app.attr('data-draft-id') || '').toString().trim();

		var schema = { sections: [], branching_rules: [] };
		var fieldOptions = [];
		var graphMap = [];

		try {
			schema = JSON.parse($app.find('.bform-logic-initial-schema').text() || '{}');
		} catch (e) {}
		try {
			fieldOptions = JSON.parse($app.find('.bform-logic-field-options').text() || '[]');
		} catch (e) {}
		try {
			graphMap = JSON.parse($app.find('.bform-logic-graph-map').text() || '[]');
		} catch (e) {}

		if (!Array.isArray(schema.sections)) {
			schema.sections = [];
		}
		if (!Array.isArray(schema.branching_rules)) {
			schema.branching_rules = [];
		}

		function buildFieldOptionsFromSchema(targetSchema) {
			var options = [];
			var sections = Array.isArray(targetSchema && targetSchema.sections) ? targetSchema.sections : [];

			sections.forEach(function(section) {
				var fields = Array.isArray(section && section.fields) ? section.fields : [];
				fields.forEach(function(field) {
					if (!field || !field.id) {
						return;
					}

					var fieldType = (field.type || 'text').toString();
					if (fieldType !== 'radio' && fieldType !== 'select') {
						return;
					}
					var settings = field.settings && typeof field.settings === 'object' ? field.settings : {};
					var rawChoices = Array.isArray(settings.options) ? settings.options : [];
					var choices = [];

					rawChoices.forEach(function(choice) {
						var cleanChoice = (choice || '').toString().trim();
						if (cleanChoice) {
							choices.push(cleanChoice);
						}
					});

					options.push({
						id: (field.id || '').toString(),
						label: (field.label || field.id || 'Campo').toString(),
						type: fieldType,
						choices: choices
					});
				});
			});

			return options;
		}

		function buildLocalGraphMap(targetSchema) {
			var sections = Array.isArray(targetSchema && targetSchema.sections) ? targetSchema.sections : [];
			var rules = Array.isArray(targetSchema && targetSchema.branching_rules) ? targetSchema.branching_rules : [];
			var ruleTargetsBySource = {};

			rules.forEach(function(rule) {
				if (!rule || rule.action !== 'jump_section' || !rule.source_section_id || !rule.target_section_id) {
					return;
				}

				if (!Array.isArray(ruleTargetsBySource[rule.source_section_id])) {
					ruleTargetsBySource[rule.source_section_id] = [];
				}
				if (ruleTargetsBySource[rule.source_section_id].indexOf(rule.target_section_id) === -1) {
					ruleTargetsBySource[rule.source_section_id].push(rule.target_section_id);
				}
			});

			return sections.map(function(section, index) {
				var sectionId = section && section.id ? String(section.id) : '';
				var sequential = sections[index + 1] && sections[index + 1].id ? [String(sections[index + 1].id)] : [];
				var branchTargets = Array.isArray(ruleTargetsBySource[sectionId]) ? ruleTargetsBySource[sectionId] : [];
				var targets = branchTargets.length ? branchTargets : sequential;

				return {
					id: sectionId,
					title: section && section.title ? String(section.title) : (sectionId || ('Sección ' + (index + 1))),
					targets: targets
				};
			});
		}

		function persistLogicDraft() {
			if (formId || !draftId) {
				return;
			}

			var previous = readDraftPayload(draftId) || {};
			writeDraftPayload(draftId, {
				form_name: typeof previous.form_name === 'string' ? previous.form_name : '',
				schema: schema,
				updated_at: Date.now()
			});
		}

		if (!formId && draftId) {
			var draftPayload = readDraftPayload(draftId);
			if (draftPayload && draftPayload.schema && Array.isArray(draftPayload.schema.sections)) {
				schema = draftPayload.schema;
			}
		}

		if (!Array.isArray(schema.sections)) {
			schema.sections = [];
		}
		if (!Array.isArray(schema.branching_rules)) {
			schema.branching_rules = [];
		}

		fieldOptions = buildFieldOptionsFromSchema(schema);
		graphMap = buildLocalGraphMap(schema);

		function ensureSectionSettings(section) {
			if (!section || typeof section !== 'object') {
				return { allow_sequential_after_branch: false };
			}

			if (!section.settings || typeof section.settings !== 'object') {
				section.settings = {};
			}

			section.settings.allow_sequential_after_branch = !!section.settings.allow_sequential_after_branch;
			return section.settings;
		}

		function updateStatus(text, isError) {
			var $status = $app.find('.bform-logic-status');
			$status.text(text || '');
			$status.toggleClass('is-error', !!isError);
		}

		function sectionLabel(section) {
			return (section.title || section.id || 'Sección').toString();
		}

		function fieldLabelById(fieldId) {
			var found = fieldOptions.find(function(option) {
				return option.id === fieldId;
			});
			return found ? found.label : (fieldId || '-');
		}

		function fieldOptionById(fieldId) {
			if (!fieldId) {
				return null;
			}

			return fieldOptions.find(function(option) {
				return option && option.id === fieldId;
			}) || null;
		}

		function sectionIdByFieldId(fieldId) {
			if (!fieldId) {
				return '';
			}

			var foundSection = null;
			schema.sections.some(function(section) {
				var fields = Array.isArray(section && section.fields) ? section.fields : [];
				var hasField = fields.some(function(field) {
					return field && field.id && String(field.id) === String(fieldId);
				});

				if (hasField) {
					foundSection = section;
					return true;
				}

				return false;
			});

			return foundSection && foundSection.id ? String(foundSection.id) : '';
		}

		function getFieldChoices(fieldId) {
			var found = fieldOptionById(fieldId);
			if (!found || !Array.isArray(found.choices)) {
				return [];
			}

			return found.choices.filter(function(choice) {
				return (choice || '').toString().trim() !== '';
			});
		}

		function getRuleValueFromEditor() {
			var $valueInput = $app.find('.bform-logic-value');
			var $valueSelect = $app.find('.bform-logic-value-select');

			if ($valueSelect.length && !$valueSelect.prop('hidden')) {
				return $valueSelect.val() || '';
			}

			return $valueInput.val() || '';
		}

		function syncLogicValueControl() {
			var sourceFieldId = $app.find('.bform-logic-source-field').val() || '';
			var choices = getFieldChoices(sourceFieldId);
			var $valueInput = $app.find('.bform-logic-value');
			var $valueSelect = $app.find('.bform-logic-value-select');

			if (!$valueInput.length || !$valueSelect.length) {
				return;
			}

			var currentValue = getRuleValueFromEditor();

			if (choices.length) {
				$valueSelect.empty();
				choices.forEach(function(choice) {
					$valueSelect.append('<option value="' + $('<div/>').text(choice).html() + '">' + $('<div/>').text(choice).html() + '</option>');
				});

				if (choices.indexOf(currentValue) !== -1) {
					$valueSelect.val(currentValue);
				} else {
					$valueSelect.prop('selectedIndex', 0);
				}

				$valueInput.val($valueSelect.val() || '');
				$valueInput.prop('hidden', true);
				$valueSelect.prop('hidden', false);
				return;
			}

			if (!$valueSelect.prop('hidden')) {
				$valueInput.val($valueSelect.val() || '');
			}

			$valueSelect.prop('hidden', true);
			$valueInput.prop('hidden', false);
		}

		function sectionLabelById(sectionId) {
			var found = schema.sections.find(function(section) {
				return section.id === sectionId;
			});
			return found ? sectionLabel(found) : (sectionId || '-');
		}

		function renderSelectOptions() {
			var $fieldSelect = $app.find('.bform-logic-source-field');
			var $sourceSection = $app.find('.bform-logic-source-section');
			var $targetSection = $app.find('.bform-logic-target-section');
			var selectedFieldId = $fieldSelect.val() || '';

			$fieldSelect.empty();
			if (!fieldOptions.length) {
				$fieldSelect.append('<option value="">Sin campos disponibles</option>');
			} else {
				fieldOptions.forEach(function(option) {
					$fieldSelect.append('<option value="' + option.id + '">' + option.label + '</option>');
				});

				if (selectedFieldId && fieldOptionById(selectedFieldId)) {
					$fieldSelect.val(selectedFieldId);
				}
			}

			$sourceSection.empty();
			$targetSection.empty();
			schema.sections.forEach(function(section) {
				var label = sectionLabel(section);
				var id = section.id || '';
				$sourceSection.append('<option value="' + id + '">' + label + '</option>');
				$targetSection.append('<option value="' + id + '">' + label + '</option>');
			});

			var autoSourceSectionId = sectionIdByFieldId($fieldSelect.val() || '');
			if (autoSourceSectionId) {
				$sourceSection.val(autoSourceSectionId);
				$sourceSection.prop('disabled', true);
			} else {
				$sourceSection.prop('disabled', false);
			}

			syncLogicValueControl();
		}

		function renderRulesTable() {
			var $tbody = $app.find('.bform-logic-rules-body');
			$tbody.empty();

			if (!schema.branching_rules.length) {
				$tbody.append('<tr><td colspan="4">Aún no has creado reglas.</td></tr>');
				return;
			}

			schema.branching_rules.forEach(function(rule, index) {
				var condition = 'Si ' + fieldLabelById(rule.source_field_id) + ' ' + (rule.operator === 'contains' ? 'contiene' : 'es igual a') + ' ' + (rule.value || '-');
				var action = 'Ir a ' + sectionLabelById(rule.target_section_id);
				var row = '';
				row += '<tr>';
				row += '<td>' + (index + 1) + '</td>';
				row += '<td>' + condition + '</td>';
				row += '<td>' + action + '</td>';
				row += '<td class="bform-row-actions"><button type="button" class="button bform-logic-remove-rule" data-rule-index="' + index + '">Eliminar</button></td>';
				row += '</tr>';
				$tbody.append(row);
			});
		}

		function renderSectionsManager() {
			var $manager = $app.find('.bform-logic-sections-manager');
			$manager.empty();

			if (!schema.sections.length) {
				$manager.append('<p>Aún no hay pasos creados.</p>');
				return;
			}

			schema.sections.forEach(function(section, index) {
				var settings = ensureSectionSettings(section);
				var allowSequential = !!settings.allow_sequential_after_branch;
				var html = '';
				html += '<div class="bform-section-row">';
				html += '<div class="bform-section-row-main">';
				html += '<span><strong>' + sectionLabel(section) + '</strong></span>';
				html += '<label class="bform-section-flow-toggle">';
				html += '<input type="checkbox" class="bform-section-allow-sequential" data-section-index="' + index + '" ' + (allowSequential ? 'checked="checked"' : '') + ' />';
				html += '<span>Continuar secuencialmente si llega por branching</span>';
				html += '</label>';
				html += '</div>';
				html += '<div class="bform-section-row-actions">';
				html += '<button type="button" class="button bform-section-up" data-section-index="' + index + '">↑</button>';
				html += '<button type="button" class="button bform-section-down" data-section-index="' + index + '">↓</button>';
				html += '<button type="button" class="button button-secondary bform-section-remove" data-section-index="' + index + '">Eliminar</button>';
				html += '</div>';
				html += '</div>';
				$manager.append(html);
			});
		}

		function renderGraph() {
			var $graph = $app.find('.bform-logic-graph-view');
			$graph.empty();

			if (!Array.isArray(graphMap) || !graphMap.length) {
				$graph.append('<p>Sin recorrido disponible por ahora.</p>');
				return;
			}

			graphMap.forEach(function(node) {
				var targets = Array.isArray(node.targets) ? node.targets : [];
				var line = '<div class="bform-flow-node">' + node.title + '</div>';
				line += '<p class="bform-graph-targets">→ ' + (targets.length ? targets.join(', ') : 'Flujo secuencial') + '</p>';
				$graph.append(line);
			});
		}

		function refreshLogicUi() {
			fieldOptions = buildFieldOptionsFromSchema(schema);
			graphMap = buildLocalGraphMap(schema);
			renderSelectOptions();
			renderSectionsManager();
			renderRulesTable();
			renderGraph();
			persistLogicDraft();
		}

		function saveLogic() {
			if (!window.bformAdmin || !window.bformAdmin.ajaxUrl) {
				return;
			}

			if (!formId) {
				persistLogicDraft();
				updateStatus('Borrador guardado en este navegador. Se aplicará al guardar el formulario.', false);
				refreshLogicUi();
				return;
			}

			updateStatus('Guardando cambios...', false);

			$.post(window.bformAdmin.ajaxUrl, {
				action: 'bform_save_logic_schema',
				nonce: window.bformAdmin.logicSaveNonce,
				form_id: formId,
				schema_json: JSON.stringify(schema)
			})
				.done(function(response) {
					if (response && response.success) {
						graphMap = response.data.graph || [];
						updateStatus(response.data.message || 'Cambios guardados correctamente.', false);
						refreshLogicUi();
						return;
					}
					updateStatus((response && response.data && response.data.message) ? response.data.message : 'No se pudieron guardar los cambios.', true);
				})
				.fail(function() {
					updateStatus('Hubo un problema de conexión. Intenta nuevamente.', true);
				});
		}

		$app.on('click', '.bform-logic-add', function() {
			var sourceField = $app.find('.bform-logic-source-field').val() || '';
			var operator = $app.find('.bform-logic-operator').val() || 'equals';
			var value = getRuleValueFromEditor();
			var sourceSection = sectionIdByFieldId(sourceField) || $app.find('.bform-logic-source-section').val() || '';
			var action = $app.find('.bform-logic-action').val() || 'jump_section';
			var targetSection = $app.find('.bform-logic-target-section').val() || '';

			schema.branching_rules.push({
				id: 'rule_' + Date.now(),
				source_field_id: sourceField,
				source_section_id: sourceSection,
				operator: operator,
				value: value,
				action: action,
				target_section_id: targetSection
			});

			refreshLogicUi();
		});

		$app.on('change', '.bform-logic-source-field', function() {
			var sourceFieldId = $(this).val() || '';
			var sourceSectionId = sectionIdByFieldId(sourceFieldId);
			var $sourceSection = $app.find('.bform-logic-source-section');

			if (sourceSectionId) {
				$sourceSection.val(sourceSectionId);
				$sourceSection.prop('disabled', true);
			} else {
				$sourceSection.prop('disabled', false);
			}

			syncLogicValueControl();
		});

		$app.on('change', '.bform-logic-value-select', function() {
			$app.find('.bform-logic-value').val($(this).val() || '');
		});

		$app.on('click', '.bform-logic-remove-rule', function() {
			var index = parseInt($(this).attr('data-rule-index'), 10);
			if (!isNaN(index)) {
				schema.branching_rules.splice(index, 1);
				refreshLogicUi();
			}
		});

		$app.on('click', '.bform-logic-add-section', function() {
			var next = schema.sections.length + 1;
			schema.sections.push({ id: 'section_' + next, title: 'Sección ' + next, fields: [], settings: { allow_sequential_after_branch: false } });
			refreshLogicUi();
		});

		$app.on('change', '.bform-section-allow-sequential', function() {
			var index = parseInt($(this).attr('data-section-index'), 10);
			if (isNaN(index) || !schema.sections[index]) {
				return;
			}

			var settings = ensureSectionSettings(schema.sections[index]);
			settings.allow_sequential_after_branch = $(this).is(':checked');
			persistLogicDraft();
		});

		$app.on('click', '.bform-section-remove', function() {
			var index = parseInt($(this).attr('data-section-index'), 10);
			if (isNaN(index) || !schema.sections[index]) {
				return;
			}
			var removed = schema.sections[index].id;
			schema.sections.splice(index, 1);
			schema.branching_rules = schema.branching_rules.filter(function(rule) {
				return rule.source_section_id !== removed && rule.target_section_id !== removed;
			});
			refreshLogicUi();
		});

		$app.on('click', '.bform-section-up, .bform-section-down', function() {
			var index = parseInt($(this).attr('data-section-index'), 10);
			if (isNaN(index)) {
				return;
			}
			var isUp = $(this).hasClass('bform-section-up');
			var target = isUp ? index - 1 : index + 1;
			if (!schema.sections[target]) {
				return;
			}
			var temp = schema.sections[index];
			schema.sections[index] = schema.sections[target];
			schema.sections[target] = temp;
			refreshLogicUi();
		});

		$app.on('click', '.bform-save-logic', saveLogic);

		refreshLogicUi();
		decorateActionButtons($app);
	}

	$(function() {
		initAdminToasts();
		initConfirmNotifications();
		copyShortcode();
		initConstructor();
		initLogicEditor();
		initAnalyticsModal();
		decorateActionButtons($(document));
	});

})( jQuery );
