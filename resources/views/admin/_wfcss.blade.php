<style>
.gwf{--n:#1e2a38;--o:#f26b1d;--l:#e5e7eb;--m:#64748b;font-size:13px}
@keyframes gwfPop{from{opacity:0;transform:translateY(8px) scale(.98)}to{opacity:1;transform:none}}
@keyframes gwfSlide{from{transform:translateX(40px);opacity:0}to{transform:none;opacity:1}}
.gwf .pop{animation:gwfPop .45s cubic-bezier(.2,.8,.2,1) both}
.gwf .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px}
.gwf .crumb{color:var(--m)}.gwf .crumb b{color:var(--o)}.gwf h1{font-size:22px;margin:4px 0 0;color:var(--n);font-weight:700}
.gwf .nav{display:flex;gap:6px;background:#fff;padding:5px;border-radius:12px;box-shadow:0 1px 4px #0001}
.gwf .nav a{padding:8px 16px;border-radius:9px;font-weight:600;color:#475569;text-decoration:none}.gwf .nav a.on{background:var(--n);color:#fff}
.gwf .b{background:var(--o);color:#fff;border:0;border-radius:8px;padding:8px 16px;font-weight:700;cursor:pointer;box-shadow:0 2px 6px #f26b1d44;text-decoration:none;display:inline-block}
.gwf .b.g{background:#fff;color:var(--n);border:1px solid #cbd5e1;box-shadow:none}.gwf .b.sm{padding:5px 11px;font-size:12px}
.gwf .panel{background:#fff;border-radius:14px;box-shadow:0 2px 10px #0000000d;margin-bottom:18px;overflow:hidden}
.gwf .ph{background:var(--n);color:#fff;font-weight:700;letter-spacing:.4px;padding:13px 18px;border-bottom:3px solid var(--o);display:flex;justify-content:space-between;align-items:center}
.gwf .pb{padding:16px 18px}.gwf .kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}@media(max-width:900px){.gwf .kpis{grid-template-columns:1fr 1fr}}
.gwf .kpi{background:#fff;border-radius:14px;padding:16px;box-shadow:0 2px 10px #0000000d;border-top:3px solid var(--o)}.gwf .kpi .l{color:var(--m);font-size:11px;text-transform:uppercase;letter-spacing:.6px}.gwf .kpi .v{font-size:26px;font-weight:800;color:var(--n);margin-top:6px}.gwf .kpi .s{color:#9ca3af;font-size:11px}
.gwf table{border-collapse:collapse;width:100%}.gwf td,.gwf th{border-bottom:1px solid #eef0f3;padding:11px 10px;text-align:left}.gwf th{color:var(--m);font-size:11px;text-transform:uppercase;letter-spacing:.5px}
.gwf tr:hover td{background:#fafbfc}.gwf .st{padding:3px 10px;border-radius:12px;font-weight:600;font-size:11px}.gwf .st.PAUSED{background:#fff4ec;color:#c2410c}.gwf .st.ACTIVE{background:#dcfce7;color:#166534}
.gwf .tg{display:inline-block;background:#f1f5f9;border-radius:6px;padding:2px 8px;margin-right:4px;font-size:11px;color:#334155}
.gwf .tpls{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
.gwf .tpl{background:#fff;border-radius:14px;box-shadow:0 2px 10px #0000000d;padding:18px;border:1px solid #eef0f3;transition:.2s}.gwf .tpl:hover{transform:translateY(-3px);box-shadow:0 10px 24px #0000001a;border-color:var(--o)}
.gwf .tpl h3{margin:10px 0 6px;font-size:15px;color:var(--n)}.gwf .tpl p{color:var(--m);margin:0 0 12px;line-height:1.5}
.gwf .ic{width:24px;height:24px;border-radius:7px;display:inline-grid;place-items:center;color:#fff;font-size:12px;flex:none}.gwf .ic.t{background:var(--o)}.gwf .ic.a{background:var(--n)}.gwf .ic.lg{background:#0f766e}.gwf .ic.x{background:#94a3b8}
.gwf .empty{color:#94a3b8;text-align:center;padding:30px;border:1px dashed #d7dde5;border-radius:12px}
.gwf .sw{padding:10px 14px;border-radius:12px;margin-bottom:14px;background:#FFF7E6;border:1px solid #F6D58E}.gwf .sw.ok{background:#EAF7F0;border-color:#A8DFC0}
.gwf .hint{color:var(--m);font-size:11.5px;margin-top:4px}
</style>
