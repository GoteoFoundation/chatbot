$(function(){

	// Repeater

	var currentSelect2Id;
	var repeaterItemsLastIndex = goteoData.initialRepeaterIndex;

	// Prevent removing any item when only 2 on screen

	var updateDeletionAvailability = function() {
		var cardsVisibleObj = $('.card-body:visible');

		if(cardsVisibleObj.length <= 2) {
			cardsVisibleObj.find('button[data-repeater-delete]').hide();
		}
		else {
			cardsVisibleObj.find('button[data-repeater-delete]').show();
		}
	}

	var updateChangeAnswerTypeEvent = function(obj) {
		obj.find('input.answer_type').click(function() {
			$(this).parents('.card-body').find('div[class*="answer_type_option_"]').hide();
			$(this).parents('.card-body').find('.answer_type_option_' + 	$(this).val()).show();

			var firstUrlLangInput = $(this).parents('.card-body').find('.answer_type_option_url input[type=url]').first();
			var firstQuestionLangSelect = $(this).parents('.card-body').find('.answer_type_option_question select').first();

			if(	$(this).val() === 'url') {
				firstUrlLangInput.prop('required', 'required');
				firstQuestionLangSelect.prop('required', '');
			}
			else {
				firstUrlLangInput.prop('required', '');
				firstQuestionLangSelect.prop('required', 'required');
			}
		});
	};

	var func_repeater = function() {
		repeaterItemsLastIndex++;
		attributesToUpdate = ['id', 'href', 'labelledby', 'aria-controls'];

		for (var i = 0; i < attributesToUpdate.length; i++) {
			var currentAttr = attributesToUpdate[i];

			$(this).find('[' + currentAttr + '$="-id1"]').each(function () {
				var oldAttr = $(this).attr(currentAttr);

				$(this).attr(currentAttr, (oldAttr.substring(0, (oldAttr.length - 1)) + repeaterItemsLastIndex));
			});
		}

		updateChangeAnswerTypeEvent($(this));

		if ($(this).find('input.answer_type:checked').length == 0) {
			$(this).find('input.answer_type').first().prop('checked', 'checked');
		}

		$(this).find('.select2-questions').select2({
			width: 'resolve',
			placeholder: goteoData.translations.questionSelectPlaceholder,
			ajax: {
				url: goteoData.questionIndexEndpoint + '/' + (Object.is(goteoData.currentQuestionId, undefined) ? '0' : goteoData.currentQuestionId)
			}
		});

		$(this).find('button[data-toggle="modal"]').click(function () {
			currentSelect2Id = $(this).parents('.answers_group').find('select[data-select2-id]').first().data('select2-id');
		});

		$(this).slideDown();
		updateDeletionAvailability();
	};

	// Init visible items Select2
	$('.card-body:visible').find('.select2-questions').select2({
		width: 'resolve',
		placeholder: goteoData.translations.questionSelectPlaceholder,
		ajax: {
			url: goteoData.questionIndexEndpoint + '/' + (Object.is(goteoData.currentQuestionId, undefined) ? '0' : goteoData.currentQuestionId)
		}
	});

	$("#answers").repeater({
		initEmpty: goteoData.isEmpty,
		show: func_repeater,
		hide: function (deletedElement) {
			var itemObj = $(this);

			$('#confirmDeletion')
				.modal('show')
				.on('click', 'button.accept', function(e) {
					itemObj.slideUp(deletedElement, function() {
						updateDeletionAvailability();
					});
				});
		}
	});

	$('#answers [data-repeater-delete-me-onload]').remove();

	// Autocreate until 2 empty repater items onload if needed

	if(repeaterItemsLastIndex < 2) {
		var itemsToAdd = (2 - repeaterItemsLastIndex);

		for(var i = 0; i < itemsToAdd; i++) {
			$('[data-repeater-create]').click();
		}
	}

	// Execute prevention on first load
	updateDeletionAvailability();

	// Control required inputs when changing questions type on first load
	updateChangeAnswerTypeEvent($('#answers .card-body'));

	// Select 2 init

	$('#copy_question_topic').select2({
		width: 'resolve',
		placeholder: goteoData.translations.topicSelectPlaceholder,
		ajax: {
			url: goteoData.topicsEndpoint,
			data: function (params) {
				params.topic = goteoData.topicId;
				return params;
			}
		}
	});

	$('#copy_question_topic').on('select2:select', function (e) {
		$('#copy_question_question').prop('disabled', false);
		$('#copy_question_question').data('topic', $(this).val());
		$('#copy_question_question').empty().trigger('change');
		$('#copy_question_question').select2({
			placeholder: goteoData.translations.questionSelectPlaceholder,
			ajax: {
				url: goteoData.topicIndexEndpoint,
				data: function (params) {
					params.topic = $('#copy_question_question').data('topic');
					return params;
				}
			}
		});
	});

	// Init visible repeaters

	$('input.answer_type').click(function() {
		$(this).parents('.card-body').find('div[class*="answer_type_option_"]').hide();
		$(this).parents('.card-body').find('.answer_type_option_' + $(this).val()).show();

		var firstUrlLangInput = $(this).parents('.card-body').find('.answer_type_option_url input[type=url]').first();
		var firstQuestionLangSelect = $(this).parents('.card-body').find('.answer_type_option_question select').first();

		if($(this).val() === 'url') {
			firstUrlLangInput.prop('required', 'required');
			firstQuestionLangSelect.prop('required', '');
		}
		else {
			firstUrlLangInput.prop('required', '');
			firstQuestionLangSelect.prop('required', 'required');
		}
	});


	// Main form submit button handling

	$('#editQuestionFormButton').click(function(e) {
		// Select first language tab (the required one) to see validation

		$('div.tabs-langs').each(function() {
			$(this).find('a.nav-link').first().tab('show');
		});
	});


	// Add new question / copy question

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// Reset create & copy modals when open
	$('#createNewQuestionModal, #copyNewQuestionModal').on('show.bs.modal', function (e) {
		$(this).find('form')[0].reset();
		$(this).find('div.tabs-langs').each(function() {
			$(this).find('a.nav-link').first().tab('show');
		});
		$('#copy_question_topic, #copy_question_question').empty().trigger('change');
		$('#copy_question_question').prop('disabled', 'disabled');
	})

	$('#createNewQuestionFormButton').click(function(e) {
		// Select first language tab (the required one) to see validation

		$('#createNewQuestionModal div.tabs-langs').each(function() {
			$(this).find('a.nav-link').first().tab('show');
		});
	});

	$('#createNewQuestionForm').submit(function(e) {
		e.preventDefault();

		var newQuestionText = $("input[name^='new_question']").first().val();

		$.ajax({
			type: "POST",
			url: goteoData.questionStoreEndpoint,
			data: $("input[name^='new_question']").serialize(),
			success: function(result) {
				$('#createNewQuestionModal').modal('hide');

				$.ajax({
					type: 'GET',
					url: goteoData.questionIndexEndpoint + '/' + (Object.is(goteoData.currentQuestionId, undefined) ? '0' : goteoData.currentQuestionId) + '/' + result.data.id
				}).then(function (data) {

					var select2Obj = $('.answers_group select[data-select2-id=' + currentSelect2Id + ']').first();
					var option = new Option(data.results[0].text, data.results[0].id, true, true);
					select2Obj.append(option).trigger('change');

					// manually trigger the `select2:select` event
					select2Obj.trigger({
						type: 'select2:select',
						params: {
							data: data
						}
					});
				});
			},
			error: function(result) {
				var response = JSON.parse(result.responseText);
				alert(response.message);
			}
		});
	});

	$("#copyNewQuestionForm").submit(function(e) {
		e.preventDefault();

		var copiedQuestionText = $("#copy_question_question").text();

		$('#copyNewQuestionFormButton').addClass('loading').prop('disabled', 'disabled');

		$.ajax({
			type: "POST",
			url: goteoData.questionCopyEndpoint,
			data: { question : $("#copy_question_question").val() },
			success: function(result) {
				$('#copyNewQuestionFormButton').removeClass('loading').prop('disabled', '');
				$('#copyNewQuestionModal').modal('hide');

				$.ajax({
					type: 'GET',
					url: goteoData.questionIndexEndpoint + '/' + (Object.is(goteoData.currentQuestionId, undefined) ? '0' : goteoData.currentQuestionId) + '/' + result.data.id
				}).then(function (data) {

					var select2Obj = $('.answers_group select[data-select2-id=' + currentSelect2Id + ']').first();
					var option = new Option(data.results[0].text, data.results[0].id, true, true);
					select2Obj.append(option).trigger('change');

					// manually trigger the `select2:select` event
					select2Obj.trigger({
						type: 'select2:select',
						params: {
							data: data
						}
					});
				});
			},
			error: function(result) {
				var response = JSON.parse(result.responseText);

				$('#copyNewQuestionFormButton').removeClass('loading').prop('disabled', '');
				alert(response.message);
			}
		});
	});


	// Create New Question

	$("#items").sortable({
		placeholder: "ui-state-highlight",
		handle: ".fa-grip-lines",
		cursor: "move",
		classes: {
			"ui-sortable": "highlight"
		}
	});
});
