  <div class="fd-card" id="fd-compose">
    <h3>Send a fax</h3>
    @if (!$fdAdmin)
      <div class="ro" style="margin:0">Only the company administrator can send faxes.</div>
    @else
    <label class="fd-f"><span>To (fax number)</span><input type="text" id="fd-to" autocomplete="off" inputmode="tel" placeholder="(555) 123-4567"></label>
    <div class="fd-f"><span>Document</span>
      <div class="fd-drop" id="fd-drop">
        <i class="feather icon-upload-cloud" style="font-size:20px"></i><div>PDF, TIFF or JPG · drag a file here</div>
        <div class="fd-btns">
          <label class="btn btn-default btn-sm" style="margin:0;cursor:pointer"><i class="feather icon-paperclip"></i> Choose file<input type="file" id="fd-file" accept=".pdf,.tif,.tiff,.jpg,.jpeg,application/pdf,image/tiff,image/jpeg" style="display:none"></label>
          <label class="btn btn-default btn-sm fd-cam" style="margin:0;cursor:pointer"><i class="feather icon-camera"></i> Take a photo<input type="file" id="fd-cam" accept="image/*" capture="environment" multiple style="display:none"></label>
          <label class="btn btn-default btn-sm" style="margin:0;cursor:pointer" title="Add photos of pages; they are combined into one PDF"><i class="feather icon-image"></i> Add photos<input type="file" id="fd-photos" accept="image/*" multiple style="display:none"></label>
        </div>
        <div class="fn" id="fd-fname"></div>
      </div>
      <div class="fd-thumbs" id="fd-thumbs"></div>
    </div>
    <label class="chk" style="display:flex;gap:8px;align-items:center;font-size:13px;color:#334155;margin:0 0 12px"><input type="checkbox" id="fd-save" checked> Save file in fax history</label>
    <button type="button" class="btn btn-primary fd-send" id="fd-go"><i class="feather icon-send"></i> Send fax</button>
    <div class="fd-msg" id="fd-msg"></div>
    @endif
  </div>
