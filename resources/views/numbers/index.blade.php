@extends('layouts.app')
@section('css')
<!-- GTM (funnel tracking, added 2026-11-06) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KDJWX9X9');</script>
    <style type="text/css">
        div#selectpackage {
            width: 47%;
            height: calc(80vh - 140px);
            background: #fff;
            margin: auto;
        }

        .modal-body div {
            margin-top: 10px;
        }

        .alert.alert-info {
            position: relative;
            width: 75%;
            margin: auto;
            display: inline-block;
            font-family: 'Rubik', sans-serif;
        }

        .alert-info:after {
            position: absolute;
            content: "";
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 15px solid #d9edf7;
            border-left: 15px solid transparent;
            bottom: calc(50% - 9px);
            left: -30px;
            right: auto;
        }

        div#uniqueId2 {
            text-align: center;
        }

        a.btn.btn-primary {
            font-size: 18px;
        }

        .guide-image {

            display: inline-block;
            width: 75px;
            height: 75px;
            margin-right: 15px;
            border-radius: 50%;
            border: 2px solid #19beec;
        }

        .centering__centering.centering__relative.c1386 {
            margin: 80px 0;
        }

        .number-info {
            width: 40%;
            margin: auto;
        }

        .membership-section-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .img-section-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .imgfluid {
            width: 30%;
        }

        .panel-heading.contact-panel-heading {
            text-align: center;
            color: #222;
            text-transform: uppercase;
            font-size: 1.6em;
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
            margin-bottom: 0px;
        }

        .panel-body p {
            line-height: 24px;
            text-align: center;
            margin: 20px 0;
            color: #111;
            font-family: "Rubik", sans-serif;
            font-size: 14px;
        }

        .heading {
            text-align: center;
            color: #222;
            text-transform: uppercase;
            font-size: 1.6em;
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .input-group {
            padding-left: 40px;
            padding-right: 40px;
        }

        .form-control {
            height: 60px;
            border-radius: 6px 0 0 6px;
            border-style: none;
            -webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1)
        }

        .btn-default {
            padding: 17px 25px 17px 25px;
            background: #19beec;
            color: #fff;
            border-radius: 0 6px 6px 0;
            -webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1)
        }

        .btn-default:hover {
            padding: 17px 25px 17px 25px;
            background: #071425;
            color: #fff;
            border-radius: 0 6px 6px 0;
            -webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1)
        }

        .heading2 {
            text-align: center;
            color: #222;
            text-transform: uppercase;
            font-size: 1.6em;
            font-family: 'Rubik', sans-serif;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .form-section-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .c2007 {
            border: 0px;
            font-style: inherit;
            font-variant: inherit;
            font-weight: 500;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 18px;
            margin: 0px;
            vertical-align: baseline;
            color: rgb(102, 102, 102);
            letter-spacing: -0.01667em;
            text-align: center;
        }

        .c2027 {
            border: 1px solid var(--selectButtonColor);
            font-style: inherit;
            font-variant: inherit;
            font-weight: 600;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 15px;
            margin: 0px 10px 10px 0px;
            padding: 10px 14px;
            vertical-align: baseline;
            outline: none;
            -webkit-box-align: center;
            align-items: center;
            background: #d1ffff;
            border-radius: 3px;
            box-sizing: border-box;
            color: var(--defaultFontColor);
            cursor: pointer;
            display: flex;
            transition: all 0.1s ease 0s;
            width: 200px;
            text-align: left;
        }

        .c2039 {
            border: 0px;
            font-style: inherit;
            font-variant: inherit;
            font-weight: inherit;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 15px;
            margin: 0px;
            padding: 0px;
            vertical-align: baseline;
        }

        .c2116 {
            border: 0px;
            font-style: inherit;
            font-variant: inherit;
            font-weight: inherit;
            font-stretch: inherit;
            line-height: inherit;
            font-family: 'Rubik', sans-serif;
            font-size: 15px;
            color: #222;
            font-weight: 500;
            margin: 0px;
            padding: 0px;
            vertical-align: baseline;
            white-space: nowrap;
        }

        .c2124 {
            border: 0px;
            font-style: inherit;
            font-variant: inherit;
            font-weight: inherit;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 15px;
            margin: 0px 20px;
            padding: 0px;
            vertical-align: baseline;
        }

        .c2279 {
            border-color: var(--brandPrimaryColor);
            font-style: inherit;
            font-variant: inherit;
            font-weight: 600;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 15px;
            margin: 0px 0px 10px;
            padding: 10px 14px;
            vertical-align: baseline;
            outline: none;
            -webkit-box-align: center;
            align-items: center;
            background: #d1ffff;
            border-radius: 3px;
            box-sizing: border-box;
            color: var(--brandPrimaryColor);
            cursor: pointer;
            display: flex;
            transition: all 0.1s ease 0s;
            width: 200px;
            text-align: left;
        }

        .c2017 {
            border: 0px;
            font-style: inherit;
            font-variant: inherit;
            font-weight: inherit;
            font-stretch: inherit;
            line-height: inherit;
            font-family: inherit;
            font-size: 14px;
            margin: 0px auto;
            padding: 0px;
            vertical-align: baseline;
            display: flex;
            flex-wrap: wrap;
            max-width: 510px;
            width: 510px;
            margin-left: 10%;
        }

        .states {
            height: 60px;
            width: 208px;
            padding-left: 10px;
            border-style: none;
            -webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            margin-bottom: 30px;
            margin-left: 5px;
        }

        .fa-search {
            font-size: 18px;
        }

        .contact-us-submit-btn,
        .contact-us-submit-btn1 {
            width: 100%;
            text-align: center;
            height: 55px;
            border-radius: 6px;
            color: #ffffff;
            font-size: 18px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .contact-us-submit-btn:hover,
        .contact-us-submit-btn1:hover {
            width: 100%;
            text-align: center;
            height: 55px;
            border-radius: 6px;
            color: #ffffff;
            font-size: 18px;
            font-weight: 500;
            text-transform: uppercase;
        }

        @media (max-width: 1000px) {
            .number-info {
                width: 70%;
            }
        }

        @media (max-width: 600px) {
            .number-info {
                width: 100%;
            }

            .guide-image {
                width: 45px;
                height: 45px;
            }
        }
        .customTollFree__numberSelection {
            display: flex;
            flex-wrap: wrap;
            justify-content: left;
            align-items: flex-start;
        }
        @media (max-width: 360px) {
            .form-group {
                padding-left: 0px !important;
                padding-right: 0px !important;
            }
        }
    </style>
@endsection
@section('content')
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDJWX9X9" height="0" width="0" style="display:none"></iframe></noscript>
<script>document.addEventListener('DOMContentLoaded',function(){window.dataLayer=window.dataLayer||[];dataLayer.push({event:'funnel_view',funnel_step:'number_selection'});document.addEventListener('click',function(e){var b=e.target.closest&&e.target.closest('.contact-us-submit-btn1');if(b){dataLayer.push({event:'choose_number',funnel_step:'number_selection'});}},true);});</script>
    <section class="header-wrap">
        <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg') }});">
            <div class="container">
                <h2>Membership Card</h2>
            </div>
        </section>
        @include('includes.top_header')
        @include('includes.navbar')
    </section>
    <div class="centering__centering centering__relative c1386">
        <div class="number-info">
            <img src="public/numbers/chatgirl.jpg" class="img-circle guide-image">
            <div class="alert alert-info">
                @if (request()->get('val') == 0)
                    {{ 'The plan you chose allows up to 1 free number. You are able to add maximum 1 tollfree/local number!' }}
                @elseif(request()->get('val') == 1)
                    {{ 'The plan you chose allows up to 2 free numbers. You are able to add maximum 2 tollfree/local number!' }}
                @elseif($_GET['val'] == 2)
                    {
                    {{ 'The plan you chose allows up to 3 free numbers. You are able to add maximum 3 tollfree/local number!' }}
                @endif
            </div>
        </div>
        <div class="container">
            <div class="row membership-section-container">
                <div class="col-lg-12 col-md-12 col-sm-12 img-section-container">
                    <img class="img-fluid" src="/public/images/gvdlogo.png" style="width: 30%;">
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12" style="margin-top: 50px;">
                    <div class="panel panel-default">
                        <div class="panel-heading contact-panel-heading">Membership Card</div>
                        <div class="panel-body">

                            @if (request()->get('val') == 0)
                                <p>{{ 'The plan you chose allows up to 1 free number. You are able to add maximum 1 tollfree/local number!' }}
                                </p>
                            @elseif(request()->get('val') == 1)
                                <p>{{ 'The plan you chose allows up to 2 free numbers. You are able to add maximum 2 tollfree/local number!' }}
                                </p>
                            @elseif(request()->get('val') == 2)
                                <p>{{ 'The plan you chose allows up to 3 free numbers. You are able to add maximum 3 tollfree/local number!' }}
                                </p>
                            @endif
                            <form class="customTollFreeSearchForm__customTollFreeSearchForm c1461">
                                <h2 class="heading">Which Toll-Free Number are you looking for?</h2>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" autocomplete="off" id="tollFreeInput"
                                            placeholder="Ex: 800, 833, 844">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="searchTollFree()">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>

                            <!-- Toll-Free results -->
                            <div class="number-selection__wrapper c1998" id="tollFreeResults"></div>



                            <form class="customTollFreeSearchForm__customTollFreeSearchForm c1461">
                                <h2 class="heading">Which Local Area Code are you looking for?</h2>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" autocomplete="off" id="localInput"
                                            placeholder="Ex: 561 or 561245">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" onclick="searchLocal()">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </form>

                            {{-- <div class="number-selection__wrapper c1998" id="uniqueId">
                            </div> --}}

                            <!-- Local area code results -->
                            <div class="number-selection__wrapper c1998" id="localResults"></div>


                            <div class="form-group" style="padding-left: 40px; padding-right: 40px;">
                                <button type="button" onclick="setforforward(this)"
                                    class="button_slide slide_right contact-us-submit-btn1">Continue to membership</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>


    @push('scripts')
        <script type="text/javascript">
            var favorite = [];
            var package = <?php echo $_GET['val']; ?>;
            var limit = 0;
            if (package == 0) {
                limit = 1;
            } else if (package == 1) {
                limit = 2;
            } else if (package == 2) {
                limit = 3;
            }
            $("input[name='numbers1']").on('change', function(evt) {
                if ($(this).siblings(':checked').length >= limit) {
                    this.checked = false;
                    alert("You Already Select the Max numbers");
                }
            });

            function goToCreditApplication(param) {
                favorite = [];
                var count = 1;
                $.each($("input[name='numbers1']:checked"), function(e) {
                    if (count > limit) {
                        this.checked = false;
                        alert("You Already Select the Max numbers");
                    } else {
                        favorite.push($(this).val());
                    }
                    count += 1;
                });
                //   var checkedValues = $("input[name='numbers1']:checked").map(function() {
                //     return $(this).;
                //     //console.log(this);
                // }).get();
                // alert($("input[name='package']:checked").val());
                //console.log(favorite);
                //setforforward(param);
                //$("#finalNumber").val(param.text);
            }

            // var favorite = [];

            // // Max 1 per category
            // var maxTollfree = 1;
            // var maxLocal = 1;

            // $("body").on("change", "input.number-item", function() {

            //     let isToll = $(this).hasClass("tollfree");
            //     let isLocal = $(this).hasClass("local");

            //     if (isToll) {
            //         if ($(".number-item.tollfree:checked").length > maxTollfree) {
            //             this.checked = false;
            //             alert("You can select only 1 Toll-Free number");
            //             return;
            //         }
            //     }

            //     if (isLocal) {
            //         if ($(".number-item.local:checked").length > maxLocal) {
            //             this.checked = false;
            //             alert("You can select only 1 Local number");
            //             return;
            //         }
            //     }
            // });

            // function goToCreditApplication(param) {

            //     favorite = [];

            //     // Push selected items (tollfree + local)
            //     $("input.number-item:checked").each(function() {
            //         favorite.push($(this).val());
            //     });

            //     console.log("Selected numbers:", favorite);
            // }


            function setforforward(param) {
                //alert($("input[name='package']:checked").val());
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                    }
                });
                if (favorite.length > 0) {
                    $.ajax({
                        url: '/number/maintainSession',
                        type: "POST",
                        data: {
                            'number': JSON.stringify(favorite),
                            'package': <?php echo $_GET['val']; ?>
                        },
                        success: function(data) {
                            var referal_token = "<?php echo isset($_GET['referal_token']) ? $_GET['referal_token'] : ''; ?>";
                            var non_referal_token = "<?php echo isset($_GET['non-memberreferal_token']) ? $_GET['non-memberreferal_token'] : ''; ?>";
                            if (referal_token != '') {
                                location.replace("/credit-application?referal_token=" + referal_token);
                            } else if (non_referal_token != '') {
                                location.replace("/credit-application?non-memberreferal_token=" +
                                    non_referal_token);
                            } else {
                                location.replace("/credit-application");
                            }
                        },
                        error: function() {

                        }
                    });
                } else {
                    alert("Please Choose Atleast one number.");
                    return false;
                }
                return false;
            }

            function getRateCenter() {
                $.ajax({
                    url: 'number/getRateCenter?state=' + $('#myStatesH').val(),
                    type: "GET",
                    //data: { 'state' : $(this).val() },
                    success: function(data) {
                        var response = JSON.parse(data);
                        console.log(response);
                        if (response.success == 1) {
                            $didoptions = "<option value='-1'>Select City</option>";
                            if (!Array.isArray(response.data.rc)) {
                                $didoptions += "<option value='" + response.data.rc + "'>" + response.data.rc +
                                    "</option>";
                            } else {
                                $.each(response.data.rc, function(key, value) {
                                    $didoptions += "<option value='" + value + "'>" + value + "</option>";
                                });
                            }
                            $('#myRateCenterH').html($didoptions);
                        } else {

                        }
                    },
                    error: function() {

                    }
                });
            }

            function searchNumber() {
                var state = $('#myStatesH').val();
                var ratecenter = $('#myRateCenterH').val();
                if (ratecenter != -1 && state != -1) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
                        }
                    });
                    $.ajax({
                        url: 'number/searchlocalareacode',
                        type: "post",
                        data: {
                            'state': state,
                            'ratecenter': ratecenter
                        },
                        success: function(data) {

                            var responseText = JSON.parse(data);

                            console.log(data);

                            if (responseText.success == "1") {

                                var numbers =
                                    '<p class="customTollFree__closestMatches c2007">Select from these great numbers:</p> <div class="customTollFree__numberSelection customTollFree__vanity c2017">';
                                var flag = true;

                                console.log(responseText);
                                for (var i = 0; i < responseText.data.length; i++) {

                                    if (flag) {
                                        numbers = numbers +
                                            '<a onclick="goToCreditApplication(this)"  id="number-select__button" role="button" tabindex="0" class="select-number-button__wrapper select-number-button__wrapper--vanity c2027"><div class="c2039"> <span class="select-number-button__text c2116"><input type="checkbox" class="custom-control-input" name="numbers1" value="' +
                                            responseText.data[i][0] + '"><span class="c2124">' + responseText.data[
                                                i][0] + '</span></span></div></a>';
                                        flag = false;
                                    } else {
                                        numbers = numbers +
                                            '<a onclick="goToCreditApplication(this)" id="number-select__button" role="button" tabindex="0" class="select-number-button__wrapper select-number-button__wrapper--vanity select-number-button__wrapper--selected c2279"><div class="c2039"> <span class="select-number-button__text c2116"><input type="checkbox" name="numbers1" class="custom-control-input" value="' +
                                            responseText.data[i][0] + '"><span class="c2124">' + responseText.data[
                                                i][0] + '</span></span> </div></a>';
                                        flag = true;
                                    }

                                }
                                numbers = numbers + "</div>";
                                document.getElementById("uniqueId1").innerHTML = numbers;
                            } else {
                                document.getElementById("uniqueId1").innerHTML =
                                    '<p class="customTollFree__closestMatches c2007">Rats! All the phone numbers with this area code are already taken. Try changing your search.</p> <div class="customTollFree__numberSelection customTollFree__vanity c2017">';
                            }
                        },
                        error: function() {

                        }
                    });
                } else {
                    alert("Please select State and Ratecenter to  see the local numbers.");
                }
            }

            function searchTollFree() {
                let prefix = $('#tollFreeInput').val();

                $.ajax({
                    url: '/number/getData',
                    type: "GET",
                    data: {
                        search: prefix
                    },
                    success: function(response) {
                        renderNumbers(response, 'tollFreeResults'); // target tollFreeResults container
                    },
                    error: function() {
                        console.log("Something went wrong with Toll-Free search.");
                    }
                });
            }


            function searchLocal() {
                let num = $('#localInput').val();
                if (num.length !== 3 && num.length !== 6) {
                    alert("Enter area code: 561 or 561245");
                    return;
                }

                $.ajax({
                    url: '/number/getData',
                    type: "GET",
                    data: {
                        search: num
                    },
                    success: function(response) {
                        renderNumbers(response, 'localResults'); // target localResults container
                    },
                    error: function() {
                        console.log("Something went wrong with Local search.");
                    }
                });
            }

            function renderNumbers(response, containerId) {
                let container = $('#' + containerId);

                if (response.success == 1 && response.dids && response.dids.length > 0) {

                    // Detect class based on resultSet
                    let typeClass = response.resultSet === "tollfree" ? "tollfree" : "local";

                    var numbers =
                        '<p class="customTollFree__closestMatches">Available Numbers:</p>' +
                        '<div class="customTollFree__numberSelection customTollFree__vanity">';

                    var flag = true;

                    response.dids.forEach(function(num) {
                        var displayNumber = num.didSummary || num.id;

                        numbers += `
                                    <a onclick="goToCreditApplication(this)" 
                                       class="select-number-button__wrapper select-number-button__wrapper--vanity ${flag ? '' : 'select-number-button__wrapper--selected'}">
                                        <div>
                                            <span style="margin-left: 42px;">
                                                <input type="checkbox"
                                                    class="custom-control-input number-item ${typeClass}"
                                                    name="numbers1"
                                                    value="${displayNumber}">
                                                <span style="margin-left: 25px;">${displayNumber}</span>
                                            </span>
                                        </div>
                                    </a>`;

                        flag = !flag;
                    });

                    numbers += "</div>";
                    container.html(numbers);

                } else {
                    container.html('<p>No numbers found.</p>');
                }
            }




            //   function getData() {

            //       num = $('#custom-toll-free__input').val();
            //       $.ajaxSetup({
            //           headers: {
            //               'X-CSRF-Token': $('meta[name=csrf-token]').attr('content')
            //           }
            //       });
            //       $.ajax({
            //           url: 'number/getData',
            //           type: "get",
            //           data: {
            //               'tollNum': num
            //           },
            //           success: function(data) {
            //               var responseText = JSON.parse(data);
            //               if (responseText.success == "1") {

            //                   var numbers =
            //                       '<p class="customTollFree__closestMatches c2007">Or, select from these closest available matches:</p> <div class="customTollFree__numberSelection customTollFree__vanity c2017">';
            //                   var flag = true;

            //                   for (var i = 0; i < responseText.data.length; i++) {

            //                       if (flag) {
            //                           numbers = numbers +
            //                               '<a onclick="goToCreditApplication(this)" id="number-select__button" role="button" tabindex="0" class="select-number-button__wrapper select-number-button__wrapper--vanity c2027"><div class="c2039"> <span class="select-number-button__text c2116"><input type="checkbox" class="custom-control-input" name="numbers1" value="' +
            //                               responseText.data[i] + '"><span class="c2124">' + responseText.data[i] +
            //                               '</span></span></div></a>';
            //                           flag = false;
            //                       } else {
            //                           numbers = numbers +
            //                               '<a onclick="goToCreditApplication(this)" id="number-select__button" role="button" tabindex="0" class="select-number-button__wrapper select-number-button__wrapper--vanity select-number-button__wrapper--selected c2279"><div class="c2039"> <span class="select-number-button__text c2116"><input type="checkbox" class="custom-control-input" name="numbers1" value="' +
            //                               responseText.data[i] + '"><span class="c2124">' + responseText.data[i] +
            //                               '</span></span> </div></a>';
            //                           flag = true;
            //                       }

            //                   }
            //                   numbers = numbers + "</div>" +
            //                       '<div class="customTollFree__getNewBatch c4692"><p class="c4738">833, 844, 855 numbers are ALL toll free!</p></div>';
            //                   document.getElementById("uniqueId").innerHTML = numbers;
            //               }
            //           },
            //           error: function() {
            //               console.log("Something Went Wrong.");
            //           }
            //       });
            // }
        </script>
    @endpush
@endsection
