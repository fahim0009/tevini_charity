@extends('frontend.layouts.user')
@section('content')
<style>
    /* Custom styles for Card Element iframe */
.StripeElement {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    color: #32325d;
    background-color: #f8f8f8;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
#card-element{
    margin-bottom: 20px;

}
#payButton{
    background-color: #007bff; /* Set the background color */
    color: #fff; /* Set the text color */
    font-size: 18px; /* Set the font size */
    padding: 10px 20px; /* Set padding */
    border: none; /* Remove border */
    border-radius: 4px; /* Set border radius */
    cursor: pointer; /* Set cursor */
}

/* Custom styles for invalid input in Card Element iframe */
.StripeElement--invalid {
    border-color: #fa755a;
}
</style>
<div class="dashboard-content py-2 px-4">
    <div class="container">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default credit-card-box">
                    <div class="panel-heading display-table" >
                        <div class="row display-tr" >
                            <h3 class="panel-title display-td" >Payment Details</h3>
                            <div class="display-td" >
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="ermsg">
                        </div>
                        @if (Session::has('success'))
                            <div class="alert alert-success text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif

                        <section class="px-4 no-print">
                          <div class="row my-3">
                              <div class="container">
                                  <div class="col-md-12 my-3">
                                      <p>**Please note that if you are topping up your account using a credit/debit card there will be an additional fee of 2% on top of the standard 5% commission fee alternatively you can top up by transfer to the following: Tevini Ltd S/C 40-52-40 A/C 00024463.</p>
                                  </div>
                                  
                              </div>
                          </div>
                      </section>
                        <!-- Include the Stripe Elements JS library -->
                        <script src="https://js.stripe.com/v3/"></script>

                        <!-- Create a form to collect card details -->
                        <form id="payment-form">
                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required'>
                                    <label class='control-label'>Topup Amount</label>
                                    <input class='form-control' id="amount" name="amount" placeholder='£' size='4' type='number' required>
                                </div>
                            </div>

                            <div class='form-row row'>
                                <div class='col-xs-12 form-group required'>
                                    <label class='control-label'>Name on Card</label>
                                    <input class='form-control' id="cardholder-name" name="cardholder_name" size='4' type='text' required>
                                </div>
                            </div>
                            <br>
                        <input type="hidden" name="donor_id" id="donor_id" value="{{auth()->user()->id}}">    
                        <div id="card-element"></div>
                        <button id="payButton" type="submit">Pay</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


@endsection

@section('script')

<script>
    // Create a Stripe instance with your publishable key
    var stripe = Stripe('pk_live_51KsS4xAynpveFrWHr7GiZOV2fLG1cYEkAlnm1SVeI93ENsDH6HQi8CoXNklvhbWP9Z9TNIzzfTR8gIi6205E2ejZ00uwYYwNpz');
  
    // Create a card element and mount it to the card-element div
    var cardElement = stripe.elements().create('card');
    cardElement.mount('#card-element');
  
    // Handle form submission
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
      event.preventDefault();
  
      // Create a PaymentMethod and confirm the PaymentIntent on the backend
      stripe.createPaymentMethod('card', cardElement).then(function(result) {
        if (result.error) {
          // Handle errors (e.g. invalid card details)
          console.error(result.error);
          $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>"+result.error.message+"</b></div>");
        } else {
          // Send the PaymentMethod ID to your backend
          var paymentMethodId = result.paymentMethod.id;
          confirmPayment(paymentMethodId);
        }
      });
    });
  
    var url = "{{URL::to('/user/stripe')}}";
    // Function to confirm the PaymentIntent on the backend
    function confirmPayment(paymentMethodId) {
        var amount = $("#amount").val();
        var cardHolderName = $("#cardholder-name").val();
        var donor_id = $("#donor_id").val();
      fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json', // Specify the Accept header for JSON
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },

        body: JSON.stringify({ payment_method_id: paymentMethodId, amount: amount, cardHolderName: cardHolderName, donor_id: donor_id })
      }).then(function(response) {
        return response.json();
      }).then(function(data) {
        // console.log(data);
        // Handle the response from the backend
        if (data.client_secret) {
          stripe.confirmCardPayment(data.client_secret).then(function(result) {
            if (result.error) {
                $(".ermsg").html("<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>"+result.error.message+"</b></div>");
              // Handle errors (e.g. authentication required)
              console.error(result.error);
            } else {
              // Payment successful
              $(".ermsg").html("<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Payment Successfull.</b></div>");
              console.log(result.paymentIntent);
            }
          });
        }
      });
    }
  </script>

@endsection



