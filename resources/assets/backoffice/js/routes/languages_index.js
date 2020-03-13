$(function(){
	// Datatable

	var table = $('#languages').DataTable({
		"dom": 'lfrtip',
		"processing": true,
		"searching": true,
		"serverSide": true,
		"ajax": goteoData.datatableEndpoint,
		"columns": [
			{ "data": 'id', "name": 'id', "width": "10%" },
			{ "data": 'name', "name": 'name' },
			{ "data": 'actions', "name": 'name', "width": "30%" }
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
		"initComplete": function(settings, json) {
			$('button[data-confirmation]').click(function(e) {
				e.preventDefault();

				var formObj = $(this).parents('form');

				$('#' + $(this).data('confirmation'))
					.modal('show')
					.on('click', 'button.accept', function(e) {
						formObj.submit();
					});
			});
		}
	});

	$(window).on('resize', function () {
		$('#languages').css('width', '100%');
	});

	var decodeHTML = function (html) {
		var txt = document.createElement('textarea');
		txt.innerHTML = html;
		return txt.value;
	};
});
