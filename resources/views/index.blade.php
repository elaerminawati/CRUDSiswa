<!DOCTYPE html>
<html lang="en">
<head>
  <title>student Report</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
<div class="container">
  <div class="col-md-12" style="text-align:center;">
    <strong><h2>Student Report</h2></strong>
    <br>
  </div>
  <div class="col-md-12" style="text-align:center;">
    <button class="btn btn-success" id="add">add new student</button>
  </div>
  <br>
  <div class="col-md-12" style="padding-top:5px;">
    <div class="alert alert-success alert-dismissible" id="success">
        <button type="button" class="close" id="closesuccess" aria-label="Close">&times;</button>
        <strong>Success!</strong> Data berhasil disimpan
    </div>
    <div class="alert alert-danger alert-dismissible" id="failed">
        <button type="button" class="close" id="closefailed" aria-label="Close">&times;</button>
        <strong>Error!</strong> 
        <ul id="errors">
        @if ($errors->any())
            @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
            @endforeach
        @endif
        </ul>
    </div>
  </div>
  <div class="col-md-12">
  {!! $html->table() !!}
  </div>
</div>
<div class="modal" id="processData">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Information</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
            <form>
                <input type="hidden" readonly id="id" name="id">
                <input type="hidden" readonly id="type" name="type">
                <div class="col-md-12">
                    <label>NIS</label>
                    <input type="text" name="nis" id="nis" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label>Nama</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
            </form>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button class="btn btn-success" id="save">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
{!! $html->scripts() !!}
<script type="text/javascript">
  $(function () {
    $("#success").hide();
    $("#failed").hide();
    var table = $("#dataTableBuilder").DataTable();
    $(document).on('click', '#add', function(e){
        $("#id").val("");
        $("#nis").val("");
        $("#nama").val("");
        $("#type").val("add");
        $("#processData").modal("show");
    });
    $( "#closesuccess" ).click(function() {
        $( "#success" ).hide();
    });
    $( "#closefailed" ).click(function() {
        $("#failed").hide();
    });
    $(document).on('click', '#edit', function(e){
        var datarow = table.row( $(this).parents('tr') ).data();
        $("#id").val($(this).data('id'));
        $("#nis").val(datarow.nis);
        $("#nama").val(datarow.nama);
        $("#type").val("update");
        $("#processData").modal("show");
    });

    $(document).on('click', '#save', function(){
        var form_data = $('form').serialize();
        if($("#type").val() == "add"){
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                url : "{{ route('save')}}",
                type : "POST",
                dataType : "JSON",
                data: form_data, 
                success :  function(data){
                        $("#processData").modal("hide");
                        $("#success").show();
                        table.ajax.reload();
                }, error : function(data){   
                    if(data.status !== 200){
                        var errors = JSON.parse(data.responseText);
                        var errorsHtml = '';
                        $.each(errors.message, function( key, value ) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        $('#errors').html(errorsHtml);
                        $("#failed").show();
                        $("#processData").modal("hide");
                    }
                }
            });
        }else{
            $.ajax({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                },
                url : "{{ route('update')}}",
                type : "POST",
                dataType : "JSON",
                data: form_data, 
                success :  function(data){
                    $("#processData").modal("hide");
                    $("#success").show();
                    table.ajax.reload();
                }, error : function(data){
                    if(data.status !== 200){
                        var errors = JSON.parse(data.responseText);
                        var errorsHtml = '';
                        $.each(errors.message, function( key, value ) {
                            errorsHtml += '<li>' + value[0] + '</li>';
                        });
                        $('#errors').html(errorsHtml);
                        $("#failed").show();
                        $("#processData").modal("hide");
                    }
                }
            });
        }
        
    });

    $(document).on("click", "#delete", function(){
        var id = $(this).data('id');
        $.ajax({
            headers : {
                'X-CSRF-Token': "{{ csrf_token() }}"
            },
            url : "{{ route('delete') }}",
            type : "POST",
            dataType : "JSON",
            data : {
                'id' : id
            }, success : function(data){
                table.ajax.reload();
            }, error :  function(thrownError){
                console.log(thrownError);
            }
        });
    });
    
  });
</script>
</body>
</html>
