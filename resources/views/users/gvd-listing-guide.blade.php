@php use App\Support\BusinessListings as BL; $addr = $r->street . ($r->suite ? ', ' . $r->suite : '') . ', ' . $r->city . ', ' . $r->state . ' ' . $r->zip; $q = rawurlencode($r->business_name . ' ' . $r->city); @endphp
<!doctype html><html><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Local listings guide – {{ $r->business_name }}</title>
<style>body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;max-width:820px;margin:24px auto;padding:0 16px;font-size:16px;line-height:1.5;color:#1f2937}h1{font-size:24px;margin:0 0 4px}.sub{color:#6b7280;margin:0 0 16px}.btn{background:#4f46e5;color:#fff;border:0;border-radius:6px;padding:10px 18px;font:inherit;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block}.tip{background:#fff8e1;border-left:4px solid #f9a825;padding:12px 16px;margin:16px 0}.nap{width:100%;border-collapse:collapse}.nap th,.nap td{border:1px solid #e4e7ec;padding:8px;text-align:left;vertical-align:top}.nap th{width:30%;background:#f8f9fb}@media print{.noprint{display:none}a{color:inherit}}</style></head><body>
<h1>Your local listings guide</h1><p class="sub">Prepared by Global Voice Direct for {{ $r->business_name }}</p>
<p class="noprint"><button class="btn" type="button" onclick="window.print()">Print / save as PDF</button></p>
<h2>Your listing details (use these exactly, everywhere)</h2>
<table class="nap">
 <tr><th>Business name</th><td>{{ $r->business_name }}</td></tr>
 <tr><th>Address</th><td>{{ $addr }}</td></tr>
 <tr><th>Phone</th><td>{{ BL::prettyPhone($r->phone) }}</td></tr>
 @if($r->website)<tr><th>Website</th><td>{{ $r->website }}</td></tr>@endif
 <tr><th>Categories</th><td>{{ implode(', ', BL::cats($r)) }}</td></tr>
 <tr><th>Hours</th><td>@foreach(BL::hoursText($r) as $d => $t){{ $d }}: {{ $t }}<br>@endforeach</td></tr>
</table>
<h3>NAP consistency checklist</h3>
<ul>
 <li>Use the exact business name above: no extra keywords or city names added.</li>
 <li>Write the address the same way on every site (same abbreviations, same suite format).</li>
 <li>Use the same main phone number everywhere: {{ BL::prettyPhone($r->phone) }}.</li>
 <li>Use the same website URL (same https and www choice).</li>
 <li>Keep hours identical, and update every listing when they change.</li>
</ul>
<div class="tip"><strong>First, search for existing listings.</strong> Duplicates hurt your ranking. Before creating anything, search <a href="https://www.google.com/maps/search/{{ $q }}" target="_blank" rel="noopener">Google Maps</a>, Apple Maps, and <a href="https://www.facebook.com/search/pages/?q={{ $q }}" target="_blank" rel="noopener">Facebook</a> for your business name and phone. If one exists, <em>claim</em> it instead of creating a new one.</div>
<h3>1. Google Business Profile</h3>
<ol>
 <li>Go to <a href="https://business.google.com/create" target="_blank" rel="noopener">business.google.com/create</a> and sign in with the Google account you want to own the listing.</li>
 <li>Search for "{{ $r->business_name }}". If it appears, choose it and request access or claim it.</li>
 <li>Otherwise, enter the name, choose "{{ $r->category }}" as the primary category, and add the address, phone and website exactly as shown above.</li>
 <li>Verify ownership as Google asks (phone, text, email, or a short video). Your listing goes public after verification.</li>
 <li>Add your hours, a description, a logo and photos.</li>
</ol>
<h3>2. Apple Business Connect (Apple Maps)</h3>
<ol>
 <li>Go to <a href="https://businessconnect.apple.com" target="_blank" rel="noopener">businessconnect.apple.com</a> and sign in with an Apple Account.</li>
 <li>Choose to add a location, search for your business, and claim it if it's already on Apple Maps.</li>
 <li>Enter the same name, address, phone, website, category and hours.</li>
 <li>Complete Apple's verification (usually a phone call or documents).</li>
</ol>
<h3>3. Facebook Page</h3>
<ol>
 <li>Go to <a href="https://www.facebook.com/pages/create" target="_blank" rel="noopener">facebook.com/pages/create</a> while logged in to your personal Facebook account.</li>
 <li>Use "{{ $r->business_name }}" as the Page name and "{{ $r->category }}" as the category.</li>
 <li>In the Page's contact and location settings, enter the address, phone, website and hours exactly as above.</li>
 <li>Add a profile picture, cover photo and your description.</li>
</ol>
<h3>4. Bing Places (we handle this)</h3>
<p>We submit your listing to Bing Places for you. Watch for a verification code from Bing by phone, text or email, and enter it when you receive it.</p>
</body></html>
