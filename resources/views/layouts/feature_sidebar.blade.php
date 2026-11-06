<div class="col-md-3">
    <nav id="sidebar">
<div class="">
	@if($_SERVER['REMOTE_ADDR'] == '103.255.5.106')
		
	@endif
<ul class="list-unstyled components mb-5">
<li  class="@if(Request::url() === url('/') . '/feature') {{'active'}} @endif" >
<a href="/feature" class="current" title="features"><span class="mr-3"><img  alt="All Features" src="{{asset('/images/feature-icon/25.png')}}" width="22px" height="20px"></span>All Features</a>
</li>
<li class="@if(Request::url() === url('/') . '/phone-number') {{'active'}} @endif">
<a href="/phone-number" title="Phone Numbers"><span class="mr-3"><img alt="Phone Numbers" src="{{asset('/images/feature-icon/26.png')}}" width="22px" height="20px"></span>Phone Numbers</a>
</li>
<li class="@if(Request::url() === url('/') . '/user-extension') {{'active'}} @endif">
<a href="user-extension" title="User Extensions"><span class="mr-3"><img alt="User Extensions" src="{{asset('/images/feature-icon/16.png')}}" width="23px" height="20px"></span>User Extensions</a>
</li>
<li class="@if(Request::url() === url('/') . '/call-handling') {{'active'}} @endif">
<a href="/call-handling" title="Call Handling"><span class="mr-3"><img  alt="Call Handling" src="{{asset('/images/feature-icon/15.png')}}" width="28px" height="20px"></span>Call Handling</a>
</li>
<li class="@if(Request::url() === url('/') . '/admin-feature') {{'active'}} @endif">
<a href="/admin-feature" title="Admin"><span class="mr-3"><img alt="Admin" src="{{asset('/images/feature-icon/21.png')}}" width="22px" height="20px"></span>Admin</a>
</li>

<li class="@if(Request::url() === url('/') . '/phone-feature') {{'active'}} @endif">
<a href="/phone-feature" title="Mobile"><span class="mr-3"><img alt="Admin" src="{{asset('/images/feature-icon/24.png')}}" width="22px" height="20px"></span>Mobile</a>
</li>
</ul></div>



</nav>
</div>