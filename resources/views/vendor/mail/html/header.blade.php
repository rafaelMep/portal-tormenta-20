@php
$appUrl = config('app.url'); // certifique-se que é uma URL pública em produção
@endphp
<tr>
    <td class="header">
        <a href="{{ $appUrl }}" style="display:inline-flex; align-items:center; gap:10px;">
            {{-- use um logo hospedado publicamente (ex.: /images/logo-email.png) --}}
            <img src="{{ $appUrl }}/images/logo-email.png" alt="Portal Tormenta 20" width="120" height="auto" style="display:block;">
            <span>Portal Tormenta 20</span>
        </a>
    </td>
</tr>