@component('mail::message')
# Portal Tormenta 20

@component('mail::panel')
**Olá!** Você está recebendo este e-mail porque foi solicitada a redefinição da senha da sua conta.
@endcomponent

@component('mail::button', ['url' => $url])
Redefinir Senha
@endcomponent

O link para redefinir a senha vai expirar em **{{ $expire }} minutos**.
Se você não solicitou a redefinição, ignore este e-mail.

Obrigado,
**Portal Tormenta 20**
@endcomponent