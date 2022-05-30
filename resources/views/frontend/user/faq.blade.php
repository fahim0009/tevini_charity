@extends('frontend.layouts.user')

@section('content')



<div class="dashboard-content">
    <section class="profile purchase-status">
        <div class="title-section">
            <span class="iconify" data-icon="et:wallet"></span>
             <div class="mx-2">FAQ's </div>
        </div>
    </section>
  <section class="px-4">
    <div class="row  my-3">
        <h4 class="my-5 fw-bold text-secondary text-capitalize text-center ">Find some common questions here!</h4>
        <div class="col-md-12 mx-auto">
            <div class="accordion text-muted" id="accordionExample">
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How do I make a donation?
                    </button>
                  </h2>
                  <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                     Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel cum repellat reiciendis, ab nostrum error eligendi quae. Vitae nemo quo soluta recusandae fugit earum, quisquam quaerat sint corporis nam temporibus velit repudiandae necessitatibus, et exercitationem perferendis, unde ducimus eligendi voluptatum odit quis aliquid nesciunt! Libero perferendis quae aliquid sint quo.
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        How long does it take for a donation to be processed?
                    </button>
                  </h2>
                  <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                     Lorem ipsum dolor sit, amet consectetur adipisicing elit. Deserunt officia modi rerum impedit, magni earum quod quam sapiente voluptatum fugit? Esse aspernatur ipsa fugiat? Quam voluptatibus non rem sapiente cumque labore facere tenetur. Cupiditate eum numquam incidunt, consequatur maxime quas! Tenetur hic repudiandae, laboriosam laudantium aut asperiores consequuntur! Voluptatum, similique.
                    </div>
                  </div>
                </div>
                <div class="accordion-item">
                  <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Standing Orders - How do I set one up?
                    </button>
                  </h2>
                  <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                     Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero totam vel fuga porro, corrupti eligendi harum, vitae commodi nam ipsa quos voluptatem nihil ipsum? Eius, earum eos est eveniet dolorum perspiciatis exercitationem asperiores nostrum ipsam iure a rerum quos, quidem vero doloribus laudantium ea, nihil adipisci repellendus odio. Facere, tempore!
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
  </section>
</div>


@endsection




@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        $("#userfaq").addClass('active');
    });
</script>
@endsection
