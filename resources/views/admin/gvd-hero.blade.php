@php
 $gvdH = (int) now('America/New_York')->format('G'); $gvdGreet = $gvdH < 12 ? 'Good morning' : ($gvdH < 18 ? 'Good afternoon' : 'Good evening');
 $gvdN = preg_replace('/[^A-Za-z ]/', '', (string) session('admin_name', '')); $gvdN = trim(explode(' ', trim($gvdN))[0] ?? '');
 $gvdN = ($gvdN === '' || in_array(strtolower($gvdN), ['admin','user'], true)) ? '' : \Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::lower($gvdN));
 $gvdQs = [['The way to get started is to quit talking and begin doing.','Walt Disney'],['Your most unhappy customers are your greatest source of learning.','Bill Gates'],['The best way to predict the future is to create it.','Peter Drucker'],['Quality is not an act, it is a habit.','Aristotle'],['Success is not final, failure is not fatal: it is the courage to continue that counts.','Winston Churchill'],["Opportunities don't happen. You create them.",'Chris Grosser'],["Don't be afraid to give up the good to go for the great.",'John D. Rockefeller'],["It always seems impossible until it's done.",'Nelson Mandela'],['Well done is better than well said.','Benjamin Franklin'],['The secret of getting ahead is getting started.','Mark Twain'],['Innovation distinguishes between a leader and a follower.','Steve Jobs'],["Great things in business are never done by one person. They're done by a team of people.",'Steve Jobs'],['Price is what you pay. Value is what you get.','Warren Buffett'],['Make every detail perfect and limit the number of details to perfect.','Jack Dorsey'],['Excellence is not a skill. It is an attitude.','Ralph Marston'],['The harder I work, the luckier I get.','Samuel Goldwyn'],["Customers may forget what you said, but they'll never forget how you made them feel.",'Maya Angelou'],['A goal without a plan is just a wish.','Antoine de Saint-Exupéry'],['Strive not to be a success, but rather to be of value.','Albert Einstein'],['Start where you are. Use what you have. Do what you can.','Arthur Ashe']];
 $gvdD = now('America/New_York'); $gvdQ = $gvdQs[((int) $gvdD->format('z') + 366 * (int) $gvdD->format('Y')) % count($gvdQs)];
@endphp
<section class="gvd-ah">
 <div class="eb">Admin Command Center &middot; {{ $gvdD->format('l, F j') }}</div>
 <h1>{{ $gvdGreet }}, <span>{{ $gvdN !== '' ? $gvdN : 'there' }}</span></h1>
 <p>Run orders, customers, requests and alerts for Global Voice Direct from one place.</p>
 <figure><span>Today's thought</span><blockquote>&ldquo;{{ $gvdQ[0] }}&rdquo;</blockquote><figcaption>&mdash; {{ $gvdQ[1] }}</figcaption></figure>
 <div class="qa"><a href="/admin/orders">Orders</a><a href="/admin/usermanagement">Customers</a><a href="/admin/scriptRecording">Script requests</a><a href="/admin/forms/listings">Business listings</a><a href="/admin/alerts">Alerts</a></div>
</section>
