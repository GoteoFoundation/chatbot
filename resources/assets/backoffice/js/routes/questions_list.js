$(function(){
	// Datatable

	var table = $('#questions').DataTable({
		"dom": 'lfr<"questions_filter-container">tip',
		"initComplete": function (settings) {
			var api = new $.fn.dataTable.Api(settings);

			$('.questions_filter-container', api.table().container()).append(
				$('#questions_filter').detach().show()
			);

			$('#questions_filter select').on('change', function(){
				table.draw();
			});

			$('button[data-confirmation]').click(function(e) {
				e.preventDefault();

				var formObj = $(this).parents('form');

				$('#' + $(this).data('confirmation'))
						.modal('show')
						.on('click', 'button.accept', function(e) {
							formObj.submit();
						});
			});
		},
		"order": [],
		"processing": true,
		"searching": true,
		"serverSide": true,
		"ajax": {
			"url": goteoData.datatableEndpoint,
			"data": function (data) {
				data.filter_by = $('#questions_filter_select').val();
			}
		},
		"columns": [
			{ "data": 'id', "name": 'id', "width": "10%" },
			{ "data": 'question', "name": 'question' },
			{ "data": 'actions', "name": 'actions', "width": "30%" }
		],
		"columnDefs": [
			{
				"targets": 0,
				"searchable": false
			},
			{
				"targets": 1,
				"render": function (data, type, row, meta) {
					return decodeHTML(data);
				}
			},
			{
				"targets": 2,
				"sortable": false,
				"searchable": false,
				"render": function (data, type, row, meta) {
					return decodeHTML(data);
				}
			}
		],
		"language": {
			"zeroRecords": goteoData.translations.zeroRecords,
			"info": goteoData.translations.info,
			"infoEmpty": "",
			"infoFiltered": goteoData.translations.infoFiltered,
			"loadingRecords": goteoData.translations.loadingRecords,
			"processing": goteoData.translations.processing,
			"lengthMenu": goteoData.translations.lengthMenu,
			"search": goteoData.translations.search,
			"searchPlaceholder": goteoData.translations.searchPlaceholder,
			"paginate": {
				"first": goteoData.translations.paginate.first,
				"last": goteoData.translations.paginate.last,
				"next": goteoData.translations.paginate.next,
				"previous": goteoData.translations.paginate.previous
			},
		},
	});

	$(window).on('resize', function () {
		$('#questions').css('width', '100%');
	});

	var decodeHTML = function (html) {
		var txt = document.createElement('textarea');
		txt.innerHTML = html;
		return txt.value;
	};

	$("#copyNewQuestionForm").submit(function(e) {
		e.preventDefault();

		var copiedQuestionText = $("#copy_question_question").text();

		$('#copyNewQuestionFormButton').addClass('loading').prop('disabled', 'disabled');

		$.ajax({
			type: "POST",
			url: goteoData.questionCopyEndpoint,
			headers: {
				'X-CSRF-TOKEN': $(this).find('input[name="_token"]').val()
			},
			data: {
				question: $("#copy_question_question").val()
			},
			success: function(result) {
				location.reload();
			},
			error: function(result) {
				var response = JSON.parse(result.responseText);

				$('#copyNewQuestionFormButton').removeClass('loading').prop('disabled', '');
				alert(response.message);
			}
		});
	});

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

});
