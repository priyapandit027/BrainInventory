$(document).ready(function() {
    var siteUrl = 'http://127.0.0.1:8000/';

    var destinationGrid = $('#destination-table').DataTable({
        "responsive": true,
        "bProcessing": true,
        "serverSide": true,
        "autoWidth": false,
        "order": [
            [0, "desc"]
        ],
        dom: "<'row'<'col-sm-5 margin-left-10'li><'col-sm-7 pull-right margin-right-10'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 'B><'col-sm-7 pull-right'p>>",
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'control',
            'width': '5px',
        }],
        buttons: [{
            extend: "print",
            text: '<a data-toggle="tooltip" title="" data-original-title="Print"><i class="fa fa-print"></i></a>',
            title: $('#comment-table').attr("data-table-title"),
            exportOptions: {
                columns: [1, 2, 3]
                //columns: ":visible"
            }
        }, {
            extend: "excel",
            text: '<a data-toggle="tooltip" title="" data-original-title="Excel"><i class="fa fa-file-excel-o"></i></a>',
            exportOptions: {
                columns: [1, 2, 3]
            }
        }, {
            extend: "pdf",
            text: '<a data-toggle="tooltip" title="" data-original-title="Pdf"><i class="fa fa-file-pdf-o"></i></a>',
            title: $('#brand-table').attr("data-table-title"),
            exportOptions: {
                columns: [1, 2, 3]
            },
            customize: function(doc) {
                doc.content[1].table.widths =
                    Array(doc.content[1].table.body[0].length + 1).join('*').split('');
            }

        }, {
            extend: "copy",
            text: '<a data-toggle="tooltip" title="" data-original-title="Copy"><i class="fa fa-files-o"></i></a>',
            exportOptions: {
                columns: [1, 2, 3]
            }
        }, {
            extend: "colvis",
            text: '<a data-toggle="tooltip" title="" data-original-title="Colvis"><i class="fa fa-columns"></i></a>'

        }],
        language: {
            'processing': '<div class="overlay"><img src="image/Spinner.gif" width="150px"></div>',
        },
        "ajax": {
            url: siteUrl + 'destination-lists',
            type: "post",
            data: {
                "_token": $("#csrf").val(),
            }
        }

    });
});
