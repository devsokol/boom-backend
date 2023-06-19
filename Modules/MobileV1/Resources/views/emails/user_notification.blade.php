@component('mail::message')

{{ $body }}

<br>
<p>@lang('With best regards'),</p>
{{ config('app.name') }}
@endcomponent
