@component('mail::message')

@lang('Hello! Actor :actor sent you a message', ['actor' => $actorName])
</br>

@component('mail::panel')
    @lang('Message'): {{ $message }}
@endcomponent

<br>
<p>@lang('With best regards'),</p>
{{ config('app.name') }}
@endcomponent
