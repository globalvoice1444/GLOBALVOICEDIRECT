@extends('layouts.user.user_main')
@section('css')

<style type="text/css">

      .title {
        width: 100%;
        max-width: 854px;
        margin: 0 auto;
      }

      .caption {
        width: 100%;
        max-width: 854px;
        margin: 0 auto;
        padding: 20px 0;
      }

      .vid-main-wrapper {
        width: 100%;
        max-width: 1100px;
        background: #fff;
        margin: 0 auto;
      }


      /*  VIDEO PLAYER CONTAINER
    ############################### */
    .vid-container {
        position: relative;
   
        height: 0;
        width: 100%;
    }
     
    .vid-container iframe,
    .vid-container object,
    .vid-container embed {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        min-height: 360px;
    }


    /*  VIDEOS PLAYLIST 
    ############################### */
    .vid-list-container {
      height:360px;
      overflow: hidden;
    }

    .vid-list-container:hover, .vid-list-container:focus {
       overflow-y: auto;
     }
    #vid-list li {
      list-style: none;
      height: 124px;
    }
    .vid-thumb {
      float:left;
      margin-right: 8px;
    }

    .active-vid { 
      background:#3A3A3A;
    }

    #vid-list .desc {
      color: #9d9d9d;
      font-size: 12px;
      margin-top: 5px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .vid-container {
      position: relative;
      padding-top: 25px;
      padding-bottom: 51.5%
      height: 0;
    }
   
    .vid-container iframe {
        box-sizing: border-box;
        background-size: contain;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }
    .text-info{
      font-size: 14px;
    }
    #vid-list li a img {
      width: 168px;
      }
      .description {
         margin-top: 350px;
     }
    @media (min-width: 768px) and (max-width: 1024px){
      #vid-list li {
        height: 196px;
        text-align: center;
      }
      .text-info{
        margin-top: 20px;
        font-size: 11px;

      }
      #vid-list li a img {
       width: 100%;
        }
    }
    @media (max-width: 667px) {
      body {
        margin: 15px;
      }
      .caption {
        margin-top: 40px;
      }
      .vid-container {
        padding-bottom: 90.5%;
      }
      .vid-list-container {
        padding-bottom: 20px;
        min-width: 0px;
        overflow: scroll;
      }
      .description {
        margin-top: 176px;
        text-align: center;
      }
      #vid-list li a img {
          width: 100%;
      }
      .card .card-block p{
        font-size: 12px !important;
      }
      .title h6 {
         font-size: 11px;
         margin-top: 10px;
      }
      #vid-list li {
         list-style: none;
          height: 230px;
        text-align: center;
      }
      .text-info{
        margin-top: 20px;
      }
    }
</style>
@endsection
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">



