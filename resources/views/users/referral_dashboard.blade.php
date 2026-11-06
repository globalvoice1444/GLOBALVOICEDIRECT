@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">

<style type="text/css">
  select.form-control.yeardropdown {
      padding-left: 5px;
      padding-right: 0px;
  }
  .phoneBillLabel a {
      font-size: 14px;
      font-weight: 800;
      color: #4fc3f7;
  }
  span.phoneBillLabel {
      margin-left: 15px;
  }
  .row.creditcardinfo {
      margin-bottom: 20px;
  }
</style>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         @if (session()->has('success'))
            <div class="alert close_alert alert-success">
              <strong>Info!</strong> {!! session()->get('success') !!}
            </div>
         @elseif (session()->has('error'))
            <div class="alert close_alert alert-danger">
              <strong>Info!</strong> {!! session()->get('error') !!}
            </div>
         @endif
         <div class="card-block" id="profile_card">
            <div class="row">
               <div class="col-md-12">
                  <div id="wizard">
                     <section>
                        <div class="container">
                           <ul class="nav nav-pills">
                              <li><a class="active show" data-toggle="pill" href="#menu1">Finance / credit line information</a></li>
                           </ul>
                           <div class="tab-content">
                              <div id="menu1" class="tab-pane fade in active show">
                                 <div class="row">   
                                    <div class="col-lg-12 col-md-12 col-sm-12" id="tab_body">
                                       <h3>Finance / credit line information </h3>
                                       <hr>
                                       <div class="row form-group">
                                             
                                          <div class="col-lg-2 space-right">
                                             <label>User Nice Name</label>
                                          </div>
                                          <div class="col-lg-2 clearfix text-left">
                                             <p>{{$refferal->user_nicename}}</p>
                                          </div>
                                          <div class="col-lg-2 space-right">
                                             <label>User Email</label>
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <p>{{$refferal->user_email}}</p>
                                          </div>
                                       </div>
                                    </div>
                                    @if(isset($user_orders_amount) && $user_orders_amount >= 250 && $refferal->bonus_status == 1)
                                       <div class="alert alert-success mt-3" role="alert">
                                          <h4 class="alert-heading">Well done!</h4>
                                          <p>Aww yeah, you successfully read this important alert message.<br> You have got the Bonus by purchasing the items from globalvoicedirect.com of $250. This Bonus may use for Pay-Bill or you can request to withdraw this Bonus.</p>
                                          <hr>
                                          <p class="mb-0">Welcome again for next time!
                                             <form method="post" action="/user/withdraw-reward">
                                                @csrf
                                                <input type="hidden" name="referral_id" value="{{$refferal->ID}}">
                                                <input type="hidden" name="request_type" value="pay_bill">
                                                <input type="hidden" name="reward_status" value="bonus">
                                                <button type="submit" class="btn btn-success float-right" style="margin-left: 2px;">Pay-Bill</button>
                                             </form>
                                             <form method="post" action="/user/withdraw-reward">
                                                @csrf
                                                <input type="hidden" name="referral_id" value="{{$refferal->ID}}">
                                                <input type="hidden" name="request_type" value="transfer">
                                                <input type="hidden" name="reward_status" value="bonus">
                                                <button type="submit" class="btn btn-info float-right">Withdraw</button>
                                             </form>
                                          </p>
                                       </div>
                                    @endif
                                    <div class="tab-content">
                                       <div id="menu1" class="tab-pane fade in active show">
                                          <div class="contacts-table">
                                             <table class="table table-hover table-responsive" id="contact_table">
                                                <thead>
                                                   <tr>
                                                      <th scope="col">#</th>
                                                      <th scope="col">User Name</th>
                                                      <th scope="col">Referral Bonus</th>
                                                      <th scope="col">User Email</th>
                                                      <th scope="col">Registered at</th>
                                                      <th scope="col">Action</th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                   @if(isset($referred_users))
                                                      <?
                                                         if(count($referred_users) > 0)  
                                                            $serial = 1;
                                                      ?>
                                                      @foreach($referred_users as $users)
                                                         <tr>
                                                            <td>{{$serial}}</td>
                                                            <td>{{$users->user_nicename}}</td>
                                                            <td>${{isset($users->reward)? $users->reward:0}}</td>
                                                            <td>{{$users->user_email}}</td>
                                                            <td>{{$users->created_at}}</td>
                                                            <td>
                                                               @if($users->totalshopping < 250 && $users->reward_status == 0)
                                                                  <button class="btn btn-primary">Not Eligible</button>
                                                               @elseif($users->totalshopping >= 250 && $users->reward_status == 1)
                                                                  <div class="form-inline">
                                                                     <form method="post" action="/user/withdraw-reward">
                                                                        @csrf
                                                                        <input type="hidden" name="referral_id" value="{{$users->referral_id}}">
                                                                        <input type="hidden" name="request_type" value="transfer">
                                                                        <input type="hidden" name="reward_status" value="reward">
                                                                        <button type="submit" class="btn btn-info">Withdraw</button>
                                                                     </form>
                                                                     <form method="post" action="/user/withdraw-reward">
                                                                        @csrf
                                                                        <input type="hidden" name="referral_id" value="{{$users->referral_id}}">
                                                                        <input type="hidden" name="request_type" value="pay_bill">
                                                                        <input type="hidden" name="reward_status" value="reward">
                                                                        <button type="submit" class="btn btn-success float-right" style="margin-left:2px;">Pay-Bill</button>
                                                                     </form>
                                                                  </div>
                                                               @elseif($users->totalshopping >=250 && $users->reward_status == 2)
                                                                  <button class="btn btn-default">UnderReview</button>
                                                               @elseif($users->totalshopping >=250 && $users->reward_status == 3)
                                                                  <button class="btn btn-danger">Withdrawl</button>
                                                               @endif
                                                            </td>
                                                         </tr>
                                                         <?
                                                            if($serial)
                                                               $serial = $serial + 1;
                                                         ?>
                                                      @endforeach 
                                                   @endif
                                                </tbody>
                                             </table>
                                          </div>   
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </section>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div> 
</div>
@endsection
@section('scripts')
   <script>
      $(document).ready(function(){
         $(".user-profile-photo").hover(function(){
            $('.edit-user-profile-photo').css("display", "block");
            $('.img-radius').css('opacity','0.6');
         }, function(){
            $('.edit-user-profile-photo').css("display", "none");
            $('.img-radius').css('opacity','1');
         });
      });
      var breadcrumb = $('.breadcrumb');
      breadcrumb.append('<li class="breadcrumb-item"><b><span>Referral Dashboard</span></b></li>');
      $('.main-menu ul').find('.active').removeClass('active');
      $('.main-menu').find('a[href="/user/profile"]').parents().addClass('active');
   </script>
@endsection
@push('scripts')

<script type="text/javascript">

   $(document).ready( function () {
      $('#contact_table').DataTable();
   });

</script>
@endpush