@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
<style>
  .gvd-gr .muted{color:#888;font-size:13px}
  .gvd-gr .now{background:#eef6ff;border:1px solid #cfe3ff;border-radius:6px;padding:10px 14px;margin-bottom:14px}
  .gvd-gr table td{vertical-align:middle}
  .gvd-gr tr.gr-row{cursor:pointer}
  .gvd-gr tr.gr-row:hover{background:#fafafa}
  .gvd-gr .badge-alt{background:#e7e7ff;color:#3b3b9a;border-radius:10px;padding:2px 8px;font-size:12px}
  .gr-player{display:flex;align-items:center;gap:8px;min-width:220px}
  .gr-player button{border:0;background:#1a73e8;color:#fff;border-radius:50%;width:30px;height:30px;line-height:30px;padding:0}
  .gr-player .bar{flex:1;height:6px;background:#e3e3e3;border-radius:3px;position:relative;cursor:pointer}
  .gr-player .bar i{position:absolute;left:0;top:0;bottom:0;background:#1a73e8;border-radius:3px;width:0}
  .gr-player .t{font-size:12px;color:#666;min-width:76px;text-align:right}
  .gr-more{position:relative}
  .gr-more ul{display:none;position:absolute;right:0;top:24px;background:#fff;border:1px solid #ddd;border-radius:6px;list-style:none;padding:4px 0;margin:0;z-index:20;min-width:150px;box-shadow:0 4px 12px rgba(0,0,0,.1)}
  .gr-more ul li a{display:block;padding:6px 14px;color:#333}
  .gr-more ul li a:hover{background:#f3f3f3}
  .gr-more.open ul{display:block}
  #gr-panel{position:fixed;top:0;right:-480px;width:460px;max-width:95vw;height:100%;background:#fff;box-shadow:-4px 0 16px rgba(0,0,0,.15);z-index:1050;transition:right .25s;overflow-y:auto;padding:18px}
  #gr-panel.open{right:0}
  #gr-shade{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:1040}
  #gr-shade.open{display:block}
  .gr-alt{border:1px solid #eee;border-radius:6px;padding:10px;margin-bottom:10px}
  .gr-alt .on{color:#0a7a3a;font-size:12px}
  .gr-mg-h{background:#0b8fd8;color:#fff;padding:10px 16px;border-radius:2px;font-size:17px;margin-bottom:12px}
  .gr-cards{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:18px}
  .gr-card{flex:1 1 380px;border:1px solid #e4e4e4;border-radius:3px;background:#fff}
  .gr-card h6{margin:0;padding:12px 14px;border-bottom:1px solid #eee;font-weight:700}
  .gr-card .acts{padding:10px 18px}
  .gr-card .acts a{display:block;padding:5px 0;color:#333;text-decoration:none}
  .gr-card .acts a.dis{color:#aaa;cursor:not-allowed}
  .gr-card .acts .ic{display:inline-block;width:22px;color:#1a8fe0}
  .gr-box{margin:0 14px 14px;border:1px solid #c9d6f5;background:#e8eefc;border-radius:3px;padding:10px 12px}
  .gr-box.hrs{background:#e1f0f8;border-color:#bfe0f0}
  .gr-box .lbl{font-size:11px;color:#666}
  .gr-box .val{border-bottom:1px solid #777;padding:3px 0;margin-bottom:10px}
  .gr-box .per{color:#3949ab;padding:6px 0}
  .gr-box .sync{color:#888;font-size:13px}
  #gr-bar{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:2px solid #333;padding:8px 18px;display:none;align-items:center;gap:12px;z-index:1030}
  #gr-bar.on{display:flex}
  #gr-bar button{border:0;background:none;color:#1a8fe0;font-size:18px}
  #gr-bar .seek{flex:1}
  #gr-bar .nm{font-size:13px;color:#555;min-width:120px}
  .gvd-gr .ro{background:#fff8e1;border:1px solid #ffe08a;border-radius:6px;padding:8px 12px;margin-bottom:12px;font-size:13px}
</style>
<div class="row gvd-gr">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h5>Greetings</h5>
        <span class="muted">What callers hear when they call your company or reach an extension's voicemail.</span>
      </div>
      <div class="card-block">
        @if ($error)
          <p class="muted">{{ $error }}</p>
        @else
          @if ($nowText)
            <div class="now"><strong>Now playing:</strong> {{ $nowText }} <span class="muted">&middot; as of {{ now('America/New_York')->format('g:i A') }} ET</span></div>
          @endif
          @foreach ($notes as $n)<div class="ro">{{ $n }}</div>@endforeach
          @if (count($cards))
            <div class="gr-mg-h">Manage greetings</div>
            <div class="gr-cards">
            @foreach ($cards as $c)
              <div class="gr-card">
                <h6>{{ $c['title'] }}</h6>
                <div class="acts">
                  <a href="#" class="gr-play" data-src="{{ $c['audio'] }}" data-name="{{ $c['title'] }} greeting"><span class="ic">&#9654;</span>Play current audio</a>
                  @if ($admin)
                    <a href="#" data-act="replace" data-mode="record" data-target="{{ $c['key'] }}"><span class="ic">&#9673;</span>Record new audio</a>
                    <a href="#" data-act="replace" data-mode="upload" data-target="{{ $c['key'] }}"><span class="ic">&#11014;</span>Upload new audio</a>
                  @endif
                  <a class="dis" title="No unsaved changes to discard"><span class="ic" style="color:#aaa">&#8630;</span>Discard</a>
                </div>
                @if ($c['hours'])
                  <div class="gr-box hrs">
                    <div class="lbl">Active hours</div><div class="val">{{ $c['hours']['label'] }}</div>
                    @foreach ($c['hours']['periods'] as $p)<div class="per">&#128197; {{ $p }}</div>@endforeach
                  </div>
                @endif
                <div class="gr-box">
                  <div class="lbl">If there is no input after greeting plays</div><div class="val">{{ $c['noInput']['action'] }}</div>
                  @if ($c['noInput']['target'])<div class="val">{{ $c['noInput']['target'] }}</div>@endif
                  <label class="sync"><input type="checkbox" disabled {{ $c['sync'] ? 'checked' : '' }}> Sync greetings with all phone numbers</label>
                </div>
              </div>
            @endforeach
            </div>
          @endif
          @if (!$admin)
            <div class="ro">You can listen to greetings. Only your company administrator can change them.</div>
          @endif
          <div class="table-responsive">
          <table class="table">
            <thead><tr><th>Greeting</th><th>Current greeting</th><th>If caller doesn't press anything</th><th></th><th></th></tr></thead>
            <tbody>
            @foreach ($rows as $i => $r)
              <tr class="gr-row" data-i="{{ $i }}">
                @php $ext = array_values(array_filter($r['alternates'], fn ($a) => !empty($a['extended']))); $oth = count($r['alternates']) - count($ext); @endphp
                <td><strong>{{ $r['name'] }}</strong>@if (count($ext)) <span class="badge-alt">Extended absence greeting</span>@endif<div class="muted">{{ $r['schedule'] }}</div></td>
                <td class="gr-noclick"><div class="gr-player" data-src="{{ $r['audio'] }}"></div></td>
                <td>{{ $r['kind'] === 'company' ? $r['noInput'] : '–' }}</td>
                <td>@if ($oth)<span class="badge-alt">+{{ $oth }} alternate{{ $oth > 1 ? 's' : '' }}</span>@endif</td>
                <td class="gr-noclick">
                  @if ($admin)
                  <div class="gr-more"><a href="#" class="gr-more-btn" aria-label="More actions">&#8943;</a>
                    <ul>
                      <li><a href="#" data-act="replace" data-target="{{ $r['key'] }}">Replace</a></li>
                      <li><a href="#" data-act="restore" data-target="{{ $r['key'] }}">Restore default</a></li>
                      @if ($r['deletable'])<li><a href="#" data-act="delete" data-target="{{ $r['key'] }}">Delete</a></li>@endif
                    </ul>
                  </div>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
<div id="gr-shade"></div>
<div id="gr-panel" aria-hidden="true"></div>
<div id="gr-bar"><button type="button" id="gr-bp" aria-label="Play">&#9654;</button><button type="button" id="gr-bs" aria-label="Stop">&#9632;</button><span class="nm" id="gr-bn"></span><span id="gr-bt">0:00</span><input type="range" class="seek" id="gr-bk" min="0" max="1000" value="0"><span id="gr-br">-0:00</span>&#128266;<input type="range" id="gr-bv" min="0" max="100" value="100" style="width:80px"><a id="gr-bd" href="#" download="greeting.wav" aria-label="Download">&#11015;</a></div>
<input type="file" id="gr-file" accept="audio/*" style="display:none">
<script>window.GVD_GREET = {!! json_encode(['rows' => $rows, 'admin' => $admin, 'holidays' => $holidays], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};</script>
</div>
@endsection
@push('scripts')
<script src="/js/gvd-greetings.js?v=4"></script>
@endpush