<h4>How To Video</h4>
<div class="col-sm-12">
   <div class="card">
      <div class="card-header">
      </div>
      <div class="card-block">
         <div class="row">
            <div class="col-md-12">
               <div id="wizard">
                  <section>
                    <div class="vid-main-wrapper clearfix">
                      <div class="row">
                        <div class="col-md-8 col-sm-12">
                          <div class="vid-container">
                          
                          <!-- THE YOUTUBE PLAYER -->
                         
                            <iframe id="vid_frame" width="525" height="350" src="https://www.youtube.com/embed/PNCq8zEjeME" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                          </div>
                          <div class="description">
                             <div class="title"><h6 class="text-info">HOW TO SEND YOUR BUSINESS CREDIT APPLICATION</h6></div>
                             <p class="desc">In This video we will walk you through the process of sending your business credit application to our underwriting department. It is the most important step to insure your business information reported correctly.</p>
                          </div>
                         
                        </div>
                        <div class="col-md-4 col-sm-12">
                          <!-- THE PLAYLIST -->
                          <div class="vid-list-container">
                              <ul id="vid-list">
                                <li>
                                  <a href="javascript:void();" onClick="setVideo(src[0],vidTitle[0],description[0])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail11.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO SEND YOUR BUSINESS CREDIT APPLICATION</h6></div>
                                    <p class="desc">In This video we will walk you through the process of sending your business credit application to our underwriting department. It is the most important step to insure your business information reported correctly.</p>
                                  </a>
                                </li>
                                <hr>
                                 <li>
                                  <a href="javascript:void();" onClick="setVideo(src[1],vidTitle[1],description[1])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail1.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO PROVISION THE MOBILE APP</h6></div>
                                    <p class="desc">Sound more professional & stay connected with Global Voice Direct. In this Video We will guide you how to provision the Mobyx application. Make and receive calls. Sound more professional & stay connected. Whether you have one or multiple office location, this app was designed with you in mind. With unlimited features; music on hold, call waiting, caller id, call screening, call recorded, automated system, call conference and many more.

                                    Perfect choice for the small or mid-sized business. We have all the cutting-edge tech that will transform the way you do business.</p>
                                  </a>
                                </li>
                                <hr>
                                 <li>
                                  <a href="javascript:void();" onClick="setVideo(src[2],vidTitle[2],description[2])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail2.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO ACTIVATE / START YOUR FAX SERVICE</h6></div>
                                    <p class="desc">Start Faxing Immediately: Sign up is quick and as easy as 1, 2, 3. In this Video We will guide you how to setup your Fax service. No Fax Machine Needed. Fax From Anywhere On Any Device. Start Today! Free Service. No obligation. No contract. PART OF YOUR MEMBERSHIP PACKAGE</p>
                                  </a>
                                </li>
                                <hr>
                                 <li>
                                  <a href="javascript:void();" onClick="setVideo(src[3],vidTitle[3],description[3])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail3.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO USE THE SMS CAMPAIGN PLATFORM</h6></div>
                                    <p class="desc">In This video we will walk you through the process of sending your business credit application to our underwriting department. It is the most important step to insure your business information reported correctly.</p>
                                  </a>
                                </li>
                                <hr>
                                 <li>
                                  <a href="javascript:void();" onClick="setVideo(src[4],vidTitle[4],description[4])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail4.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO SEND YOUR BUSINESS CREDIT APPLICATION</h6></div>
                                    <p class="desc">In this video you will how to send promotions, reminders & notifications to your clients. Send your first message in minutes. You can easily upload your own list. What kind of message will work best for your audience? Will it be a coupon, an event notification, or a reminder? Texting works for any kind of message that you want opened fast!. enables you to access your online account from any internet connected Web browser. Create, manage and send your two-way SMS communications to predetermined distribution groups or individuals.</p>
                                  </a>
                                </li>
                                <hr>
                                <li>
                                  <a href="javascript:void();" onClick="setVideo(src[5],vidTitle[5],description[5])">
                                    <span class="vid-thumb"><img width=174 src="/public/users/images/thumbnail5.png" /></span>
                                    <div class="title"><h6 class="text-info">HOW TO SEND YOUR AUTO ATTENDANT GET FREE PROFESSIONAL VOICE RECORDING</h6></div>
                                    <p class="desc">Create a Professional Automated Attendant Script.<br><br>our professional studio will gladly record your phone script/ greetings free of charge PART OF YOUR MEMBERSHIP PACKAGE . Customize recorded greetings so that callers can press 1 for sales, press 2 for support, dial by name or company directory. Your business can have the same type features used by big companies, without the big cost. Our studio is eager to record your professional phone script. CLICK HERE TO DOWNLOAD https://globalvoicedirect.com/script-samples/. our support team would be happy to help studio@globalvoicedirect.com</p>
                                  </a>
                                </li>
                              </ul>
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
@endsection
@push('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    $('.vid-item').each(function(index){
      $(this).on('click', function(){
        var current_index = index+1;
        $('.vid-item .thumb').removeClass('active');
        $('.vid-item:nth-child('+current_index+') .thumb').addClass('active');
      });
    });
  });

  var src = ['https://www.youtube.com/embed/PNCq8zEjeME?autoplay=1&rel=0&showinfo=1&autohide=1', 
              'https://www.youtube.com/embed/09zz9mjywDw?autoplay=1&rel=0&showinfo=1&autohide=1',
              'https://www.youtube.com/embed/r6sN-L-8hm0?autoplay=1&rel=0&showinfo=0&autohide=1',
              'https://www.youtube.com/embed/qYaCfPpsyUk?autoplay=1&rel=0&showinfo=0&autohide=1',
              'https://www.youtube.com/embed/PNCq8zEjeME?autoplay=1&rel=0&showinfo=0&autohide=1',
              'https://www.youtube.com/embed/PNCq8zEjeME?autoplay=1&rel=0&showinfo=0&autohide=1'];
  var vidTitle = ['HOW TO SEND YOUR BUSINESS CREDIT APPLICATION',
                'HOW TO PROVISION THE MOBILE APP',
                'HOW TO ACTIVATE / START YOUR FAX SERVICE',
                'HOW TO USE THE SMS CAMPAIGN PLATFORM',
                'HOW TO SEND YOUR BUSINESS CREDIT APPLICATION',
                'HOW TO SEND YOUR AUTO ATTENDANT GET FREE PROFESSIONAL VOICE RECORDING'
              ];
  var description = ['In This video we will walk you through the process of sending your business credit application to our underwriting department. It is the most important step to insure your business information reported correctly.',
      'Sound more professional & stay connected with Global Voice Direct. In this Video We will guide you how to provision the Mobyx application. Make and receive calls. Sound more professional & stay connected. Whether you have one or multiple office location, this app was designed with you in mind. With unlimited features; music on hold, call waiting, caller id, call screening, call recorded, automated system, call conference and many more.<br>Perfect choice for the small or mid-sized business. We have all the cutting-edge tech that will transform the way you do business.',
      'Start Faxing Immediately: Sign up is quick and as easy as 1, 2, 3. In this Video We will guide you how to setup your Fax service. No Fax Machine Needed. Fax From Anywhere On Any Device. Start Today! Free Service. No obligation. No contract. PART OF YOUR MEMBERSHIP PACKAGE',
      'Start Faxing Immediately: Sign up is quick and as easy as 1, 2, 3. In this Video We will guide you how to setup your Fax service. No Fax Machine Needed. Fax From Anywhere On Any Device. Start Today! Free Service. No obligation. No contract. PART OF YOUR MEMBERSHIP PACKAGE',
      'In This video we will walk you through the process of sending your business credit application to our underwriting department. It is the most important step to insure your business information reported correctly.',
      'In this video you will how to send promotions, reminders & notifications to your clients. Send your first message in minutes. You can easily upload your own list. What kind of message will work best for your audience? Will it be a coupon, an event notification, or a reminder? Texting works for any kind of message that you want opened fast!. enables you to access your online account from any internet connected Web browser. Create, manage and send your two-way SMS communications to predetermined distribution groups or individuals.',
      'Create a Professional Automated Attendant Script.<br><br>our professional studio will gladly record your phone script/ greetings free of charge PART OF YOUR MEMBERSHIP PACKAGE . Customize recorded greetings so that callers can press 1 for sales, press 2 for support, dial by name or company directory. Your business can have the same type features used by big companies, without the big cost. Our studio is eager to record your professional phone script. CLICK HERE TO DOWNLOAD https://globalvoicedirect.com/script-samples/. our support team would be happy to help studio@globalvoicedirect.com'

      ];
    function setVideo(srcLink, vidTitle, desc){
      $('#vid_frame').attr('src',srcLink);
      $('.description .title .text-info').text(vidTitle);
      $('.description').find('p').text(desc);
  }
</script>

<script type="text/javascript">
    /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');
    var thisLink = '/user/howtovideo';
    breadcrumb.text('How to Video');
    breadcrumb.prop("href", thisLink);
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="'+thisLink+'"]').parents().addClass('active');*/
     var breadcrumb = $('.breadcrumb');
      breadcrumb.append('<li class="breadcrumb-item"><b><a href="/user/howtovideo"><span>How to Video</span></a></b></li>');
      $('.main-menu ul').find('.active').removeClass('active');
      $('.main-menu').find('a[href="/user/howtovideo"]').parents().addClass('active');
</script>
@endpush
