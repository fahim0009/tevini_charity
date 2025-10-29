@component('mail::message')
# Verify Your Email

Hi there,

Please verify your email address by clicking the button below.

@component('mail::button', ['url' => route('verify.email', $data->verification_token)])
Verify Email
@endcomponent

If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
