@component('mail::message')

@lang('Hi'),
</br>

@component('mail::panel')
    @lang('Verification code'): <strong><h2>{{ $code }}</h3></strong>
@endcomponent

@lang('The code will be valid for :minutes', [
    'minutes' => $minutes
])
<br>
<p>@lang('With best regards'),</p>
{{ config('app.name') }}
@endcomponent
