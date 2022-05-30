@extends('layouts.admin')



@section('content')
    <main class="app-content">
        <div class="app-title">
            <div>
              <h1><i class="fa fa-edit"></i> Seo Settings</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
              <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
              <li class="breadcrumb-item">Forms</li>
              <li class="breadcrumb-item"><a href="#">Form Components</a></li>
            </ul>
          </div>
{{-- form start  --}}
      <div class="row">
        <div class="col-md-3">
        </div>
          <div class="col-md-6">
            <div class="card-header">
                <h3> Seo Settings</h3>
            </div>
            <div class="tile">
              <div class="row">
                <div class="col-lg-12">
                  <form>
                    <div class="form-group">
                      <label for="keyword">Key Word</label>
                      {{-- <textarea class="form-control" id="keyword" name="keyword" data-role="tagsinput" rows="4" placeholder="Enter your keyword">@if (!empty($seo->keyword)){{$seo->keyword}}@endif</textarea> --}}

                      <input type="text" class="form-control" name="tags[]" id="keyword" value="{{ $seo->keyword }}" placeholder="{{__('Type and Hit Enter')}}" data-role="tagsinput" multiple>

                    </div>

                    <div class="form-group">
                        <label for="author">Author</label>
                        <input class="form-control" id="author" name="author" value="@if (!empty($seo->author)){{$seo->author}}@endif" type="text">
                        <input class="form-control" id="seo_id" name="seo_id" value="@if (!empty($seo->id)){{$seo->id}}@endif" type="hidden">
                    </div>
                    <div class="form-group">
                        <label for="revisit">Revisit After</label>
                        <input class="form-control" id="revisit" name="revisit" value="@if (!empty($seo->revisit)){{$seo->revisit}}@endif" type="number">
                    </div>
                    <div class="form-group">
                        <label for="sitemap_link">Sitemap Link</label>
                        <input class="form-control" id="sitemap_link" name="sitemap_link" value="@if (!empty($seo->sitemap_link)){{$seo->sitemap_link}}@endif" type="text">
                    </div>

                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter your description">@if (!empty($seo->description)){{$seo->description}}@endif</textarea>
                    </div>

                  </form>
                </div>

              </div>
              <div class="tile-footer">
                  @if (!empty($seo->id))
                  <button class="btn btn-primary updateBtn" type="submit">Update</button>
                  @else
                  <button class="btn btn-primary submitBtn" type="submit">Submit</button>
                  @endif
                {{-- <button class="btn btn-primary updateBtn" type="submit">Update</button> --}}
              </div>
            </div>
          </div>
          <div class="col-md-3">
        </div>
        </div>
{{-- form end --}}

    </main>
    {{-- <script>
      CKEDITOR.replace( 'address' );
      </script> --}}


@endsection
@section('script')
    <script>
        $(document).ready(function () {

            //header for csrf-token is must in laravel
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            //

            var url = "{{URL::to('/admin/seo-settings')}}";
             //console.log(url);

             $(".submitBtn").click(function(){
              // alert("btn work");
                var form_data = new FormData();
                form_data.append("keyword", $("#keyword").val());
                form_data.append("author", $("#author").val());
                form_data.append("revisit", $("#revisit").val());
                form_data.append("sitemap_link", $("#sitemap_link").val());
                form_data.append("description", $("#description").val());

                $.ajax({
                  url: url,
                  method: "POST",
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function (d) {
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                      }else if(d.status == 300){
                        success("Create Successfully!!");
                            window.setTimeout(function(){location.reload()},2000)
                      }
                  },
                  error: function (d) {
                      console.log(d);
                  }
                });
              });

              // company details update;
              $(".updateBtn").click(function(){
                // alert('update btn work');
                //var facebook = $('#facebook').val();
                //alert(facebook);
                var form_data = new FormData();
                form_data.append("keyword", $("#keyword").val());
                form_data.append("author", $("#author").val());
                form_data.append("revisit", $("#revisit").val());
                form_data.append("sitemap_link", $("#sitemap_link").val());
                form_data.append("description", $("#description").val());
                form_data.append('_method', 'put');

                    // alert(name);
                    $.ajax({
                        url:url+'/'+$("#seo_id").val(),
                        type: "POST",
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        data:form_data,
                        success: function(d){
                            console.log(d);
                            if (d.status == 303) {
                                $(".ermsg").html(d.message);
                                pagetop();
                            }else if(d.status == 300){
                              success(" Update Successfully!!");
                                pagetop();
                                window.setTimeout(function(){location.reload()},2000)
                            }
                        },
                        error:function(d){
                            console.log(d);
                        }
                    });
                  });
                //Update end

        });
    </script>
    
    <script src="//cdn.ckeditor.com/4.13.0/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
             $("#seo").addClass('active');
        });
    </script>
@endsection
