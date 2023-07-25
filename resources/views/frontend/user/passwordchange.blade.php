@extends('frontend.layouts.user')
@section('content')


<!-- content area -->
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="pagetitle pb-2">
               Change Password
            </div>

        @if(session()->has('success'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-success" id="successMessage">{{ session()->get('success') }}</div>
            </div>
        </section>
        @endif
        @if(session()->has('error'))
        <section class="px-4">
            <div class="row my-3">
                <div class="alert alert-danger" id="errMessage">{{ session()->get('error') }}</div>
            </div>
        </section>
        @endif
            
        </div>
    </div>
    <div class="row ">
        <div class="col-lg-12">
            <div class="row ">
                <div class="col-lg-6 py-5 flex-column d-flex align-items-center">
                    
                    
                    <form action="{{route('user.pwdchange')}}" method="POST" enctype="multipart/form-data" >
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                    <label for="">Password</label>
                                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="password">
                                        
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                            </div>
                            <div class="col-lg-12">
                                
                                    <label for="password_confirmation">Confirm password</label>
                                    
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control  @error('password_confirmation') is-invalid @enderror" placeholder="Confirm password">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                        
                                        
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group mt-4">
                                    <button class="btn-theme bg-primary updateBtn" id="updateBtn" type="submit"> Change Password</button>

                                </div>
                            </div>
                        </div>
                    </form>
                    
                        
                </div>

                <div class="col-lg-6 border-left-lg  pt-3  ">
                    <div class="col-lg-11 mx-auto">
                        
                        <p><span style="color: red">***</span>The password must contain at least one symbol.</p>
                        <p><span style="color: red">***</span>The password must be at least 8 characters.</p>
                        <p><span style="color: red">***</span>The password must contain at least one uppercase and one lowercase letter.</p>
                    </div>
                  
                 
                </div> 
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')


<script>
    
</script>

@endsection
