(function( $ ) {
	'use strict';

	function normalizeComparableValue(value) {
		if (value === undefined || value === null) {
			return '';
		}

		return value
			.toString()
			.replace(/\s+/g, ' ')
			.trim()
			.toLowerCase();
	}

	function evaluateRule(rule, localState) {
		if (!rule || !localState) {
			return false;
		}

		var sourceValue = localState[rule.source_field_id];
		var expected = normalizeComparableValue(rule.value || '');
		var current = normalizeComparableValue(sourceValue);

		if (!expected) {
			return false;
		}

		if (rule.operator === 'contains') {
			return current.indexOf(expected) !== -1;
		}

		return current === expected;
	}

	function getSections(schema) {
		return Array.isArray(schema && schema.sections) ? schema.sections : [];
	}

	function getSectionIndexById(schema, sectionId) {
		var sections = getSections(schema);
		for (var index = 0; index < sections.length; index++) {
			if (sections[index] && sections[index].id === sectionId) {
				return index;
			}
		}
		return -1;
	}

	function getSectionById(schema, sectionId) {
		var index = getSectionIndexById(schema, sectionId);
		if (index < 0) {
			return null;
		}
		return getSections(schema)[index];
	}

	function sectionHasFieldId(schema, sectionId, fieldId) {
		var section = getSectionById(schema, sectionId);
		if (!section || !Array.isArray(section.fields) || !fieldId) {
			return false;
		}

		for (var index = 0; index < section.fields.length; index++) {
			if (section.fields[index] && section.fields[index].id === fieldId) {
				return true;
			}
		}

		return false;
	}

	function hasValue(value) {
		if (value === undefined || value === null) {
			return false;
		}
		return value.toString().trim() !== '';
	}

	function isSectionAnswered(schema, sectionId, localState) {
		var section = getSectionById(schema, sectionId);
		if (!section || !Array.isArray(section.fields) || !section.fields.length) {
			return false;
		}

		for (var index = 0; index < section.fields.length; index++) {
			var field = section.fields[index] || {};
			if (!field.id) {
				continue;
			}
			if (hasValue(localState[field.id])) {
				return true;
			}
		}

		return false;
	}

	function getRulesFromSection(schema, sectionId) {
		var rules = Array.isArray(schema && schema.branching_rules) ? schema.branching_rules : [];
		return rules.filter(function(rule) {
			if (!rule || rule.action !== 'jump_section' || !rule.target_section_id) {
				return false;
			}

			if (rule.source_section_id) {
				return rule.source_section_id === sectionId;
			}

			return sectionHasFieldId(schema, sectionId, rule.source_field_id);
		});
	}

	function sectionIsBranchTarget(schema, sectionId) {
		if (!sectionId) {
			return false;
		}

		var rules = Array.isArray(schema && schema.branching_rules) ? schema.branching_rules : [];
		for (var index = 0; index < rules.length; index++) {
			var rule = rules[index];
			if (!rule || rule.action !== 'jump_section') {
				continue;
			}

			if (rule.target_section_id === sectionId) {
				return true;
			}
		}

		return false;
	}

	function sectionAllowsSequentialAfterBranch(schema, sectionId) {
		var section = getSectionById(schema, sectionId);
		if (!section || typeof section !== 'object') {
			return false;
		}

		var settings = section.settings && typeof section.settings === 'object' ? section.settings : {};
		return !!settings.allow_sequential_after_branch;
	}

	function getSequentialNextSectionId(schema, sectionId) {
		var sections = getSections(schema);
		var currentIndex = getSectionIndexById(schema, sectionId);
		if (currentIndex < 0) {
			return null;
		}

		var next = sections[currentIndex + 1];
		return next && next.id ? next.id : null;
	}

	function getNextSectionId(schema, sectionId, localState) {
		if (!isSectionAnswered(schema, sectionId, localState)) {
			return null;
		}

		var rules = getRulesFromSection(schema, sectionId);
		if (rules.length) {
			for (var index = 0; index < rules.length; index++) {
				if (evaluateRule(rules[index], localState)) {
					return rules[index].target_section_id;
				}
			}
			return null;
		}

		if (sectionIsBranchTarget(schema, sectionId) && !sectionAllowsSequentialAfterBranch(schema, sectionId)) {
			return null;
		}

		return getSequentialNextSectionId(schema, sectionId);
	}

	function calculateVisibleSections(schema, localState, currentSectionId) {
		if (!schema || !Array.isArray(schema.sections)) {
			return [];
		}

		if (!currentSectionId) {
			return [];
		}

		var sectionExists = getSectionIndexById(schema, currentSectionId) >= 0;
		if (!sectionExists) {
			return [];
		}

		return [currentSectionId];
	}

	function collectLocalState($form) {
		var state = {};
		$form.find('[data-field-id]').each(function() {
			var $field = $(this);
			var fieldId = $field.data('field-id');
			if (!fieldId) {
				return;
			}

			if ($field.is(':checkbox')) {
				if (!Array.isArray(state[fieldId])) {
					state[fieldId] = [];
				}
				if ($field.is(':checked')) {
					state[fieldId].push($field.val() || '1');
				}
				return;
			}

			if ($field.is(':radio')) {
				if ($field.is(':checked')) {
					state[fieldId] = $field.val() || '';
				}
				return;
			}

			state[fieldId] = $field.val() || '';
		});

		Object.keys(state).forEach(function(key) {
			if (Array.isArray(state[key])) {
				state[key] = state[key].join(', ');
			}
		});

		return state;
	}

	function renderVisibility($form, schema) {
		if (!schema || !Array.isArray(schema.sections)) {
			return;
		}

		var state = collectLocalState($form);
		var currentSectionId = $form.data('bformCurrentSectionId') || '';
		var visibleSections = calculateVisibleSections(schema, state, currentSectionId);
		var visibleMap = {};
		visibleSections.forEach(function(sectionId) {
			visibleMap[sectionId] = true;
		});

		$form.find('[data-section-id]').each(function() {
			var $section = $(this);
			var sectionId = $section.data('section-id');
			if (!sectionId) {
				return;
			}

			if (visibleMap[sectionId]) {
				$section.show();
			} else {
				$section.hide();
			}
		});
	}

	function updateActionButtons($form, schema) {
		var $backButton = $form.find('.bform-runtime-back');
		var $nextButton = $form.find('.bform-runtime-next');
		var $submitButton = $form.find('.bform-runtime-submit');
		if (!$backButton.length || !$nextButton.length || !$submitButton.length) {
			return;
		}

		var history = $form.data('bformSectionHistory') || [];
		$backButton.prop('disabled', !history.length);

		var localState = collectLocalState($form);
		var currentSectionId = $form.data('bformCurrentSectionId') || '';
		var hasAnswer = currentSectionId && isSectionAnswered(schema, currentSectionId, localState);
		var nextSectionId = hasAnswer ? getNextSectionId(schema, currentSectionId, localState) : null;
		var canAdvance = !!(hasAnswer && nextSectionId);
		var canSubmit = !!(hasAnswer && !nextSectionId);

		$nextButton.prop('disabled', !canAdvance);
		$nextButton.prop('hidden', canSubmit);

		if (canSubmit) {
			$submitButton.prop('hidden', false);
			$submitButton.prop('disabled', false);
		} else {
			$submitButton.prop('disabled', true);
			$submitButton.prop('hidden', true);
		}
	}

	function resolveInitialCurrentSection(schema, localState) {
		var sections = getSections(schema);
		if (!sections.length || !sections[0].id) {
			return '';
		}

		var currentSectionId = sections[0].id;
		var guard = 0;

		while (guard < sections.length * 2) {
			guard++;
			var nextSectionId = getNextSectionId(schema, currentSectionId, localState);
			if (!nextSectionId || nextSectionId === currentSectionId) {
				break;
			}

			if (getSectionIndexById(schema, nextSectionId) < 0) {
				break;
			}

			currentSectionId = nextSectionId;
		}

		return currentSectionId;
	}

	function initVisibilityRuntime() {
		$('[data-bform-schema]').each(function() {
			var $form = $(this);
			var rawSchema = $form.attr('data-bform-schema');
			if (!rawSchema) {
				return;
			}

			var schema;
			try {
				schema = JSON.parse(rawSchema);
			} catch (error) {
				return;
			}

			var initialState = collectLocalState($form);
			var initialSectionId = resolveInitialCurrentSection(schema, initialState);
			if (!initialSectionId && Array.isArray(schema.sections) && schema.sections[0] && schema.sections[0].id) {
				initialSectionId = schema.sections[0].id;
			}
			$form.data('bformCurrentSectionId', initialSectionId);
			$form.data('bformSectionHistory', []);

			renderVisibility($form, schema);
			updateActionButtons($form, schema);

			$form.on('change input', '[data-field-id]', function() {
				var $field = $(this);
				var $section = $field.closest('[data-section-id]');
				var sectionId = $section.data('section-id');
				var currentSectionId = $form.data('bformCurrentSectionId') || '';

				if (!sectionId || sectionId !== currentSectionId) {
					renderVisibility($form, schema);
					updateActionButtons($form, schema);
					return;
				}

				renderVisibility($form, schema);
				updateActionButtons($form, schema);
			});

			$form.on('click', '.bform-runtime-next', function() {
				var currentSectionId = $form.data('bformCurrentSectionId') || '';
				if (!currentSectionId) {
					return;
				}

				var localState = collectLocalState($form);
				var nextSectionId = getNextSectionId(schema, currentSectionId, localState);
				if (!nextSectionId || getSectionIndexById(schema, nextSectionId) < 0) {
					updateActionButtons($form, schema);
					return;
				}

				var history = $form.data('bformSectionHistory') || [];
				history.push(currentSectionId);
				$form.data('bformSectionHistory', history);
				$form.data('bformCurrentSectionId', nextSectionId);

				renderVisibility($form, schema);
				updateActionButtons($form, schema);
			});

			$form.on('click', '.bform-runtime-back', function() {
				var history = $form.data('bformSectionHistory') || [];
				if (!history.length) {
					return;
				}

				var previousSectionId = history.pop();
				$form.data('bformSectionHistory', history);
				if (previousSectionId && getSectionIndexById(schema, previousSectionId) >= 0) {
					$form.data('bformCurrentSectionId', previousSectionId);
				}

				renderVisibility($form, schema);
				updateActionButtons($form, schema);
			});

			$form.on('submit', function() {
				updateActionButtons($form, schema);
			});
		});
	}

	window.bformVisibilityEngine = {
		evaluateRule: evaluateRule,
		calculateVisibleSections: calculateVisibleSections,
		renderVisibility: renderVisibility,
		getNextSectionId: getNextSectionId
	};

	$(function() {
		initVisibilityRuntime();
	});

})( jQuery );
