@component('mail::message')
# Password change Request

Use this Link to reset Your Paasword

@component('mail::button', ['url' => 'http://localhost:4200/reset-password-response?token='.$token])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
