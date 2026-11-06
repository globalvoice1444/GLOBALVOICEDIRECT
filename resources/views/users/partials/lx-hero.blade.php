{{-- v3 premium hero (2026-11-01): presentation only, chosen by path. Pages with their own v2 hero are skipped. Counts only from data the page already has. --}}
@php
 $lxP = request()->path();
 $lxMap = [
  'user/calls/inbound' => ['Call history', 'Inbound calls', 'Every call that reached your numbers, with status and duration.', 'icon-phone-incoming'],
  'user/calls/outbound' => ['Call history', 'Outbound calls', 'Calls placed from your extensions.', 'icon-phone-outgoing'],
  'user/calls/recorded' => ['Call history', 'Recorded calls', 'Listen to or download your call recordings.', 'icon-mic'],
  'user/calls/dial-out' => ['Call history', 'Dial-out calls', 'Calls placed through dial out.', 'icon-phone-forwarded'],
  'user/calls/conference-recordings' => ['Call history', 'Recorded conferences', 'Recordings from your conference rooms.', 'icon-headphones'],
  'user/conference-rooms' => ['Calls', 'Conference rooms', 'Meet with your team and clients on a dedicated dial-in.', 'icon-users'],
  'user/call-block' => ['Calls', 'Call block', 'Keep unwanted callers away from your lines.', 'icon-slash'],
  'user/mobile-app' => ['Calls', 'Mobile app', 'Take your business line with you.', 'icon-smartphone'],
  'user/webrtc' => ['Calls', 'WebRTC', 'Choose the extension your browser phone uses.', 'icon-headphones'],
  'user/greetings' => ['Business', 'Greetings', 'What callers hear when they reach your business.', 'icon-volume-2'],
  'user/phone-scripts' => ['Business', 'Phone scripts', 'Professional scripts for your greetings and menus, recorded for you.', 'icon-file-text'],
  'user/sms-campaigns' => ['Business', 'SMS campaign', 'Reach your customers with text messages.', 'icon-message-square'],
  'emailCampaign' => ['Business', 'Email campaign', 'Send email campaigns to your customer lists.', 'icon-mail'],
  'send_email' => ['Business', 'Email campaign', 'Send email campaigns to your customer lists.', 'icon-mail'],
  'ot_editor_sendEmail' => ['Business', 'Email campaign', 'Design and send an email to your customer lists.', 'icon-mail'],
  'user/phone-numbers' => ['Settings', 'Phone numbers', 'Your business numbers and where they ring.', 'icon-hash'],
  'user/extensions' => ['Settings', 'Extensions', 'Lines, voicemail and forwarding for your team.', 'icon-grid'],
  'user/users' => ['Settings', 'Users', 'People who can sign in to your account.', 'icon-user-check'],
  'user/extras' => ['Settings', 'Extras', 'Optional features for your account.', 'icon-plus-square'],
  'user/dial-out-numbers' => ['Settings', 'Dial out numbers', 'Numbers allowed to use dial out.', 'icon-phone-outgoing'],
 ];
 $lx = $lxMap[$lxP] ?? null;
 $lxChips = [];
 if ($lx) {
  if (isset($total) && is_numeric($total) && empty($error)) $lxChips[] = ['icon-bar-chart-2', (int) $total, ($total == 1 ? 'call' : 'calls') . (isset($ranges[$range ?? '']) ? ' · ' . strtolower($ranges[$range]) : '')];
  elseif (isset($rows) && is_array($rows) && empty($error) && in_array($lxP, ['user/phone-numbers', 'user/extensions', 'user/users', 'user/conference-rooms', 'user/dial-out-numbers'], true)) {
   $n = in_array($lxP, ['user/extensions'], true) ? count(array_filter($rows, fn ($r) => ($r['type'] ?? 'Account') === 'Account')) : count($rows);
   $lxChips[] = ['icon-layers', $n, ['user/phone-numbers' => 'numbers', 'user/extensions' => 'extensions', 'user/users' => 'users', 'user/conference-rooms' => 'rooms', 'user/dial-out-numbers' => 'numbers'][$lxP]];
  }
  if (isset($admin) && $admin === false && empty($error)) $lxChips[] = ['icon-eye', null, 'View only'];
 }
@endphp
@if ($lx)
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<script>document.documentElement.classList.add('lx-v3');</script>
<div class="lx lx-v3hero"><div class="lx-hero lx-in" style="padding:22px 26px"><div class="grid"></div>
 <div class="lx-eyebrow"><i class="feather {{ $lx[3] }}"></i> {{ $lx[0] }}</div>
 <h1 style="font-size:24px">{{ $lx[1] }}</h1><p>{{ $lx[2] }}</p>
 @if ($lxChips)<div class="row2">@foreach ($lxChips as $c)<span class="lx-glass"><i class="feather {{ $c[0] }}"></i>@if ($c[1] !== null)<b data-count="{{ $c[1] }}">{{ $c[1] }}</b>@endif {{ $c[2] }}</span>@endforeach</div>@endif
</div></div>
<script>document.querySelectorAll('.lx-v3hero [data-count]').forEach(function(e){var n=+e.dataset.count,s=performance.now();(function f(t){var p=Math.min(1,(t-s)/800);e.textContent=Math.round(n*(1-Math.pow(1-p,3))).toLocaleString();if(p<1)requestAnimationFrame(f);})(s);});</script>
@endif
@if ($lx)<script src="/js/gvd-lux-empty.js?v=1" defer></script>@endif
