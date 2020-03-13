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
});
