{{-- resources/views/vendor/mail/html/message.blade.php --}}
@component('mail::layout')
{{-- Sem header, sem footer, só o conteúdo do e-mail --}}
{{ $slot }}

{{-- Se quiser uma sub-mensagem pequena, use a seção "subcopy" no markdown --}}
{{ $subcopy ?? '' }}
@endcomponent