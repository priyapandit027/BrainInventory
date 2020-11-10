@extends('layouts.app')

@section('content')
<input type="hidden" id="csrf" value="{{ csrf_token() }}">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Dashboard') }}</div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="col-sm-12">
                    <table class="table table-striped dt-responsive nowrap" cellspacing="0" id="destination-table" width="100%">
                        <thead>
                            <tr>
                                <th class="no-sort-column"></th>
                                <th>Place name</th>
                                <th >Rating</th>
                                <th>Review</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal fade" id="ratingModal" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Update Rating & Review</h4>
                            </div>
                            <form class="tagForm" id="rating-form" action="{{ 'http://127.0.0.1:8000/update-rating' }}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="hidDestinationId" id="hidDestinationId" value="">
                                <div class="modal-body">
                                    <div class="input-group-prepend">
                                        <input type="number" name="rating" id="rating" class="form-control" placeholder="Add rating" aria-label="Username" aria-describedby="basic-addon1">
                                    </div><br>
                                    <div class="input-group-prepend">
                                        <textarea class="form-control" name="review" id="review" placeholder="write review in max 20 characters" aria-label="With textarea"></textarea>
                                    </div>

                                </div>
                            </form>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" id="submitRating" class="btn btn-default">Update</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" src= "https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css"/>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"/>
<script>
$(document).ready(function () {
    $(document).on('click', '.updateRating', function (e) { 
        var rating = $(this).parent().parent().find("td:nth-child(3) #ratingValue"+this.id).val();
        var review = $(this).parent().parent().find("td:nth-child(4)").html();
        $('#hidDestinationId').val(this.id);
        $("#rating").val(rating);
        $("#review").val(review);
    });
    $("#ratingModal").on("hidden.bs.modal", function () {
        $(this).find('textarea').val('');
        $(this).find('input').val('');
    });
    $(document).on('click', '#submitRating', function (e) {
        var rating = $("#rating").val();
        var review = $("#review").val();
        var length = rating.length;
        if(rating == '' || review == '') {
            toastr.error("Please enter both the values");
        } else if(rating > 5) {
            toastr.error("Rating cannot be grater than 5");
        } else if(rating == 0) {
            toastr.error("Rating should be greater than 1");
        } else if (length > 20) {
            toastr.error("Characters cannot greater than 20");
        } else {
            var url = 'http://127.0.0.1:8000/update-rating';
            $.ajax({
                url: url,
                type: 'post',
                data: {formValue:$('#rating-form').serialize(),
                    _token: $("#csrf").val(),},
                success: function (data) {
                    $('#ratingModal').modal('hide');
                    destinationGrid.draw();
                    toastr.success(data.message);
                },
                error: function (data) {
                    toastr.error('please try again!');
                },
                complete: function () {
                    return;
                }
            });
        }
    });
    var destinationGrid = $('#destination-table').DataTable({
        "responsive": true,
        "bProcessing": true,
        "serverSide": true,
        "autoWidth": false,
        "order": [[0, "desc"]],
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
                    columns: [1, 2,3]
                            //columns: ":visible"
                }
            }, {
                extend: "excel",
                text: '<a data-toggle="tooltip" title="" data-original-title="Excel"><i class="fa fa-file-excel-o"></i></a>',
                exportOptions: {
                    columns: [1, 2,3]
                }
            }, {
                extend: "pdf",
                text: '<a data-toggle="tooltip" title="" data-original-title="Pdf"><i class="fa fa-file-pdf-o"></i></a>',
                title: $('#brand-table').attr("data-table-title"),
                exportOptions: {
                    columns: [1, 2,3]
                },
                customize: function (doc) {
                    doc.content[1].table.widths =
                            Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                }

            }, {
                extend: "copy",
                text: '<a data-toggle="tooltip" title="" data-original-title="Copy"><i class="fa fa-files-o"></i></a>',
                exportOptions: {
                    columns: [1, 2,3]
                }
            }, {
                extend: "colvis",
                text: '<a data-toggle="tooltip" title="" data-original-title="Colvis"><i class="fa fa-columns"></i></a>'

            }],
        language: {
            'processing': '<div class="overlay"><img src="image/Spinner.gif" width="150px"></div>',
        },
        "ajax": {
            //url: siteUrl + 'destination-lists',
            url: 'http://127.0.0.1:8000/destination-lists',
            type: "post",
            data: {
                "_token": $("#csrf").val(),
            }
        }
    });
});
</script>
@endsection