@extends('layouts.app')  
  <!-- Content Wrapper. Contains page content -->
  
  @section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>

        Edit Ad
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Edit Ad</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <p class="text-right"><a href="{{ route('ads') }}" class="btn btn-primary">Back</a></p>
        <div class="box box-primary">
            @if(Session::has('success'))
                  <div class="alert alert-success alert-dismissable">
                      <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                      {{ Session::get('success') }}
                  </div>
              @endif

              @if(Session::has('fail'))
                  <div class="alert alert-danger alert-dismissable">
                      <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                      {{ Session::get('fail') }}
                  </div>
              @endif
        
            <form id="form" class="form-horizontal" role="form" method="POST" action="{{ url('post-edit-ad') }}" enctype="multipart/form-data">
              {{ csrf_field() }}
              {{ Form::hidden('id', $ad->id ) }}
              <input type="hidden" id="hidden_tab_no" name="hidden_tab_no" value="{{ old('hidden_tab_no') }}">
              <input type="hidden" id="ad" name="ad" value="{{ old('ad') }}">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="title" class="col-md-4 control-label">Title <span>*</span></label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control" name="title" value="{{ old('title')?old('title'):$ad->title }}">

                                @if ($errors->has('title'))
                                    <span class="help-block">{{ $errors->first('title') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('start_time') ? ' has-error' : '' }}">
                            <label for="start_time" class="col-md-4 control-label">Start Date <span>*</span></label>

                            <div class="col-md-6">
                                <input id="start_time" type="text" class="form-control" name="start_time" value="{{ old('start_time')?old('start_time'):$ad->start_time }}" readonly="">

                                @if ($errors->has('start_time'))
                                    <span class="help-block">{{ $errors->first('start_time') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        
                        <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label for="image" class="col-md-4 control-label">Image<br> (Size: 750x406) <span>*</span></label>

                            <div class="col-md-6">
                                <div style="width: 150px; height: 81px; overflow: hidden;"><img id="image_tag" src="{{ old('image')?old('image'):URL::to('images/'.$ad->image) }}" style="width: 100%; height: 100%;"></div>
                                <input id="image_small" type="file" class="form-control" name="image_small" onchange="readURL(this);">
                                <input type="hidden" id="image" name="image" value="{{ old('image') }}">
                                @if ($errors->has('image'))
                                    <span class="help-block">{{ $errors->first('image') }}</span>
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                            <label for="url" class="col-md-4 control-label">URL <span>*</span></label>

                            <div class="col-md-6">
                                <input id="url" type="text" class="form-control" name="url" value="{{ old('url')?old('url'):$ad->url }}">

                                @if ($errors->has('url'))
                                    <span class="help-block">{{ $errors->first('url') }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('end_time') ? ' has-error' : '' }}">
                            <label for="end_time" class="col-md-4 control-label">End Date <span>*</span></label>

                            <div class="col-md-6">
                                <input id="end_time" type="text" class="form-control" name="end_time" value="{{ old('end_time')?old('end_time'):$ad->end_time }}" readonly="">

                                @if ($errors->has('end_time'))
                                    <span class="help-block">{{ $errors->first('end_time') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="active" class="col-md-4 control-label">Active</label>

                            <div class="col-md-6">
                                <select id="is_active" type="text" class="form-control" name="is_active">
                                    <option value="0" <?php if(old("is_active")!=""){ if(old("is_active")==0) { echo "selected"; } } else { if($ad->is_active==0) {echo "selected"; }}?> >No</option>
                                    <option value="1" <?php if(old("is_active")!=""){ if(old("is_active")==1) { echo "selected"; } } else { if($ad->is_active==1) {echo "selected"; }}?> >Yes</option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                </div>
              
                <div class="row">
                    <div class="col-lg-6">

                        <div class="form-group">
                            <label for="active" class="col-md-4 control-label"></label>

                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

          </form>
        </div>
    </section>
    <!-- /.content -->
  </div>
  
  
  @endsection
  
@section('script')
<script>
    
$( document ).ready(function() {

    $('#start_time').datepicker();
    $('#end_time').datepicker();

});   


  function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(input).parent('div').find('img')
                        .attr('src', e.target.result)
                        //.width(150)
                        //.height(200);

                    $(input).next('input').val(e.target.result)
                    $(input).next('input').next('input').remove();
                        
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    
</script>
@endsection