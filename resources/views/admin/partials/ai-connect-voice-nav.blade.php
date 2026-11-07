@if(config('ai_connect.voice_provisioning_enabled'))
<li class="{{ request()->is('admin/ai-connect/voice-bots*') ? 'active' : '' }}">
    <a href="/admin/ai-connect/voice-bots" class="waves-effect waves-dark">
        <span class="pcoded-micon"><i class="fa fa-microphone"></i></span>
        <span class="pcoded-mtext">AI Connect &gt; Voice bots</span>
    </a>
</li>
@endif
