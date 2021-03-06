@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="content-wrapper">
        <!-- Venue, Checked In Visitors, Checked In Members, Firelist -->
        <div class="row mb-3">
          	<div class="col-md-12">
            	<div class="content-header">Edit Site</div>
          	</div>
        </div>

        <section class="edit-site">
          	<div class="card">
            	<div class="card-body">
            		{!! Form::model($item, ['files' => 'true', 'method' => 'PATCH', 'route' => ['site.update', $item->id]]) !!}
            		@csrf
                	<div class="row">
                  		<div class="col-md-12">
                    		<fieldset class="form-group">
                      			<label>Site Name</label>
                      			<input type="text" class="form-control" id="sitename" name="name" value="{{ $item->name }}" placeholder="Enter Site Name">
                   			</fieldset>
                  		</div>
		                <div class="col-md-6">
		                    <fieldset class="form-group">
		                    	<label>Select State</label>
		                    	<select name="state" id="state" class="form-control">
            						<option value="">Select a State</option>
            						@foreach($state as $value)
            							<option value="{{$value['id']}}" <?php if($value['name'] == $item->state){echo 'selected';}  ?>>{{$value['name']}}</option>
            						@endforeach
            					</select>
		                    </fieldset>
		                  </div>
		                  <div class="col-md-6">
		                    <fieldset class="form-group city-data" data-city="{{ $item->city }}">
		                    	<label>Select City</label>
	                    	    <select name="city" id="city" class="form-control">
            					</select>
		                    </fieldset>
		                  </div>
		                  <div class="col-md-12">
		                    <fieldset class="form-group">
		                      <label>Address</label>
		                      <textarea rows="3" class="form-control" name="address" placeholder="Address">{{ $item->address }}</textarea>
		                    </fieldset>
		                  </div>
		                  <div class="col-md-12">
		                    <fieldset class="form-group">
		                      <label class="mb-1">Site Admins</label>
		                      <div class="row site-admins">
		                        <div class="col-md-2">
		                            <ul>
		                            	@if(count($siteEmployeeData['siteEmployees']) > 0)
			                            	@foreach($siteEmployeeData['siteEmployees'] as $siteEmployee)
				                              	<li id="employee_id" value="{{$siteEmployee['employeeDetail']['id']}}">{{$siteEmployee['employeeDetail']['employee_name']}}<a href="#"><i class="ft-user-minus removedEmployee" data-employeeid="{{$siteEmployee['employeeDetail']['id']}}"></i></a></li>
			                            	@endforeach
			                            @else
			                            	<li>------</li>
			                            @endif

		                            </ul>
		                        </div>

		                      </div>
		                    </fieldset>
		                  </div>
		                </div>
		                <div class="row">
		                  	<div class="col-md-12 btns">
		                    	<button type="submit" class="btn btn-primary">Edit Site</button>
		                    	<a class="btn btn-grey" href="{{ url('site') }}"> Cancel</a>
		                  	</div>
		                </div>
              		{!! Form::close() !!}
           		</div>
          	</div>
        </section>
    </div>
</div>

<!-- Add as venue Admin message-->
<div class="modal fade text-left" id="remove-employee-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <div class="removeMessage"></div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
    </div>
  </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
jQuery(document).ready(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var city = $('.city-data').data("city");
    $("#city").append('<option value="1" selected>'+city+'</option>');

    $('#state').on('change',function(){
	    var stateID = $(this).val();

	    if(stateID){
	        $.ajax({
	           type:"GET",
	           url:"{{url('getCityList')}}?state_id="+stateID,
	           success:function(res){
	            if(res){
	                $("#city").empty();
	                $("#city").append('<option value="">Select City</option>');
	                $.each(res,function(key,value){
	                    $("#city").append('<option value="'+key+'">'+value+'</option>');
	                });
	            }else{
	               $("#city").empty();
	        	}
	        }
	        });
	    }else{
	        $("#city").empty();
	    }
   	});

   	jQuery('.removedEmployee').click(function (event) {
		var site_id     = {{ $item->id }};
		var employee_id = $(this).data('employeeid');
		var CSRF_TOKEN  = '{{ csrf_token() }}';
        $.ajax({
            type: "POST",
            url: '{!! route('site.removeSiteEmployee') !!}',
            data: {
                _token : CSRF_TOKEN,
                site_id:site_id,
                employee_id:employee_id,
            },
            dataType : 'JSON',
            success: function( response ) {
                if(response.success == "1"){
                    $('.removeMessage').html(response.msg);
                    $('#remove-employee-modal').modal('show');
                    location.reload();
                }else{
                    $('.removeMessage').html(response.msg);
                    $('#remove-employee-modal').modal('show');
                }
            }
        });
    });
});
</script>
@endsection