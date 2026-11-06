{{-- Fax dashboard header, stats, tabs and inline compose (presentation 2026-11-01). Lists still load through gvd-vo.js. --}}
<style>
  .fd-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;margin-bottom:16px;flex-wrap:wrap}
  .fd-head h1{font-size:22px;margin:0 0 4px;font-weight:650}
  .fd-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
  @media (max-width:900px){.fd-stats{grid-template-columns:repeat(2,1fr)}}
  .fd-stat{background:#fff;border:1px solid var(--gp-line);border-radius:10px;padding:14px 16px}
  .fd-stat .l{color:var(--gp-muted);font-size:12px}.fd-stat .v{font-size:26px;font-weight:650;margin-top:2px}.fd-stat .h{color:var(--gp-muted);font-size:11.5px}
  .fd-grid{display:grid;grid-template-columns:340px 1fr;gap:16px;align-items:start}
  .fd-grid.noc{grid-template-columns:1fr}
  @media (max-width:1000px){.fd-grid{grid-template-columns:1fr}}
  .fd-card{background:#fff;border:1px solid var(--gp-line);border-radius:10px;padding:16px;min-width:0}
  .fd-card h3{font-size:14px;font-weight:600;margin:0 0 12px}
  .fd-f{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}.fd-f>span{font-size:12.5px;color:var(--gp-muted);font-weight:500}
  .gp-content .fd-f input[type=text]{width:100%}
  .fd-drop{border:1.5px dashed #cbd5e1;border-radius:10px;padding:14px;text-align:center;color:var(--gp-muted);font-size:13px;background:#fafbff}
  .fd-drop.over{border-color:var(--gp-brand);background:var(--gp-brand-50)}
  .fd-drop .fn{color:var(--gp-text);font-weight:500;margin-top:6px;word-break:break-all}
  .fd-btns{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-top:8px}
  .fd-thumbs{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:10px}
  .fd-th{position:relative;border:1px solid var(--gp-line);border-radius:8px;overflow:hidden;background:#f8fafc}
  .fd-th img{width:100%;height:110px;object-fit:cover;display:block}
  .fd-th .n{position:absolute;left:6px;top:6px;background:#0f172acc;color:#fff;font-size:11px;border-radius:99px;padding:1px 7px}
  .fd-th .a{display:flex;justify-content:space-between;padding:3px}
  .fd-th .a button{border:0;background:#fff;border-radius:6px;padding:2px 7px;cursor:pointer;font-size:13px}
  .fd-th .a button:disabled{opacity:.3}
  .gp-content .fd-send{width:100%;padding:10px!important}
  .fd-msg{font-size:12.5px;margin-top:8px;min-height:16px;color:var(--gp-muted)}
  .fd-msg.err{color:var(--gp-red)}.fd-msg.ok{color:#166534}
  .fd-tabs{display:flex;gap:4px;border-bottom:1px solid var(--gp-line);margin:-4px 0 12px}
  .gp-content .fd-tabs a{padding:8px 14px;color:var(--gp-muted);border-bottom:2px solid transparent;margin-bottom:-1px;text-decoration:none}
  .gp-content .fd-tabs a.on{color:var(--gp-brand);border-bottom-color:var(--gp-brand);font-weight:500}
  .fd-cam{display:none}
  @media (pointer:coarse),(max-width:760px){.fd-cam{display:inline-flex}}
</style>
<div class="fd-head">
  <div><h1>Fax</h1><div class="muted">Send and receive faxes on your Global Voice Direct number. Copies are kept for {{ (int) config('fax.retention_days', 90) }} days.</div></div>
  <button type="button" class="btn btn-default btn-sm" id="fx-refresh"><i class="feather icon-refresh-cw"></i> Refresh</button>
</div>
<div class="fd-stats">
  <div class="fd-stat"><div class="l">Received</div><div class="v" id="fd-in">–</div><div class="h">in your inbox</div></div>
  <div class="fd-stat"><div class="l">Sent</div><div class="v" id="fd-out">–</div><div class="h">last 30 days</div></div>
  <div class="fd-stat"><div class="l">Last 7 days</div><div class="v" id="fd-7">–</div><div class="h">received</div></div>
  <div class="fd-stat"><div class="l">Pages</div><div class="v" id="fd-pg">–</div><div class="h">received</div></div>
</div>
