<style>
  .gvd-vo .muted{color:#888;font-size:13px}
  .gvd-vo .ro{background:#fff8e1;border:1px solid #ffe08a;border-radius:6px;padding:8px 12px;margin-bottom:12px;font-size:13px}
  .gvd-vo tr.vo-row{cursor:pointer}
  .gvd-vo tr.vo-row:hover{background:#fafafa}
  .gvd-vo .st{border-radius:10px;padding:2px 8px;font-size:12px;background:#f1f1f1;color:#555;white-space:nowrap}
  .gvd-vo .st.on{background:#e6f4ea;color:#1e7e34}
  .gvd-vo form.flt{display:flex;flex-wrap:wrap;gap:8px;align-items:flex-end;margin-bottom:12px}
  .gvd-vo form.flt label{font-size:12px;color:#666;display:block}
  .gvd-vo form.flt input,.gvd-vo form.flt select{padding:5px 8px;border:1px solid #ccc;border-radius:4px}
  .gvd-vo h6.grp{margin:18px 0 6px;font-weight:700}
  .gvd-vo .cb-card{border:1px solid #e4e4e4;border-radius:3px;margin-bottom:16px}
  .gvd-vo .cb-card h6{background:#0b8fd8;color:#fff;margin:0;padding:10px 14px;display:flex;justify-content:space-between;align-items:center}
  .gvd-vo .cb-card ul{list-style:none;margin:0;padding:8px 14px}
  .gvd-vo .cb-card li{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f2f2f2}
  #vo-panel{position:fixed;top:0;right:-500px;width:480px;max-width:95vw;height:100%;background:#fff;box-shadow:-4px 0 16px rgba(0,0,0,.15);z-index:1050;transition:right .25s;overflow-y:auto;padding:18px}
  #vo-panel.open{right:0}
  #vo-shade{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:1040}
  #vo-shade.open{display:block}
  #vo-panel label{display:block;font-size:12px;color:#666;margin:12px 0 4px}
  #vo-panel input[type=text],#vo-panel input[type=email],#vo-panel input[type=number],#vo-panel input[type=password],#vo-panel select{width:100%;padding:6px 8px;border:1px solid #ccc;border-radius:4px}
  #vo-panel .tabs a{display:inline-block;padding:6px 10px;margin-right:4px;border-bottom:2px solid transparent;color:#333}
  #vo-panel .tabs a.on{border-color:#1a73e8;color:#1a73e8}
  #vo-panel .chk{display:flex;gap:8px;align-items:center;margin:6px 0;font-size:14px;color:#333}
  .vo-spin{display:inline-block;width:14px;height:14px;border:2px solid #ccc;border-top-color:#1a73e8;border-radius:50%;animation:vospin .8s linear infinite;vertical-align:middle;margin-right:6px}
  @keyframes vospin{to{transform:rotate(360deg)}}
  .gvd-vo-legacy .card{border-radius:4px;box-shadow:0 1px 2px rgba(0,0,0,.06)}
  .gvd-vo-legacy .card-header h5{font-weight:600}
  .gvd-vo-legacy table.table{background:#fff}
</style>

@include('users.partials.lx-hero')
