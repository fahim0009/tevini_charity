@extends('frontend.layouts.user')
@section('content')
<section class="funded-profile">
    <div class="container">
        <div class="row col-lg-10 mx-auto">

            <div class="col-lg-8 my-4">
                <div class="donation">
                    <a href="{{ url()->previous() }}"class='hrt-styled-button py-2 text-decoradion-none'> Return to Dashboard</a>

                    <div class="donatearea p-3 border rounded my-4" id="donation">
                            <div class="d-flex align-items-center flex-wrap justify-content-between">
                                <div class='paymentLabel'>Use credit or debit card</div>
                                <div class="payment">
                                    <span class="iconify" data-icon="bx:bxl-visa" data-inline="false"></span>
                                    <span class="iconify" data-icon="fa:cc-discover" data-inline="false"></span>
                                    <span class="iconify" data-icon="bx:bxl-mastercard" data-inline="false"></span>
                                    <span class="iconify" data-icon="cib:american-express" data-inline="false"></span>
                                </div>
                            </div>

                            @if (Session::has('success'))
                            <div class="alert alert-success text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif
                            <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
                                @csrf

                                <h6 class="mt-4">Enter your top up ampunt</h6>
                                <div class="donateAmmount">
                                    <div class='first'><span>$</span>
                                        <span>USD</span>
                                    </div>
                                    <input type="number" autocomplete="off" id="amount" inputmode="numeric" maxlength="5"
                                        name="donationAmount" value="">
                                </div>

                                <div class="form-group">
                                    <div class="form-item">
                                        <label for=""> Email </label>
                                        <input type="email" name="email" class="form-control" placeholder="@if(Auth::user()) {{ Auth::user()->email}} @else  @endif">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-item">
                                        <label for="">First name </label>
                                        <input type="text" name="fname" class="form-control" placeholder=" @if(Auth::user()) {{ Auth::user()->fname}} @else  @endif">
                                    </div>
                                    <div class="form-item">
                                        <label for=""> Last name </label>
                                        <input type="text" name="lname" class="form-control" placeholder=" @if(Auth::user()) {{ Auth::user()->lname}} @else  @endif">
                                    </div>
                                </div>
                                <div class="form-group col-gap-adjust" >
                                    <div class="col-sm-9 card required">
                                        <input type="text" name="cnumber" placeholder="Card number" autocomplete='off' class='form-control card-number' size='20'>
                                    </div>

                                    <div class="col-sm-3 cvc required">
                                        <input type="text" name="cvv" placeholder="CVV" size='4' class="form-control card-cvc" autocomplete='off'>
                                    </div>
                                </div>
                                <div class="form-group col-gap-adjust" >
                                    <div class="form-item expiration required" >
                                        <input type="text" name="mm" placeholder="MM" class="form-control card-expiry-month" size='2'>
                                    </div>

                                    <div class="form-item expiration required">
                                        <input type="text" name="yy" placeholder="YYYY" class="form-control card-expiry-year" size='4'>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-item">
                                        <label for=""> Name on card </label>
                                        <input type="text" name="cname" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-item">
                                        <label for="">Country</label>
                                        <select name="country" id="" class="form-control">
                                            <option value="">country 1</option>
                                            <option value="">country 1</option>
                                            <option value="">country 1</option>
                                        </select>
                                    </div>
                                    <div class="form-item">
                                        <label for=""> Postal code</label>
                                        <input type="number" name="postcode" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-item">
                                        <label class=""><b>Donation Details </b></label> <br>
                                        <input type="checkbox" class="font-weight-bold" class="form-control">
                                        <small>  Don't display my name publicly on the campaign.</small>  <br>
                                        <input type="checkbox" class="font-weight-bold" class="form-control">
                                        <small>  Get occasional marketing updates from GoFundMe. You may unsubscribe at any time.</small>
                                    </div>
                                </div>

                                <div class='form-row row'>
                                    <div class='col-md-12 error form-group hide'>
                                        <div class='alert-danger alert'>
                                            
                                        </div>
                                    </div>
                                </div>

                                <input type="text" name="typeof" value="strip-donation">
                                <hr>

                                <button class="btn btn-theme" type="submit">Top Up</button>
                             </form>
                    </div>

                </div>
            </div>
         </div>

    </div>
</section>


@endsection
@section('script')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
$(function() {

    var $form   = $(".require-validation");

    $('form.require-validation').bind('submit', function(e) {
        var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');

        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
          var $input = $(el);
          if ($input.val() === '') {
            $input.parent().addClass('has-error');
            $errorMessage.removeClass('hide');
            e.preventDefault();
          }
        });

        if (!$form.data('cc-on-file')) {
          e.preventDefault();
          Stripe.setPublishableKey($form.data('stripe-publishable-key'));
          Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
          }, stripeResponseHandler);
        }

  });

  function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            /* token contains id, last4, and card type */
            var token = response['id'];

            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }

});
</script>

@endsection
