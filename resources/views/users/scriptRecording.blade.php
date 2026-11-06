@extends('layouts.user.user_main')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{asset('public/user/plugin/css/style.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('public/user/css/player.css')}}">
    <link href="https://fonts.googleapis.com/css?family=RobotoDraft:400,500,700,400italic" rel="stylesheet" type="text/css">
   <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <script src="https://kit.fontawesome.com/5e43429d2e.js"></script>
   <style> 
        #current-time{
            font-family: monospace;
            font-size: 14px;
            color: #ffffff;
            padding-bottom: 84px;
            padding-left: 51px;
            position: absolute;
            top: 92%;
            left: 30%;
            transform: translate(-50%, -50%);
        }
        .t-play{
        cursor: pointer;
        .show{
            background: rgba(white, .7)
            padding: .5rem
            width: 12rem
        }
        .right .search {
          float: left;
          display: block;
          margin-right: 2.3576520234%;
          width: 65.8807826589%;
          text-align: right;
        }
        .right .search:last-child {
          margin-right: 0;
        }
        .right .open-search {
          color: white;
          cursor: pointer;
          padding: 0 0.5rem;
          text-transform: uppercase;
        }
    
        h3.table-header {
            font-size: 20px;
            line-height: 28px;
            text-align: center;
            padding-top: 40px;
            color: #134576;
            text-transform: uppercase;
            border-radius: 6px 6px 0 0;
        }
        .forms-inlines-forms {
            padding: 60px 20px 20px 20px;
            border-radius: 6px;
            -webkit-box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
            box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
            border: none;
            margin: 30px 0px 40px 0px;
            -webkit-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
            background: #dfe4e7;
        }
        .top-detail-section {
            border: 1px solid #3f4d67;
            padding: 34px 16px 20px 16px;
            margin: -21px 0px 15px;
            border-radius: 5px;
        }
        p.detail-text {
            font-size: 16px;
            color: #134576;
        }
        .mt-20.form-group.form-primary {
            margin-top: 20px;
        }
        .btn-info {
            background-color: #134576;
            border-color: #134576;
            color: #fff;
            cursor: pointer;
            -webkit-transition: all ease-in 0.3s;
            transition: all ease-in 0.3s;
            border-radius: 6px;
        }
        .btn-info:active, .btn-info:not([disabled]):not(.disabled).active, .btn-info:not([disabled]):not(.disabled):active, .btn-info:hover, .btn-info:not([disabled]):not(.disabled).hover, .btn-info:not([disabled]):not(.disabled):hover {
            background-color: #b0cb02;
            border-color: #b0cb02;
            -webkit-box-shadow: none;
            box-shadow: none;
            color: #fff;
        }
        h3.table-heading {
            text-align: center;
            background-color: #134576;
            padding-top: 20px;
            padding-bottom: 20px;
            color: #fff;
            text-transform: uppercase;
            border-radius: 6px 6px 0 0;
        }
        .form-inline-form {
            padding: 20px 20px 77px 20px;
            border-radius: 0 0 6px 6px;
        }
        .form-control:disabled, .form-control[readonly] {
            background-color: #fff;
            opacity: 1;
        }
        label.table-heading {
            color: #134576;
            font-size: 18px;
            font-weight: 600;
        }
        .recording-card {
            border: 1px solid #3f4d67;
            border-radius: 8px 8px 6px 6px;
        }
        #left_script_sec {
            padding: 30px 0px 0px 0px;
            margin-bottom: 35px;
        }
        .right_script_bar {
            padding: unset;
            border: 1px solid #3f4d67;
            border-radius: 8px 8px 6px 6px;
            margin: 30px 0px 20px 0px;
            height: auto;
        }
        .audioplayer_wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .search-box {
             position: absolute;
             top: 50%;
             left: 50%;
             transform: translate(-50%, -50%);
             height: 40px;
             border-radius: 40px;
             display: none;
             /*padding: 10px;*/
        }

        .search-box:hover > .search-txt {
             width: 100px;
             padding: 0 6px;
        }

        .search-box:hover {
             background: white;
             color: black;
        }

        .search-btn {
             color: #e84118;
             float: right;
             height: 40px;
             border-radius: 50%;
             display: flex;
             justify-content: center;
             align-items: center;
             transition: 0.4s;
             color: white;
             cursor: pointer;
        }

        .search-btn > i {
             font-size: 24px;
        }
        .search-txt {
             border: none;
             background: none;
             outline: none;
             float: left;
             padding: 0;
             color: black;
             font-size: 14px;
             transition: 0.4s;
             line-height: 40px;
             width: 0px;
            /* font-weight: bold;*/
        }
        .music-playlist{
            overflow: scroll;
        }
        .played{
            cursor: pointer;
        }
        .progress{
            cursor: pointer;

        }
    </style>
@endsection
@section('content')

    <div class="card">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="script_header">
                        <h3 class="table-header">PLEASE SEND US YOUR SCRIPT SO WE CAN SEND TO OUR STUDIO FOR YOUR <br>
                        AUTO ATTENDANT/ANSWERING MACHINE GREETING</h3>
                    </div>
                </div>
                @if(session('status'))
                    <div class="alert alert-info">{{ session('status')}}</div> 
                @endif
                @if($errors->any())
                    @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
                <?php if(isset( $_GET['message'])){ ?>
                  <div class="alert alert-info">
                    <strong>Info!</strong> <?php echo $_GET['message']; ?>.
                  </div>
                <?php } ?>
                <div class="col-sm-12 col-md-12">
                    <div class="forms-inlines-forms">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="wizard">
                                    <section class="top-detail-section">
                                        <div class="col-md-12">
                                          <p class="detail-text">Your computerized automated attendant greeting on your Global Voice Direct business telephone system is the first thing your customers and business associates will hear when they call your company </p>
                                            <div class="mt-20 form-group form-primary">
                                                <button class="btn btn-info btn-xs waves-effect waves-light" type="submit">
                                                  LEARN MORE</button>
                                            </div>

                                        </div>
                                    </section>
                                    <section class="script_recording">
                                        <div class="row">
                                            <div class="col-md-7 col-sm-12" >
                                                <div id="left_script_sec">
                                                    <div class="recording-card">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="top-title border-bootm">
                                                                    <h3 class="table-heading">Script Recording</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-inline-form">
                                                            
                                                            @if($userFormDetail == null)
                                                                <form method="post" action="{{ route('saveScriptInfo') }}" enctype="multipart/form-data">
                                                                    {{ csrf_field()}}
                                                                    <div class="form-group">

                                                                        <label class="table-heading"  class="table-heading" for="main_script">Main greeting script:</label>

                                                                        <textarea class="form-control" name="main_script" rows="4"></textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                        <label  class="table-heading" for="hours_script">After-hours greeting script:</label>

                                                                        <textarea class="form-control" name="hours_script" rows="4"></textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                    <label  class="table-heading" for="message_onhold">Message on hold scripts:</label>

                                                                    <textarea class="form-control" name="message_onhold" rows="4"></textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                    <label class="table-heading"  class="table-heading" for="special_pronunciation_words">Special Pronunciation Words:</label>

                                                                    <input type="text" name="special_pronunciation_words" class="form-control">
                                                                    </div>
                                                                     <div class="form-group">

                                                                        <label class="table-heading"  class="table-heading" for="audio_file_path">Audio:</label>
                                                                        <input type="file" name="audioFile">
                                                                    </div>

                                                                    <button type="submit" id="submit_script_btn" class="btn btn-info " name="submit">Submit</button>
                                                                </form>
                                                            @elseif($userFormDetail->active==2 or $userFormDetail->active==1)
                                                            <div class="form-group">
                                                                <label for="main_script">Main greeting script:</label>
                                                                <textarea class="form-control" name="main_script" rows="4" readonly>{{ trim($userFormDetail->main_greeting_script) }}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="hours_script">After-hours greeting script:</label>
                                                                <textarea class="form-control" name="hours_script" rows="4" readonly>{{ trim($userFormDetail->after_hours_script)}}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="message_onhold">Message on hold scripts:</label>
                                                                <textarea class="form-control" name="message_onhold" rows="4" readonly>{{ trim($userFormDetail->hold_script)}}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="message_onhold">Special Pronunciation Words:</label>
                                                                <textarea class="form-control" name="special_pronunciation_words" rows="4" readonly>{{ trim($userFormDetail->special_pronunciation_words)}}</textarea>
                                                            </div>
                                                            <div class="form-group">

                                                                <label for="message_onhold">Audio:</label>
                                                                @if($userFormDetail->audio_file_path !=null)
                                                                    <audio controls><source src="/user/phone-scripts/audio/{{ $userFormDetail->id }}" type="audio/mpeg"></audio>
                                                                    <!-- <audio controls>
                                                                        <source src="/public/users/{{ $userFormDetail->audio_file_path}}" type="audio/mpeg">
                                                                    </audio> -->
                                                                        
                                                                @else
                                                                    <strong>Audio Not Exist</strong>
                                                                 @endif
                                                            </div>
                                                                
                                                                
                                                            @else
                                                                <form method="post" action="/updatescriptinfo" enctype="multipart/form-data">
                                                                    {{ csrf_field()}}
                                                                    <input type="hidden" name="sid" value="{{$userFormDetail->id}}">
                                                                    <div class="form-group">

                                                                        <label class="table-heading"  class="table-heading" for="main_script">Main greeting script:</label>

                                                                        <textarea class="form-control" name="main_script" rows="4">{{ trim($userFormDetail->main_greeting_script) }}</textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                        <label  class="table-heading" for="hours_script">After-hours greeting script:</label>

                                                                        <textarea class="form-control" name="hours_script" rows="4">{{ trim($userFormDetail->after_hours_script)}}</textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                    <label  class="table-heading" for="message_onhold">Message on hold scripts:</label>

                                                                    <textarea class="form-control" name="message_onhold" rows="4">{{ trim($userFormDetail->hold_script)}}</textarea>
                                                                    </div>
                                                                    <div class="form-group">

                                                                    <label class="table-heading"  class="table-heading" for="special_pronunciation_words">Special Pronunciation Words:</label>

                                                                    <input type="text" name="special_pronunciation_words" class="form-control" value="{{ trim($userFormDetail->special_pronunciation_words)}}">
                                                                    </div>
                                                                     <div class="form-group">

                                                                        <label class="table-heading"  class="table-heading" for="audio_file_path">Audio:</label>
                                                                        <input type="file" name="audioFile">
                                                                    </div>

                                                                    <button type="submit" id="submit_script_btn" class="btn btn-info " name="submit">Submit</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-sm-12" >
                                                <div class="right_script_bar">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="top-title border-bootm">
                                                                <h3 class="table-heading">Automated Attendant <br>
                                                                 Greeting</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                  
                                                    <div class="audioplayer_wrapper">

                                                        <div class="player">
                                                            <div class="cover" id="cover"></div>
                                                            <nav>
                                                                <div class="left">
                                                                    <i class="material-icons">menu</i>
                                                                    <!--<h6>Playlist</h6>-->
                                                                </div>
                                                                <div class="right">
                                                                    <div class="search-box">
                                                                       <input type="text" name="" class="search-txt" placeholder="Search"/>
                                                                       <a class="search-btn" href="#">
                                                                       <i class="material-icons search">
                                                                            search
                                                                            </i>
                                                                       </a>
                                                                 </div>
                                                        
                                                                    <i class="material-icons">queue_music</i>
                                                                </div>
                                                            </nav>
                                                            <div class="player-ui">
                                                                <div class="title">
                                                                    <h3 id="change-title">Play</h3>
                                                                </div>
                                                                <div class="small">
                                                                    
                                                                    <i class="material-icons" id="songdata">replay</i>
                                                                    <!--<p>Adele</p>-->
                                                                    <i class="material-icons t-play" id="vol" onclick="mute()" >volume_up</i>
                                                                    <input type="range" id="change_vol" onchange="change_vol()" step="0.05" min="0" max="1" value="1">
                                                                    </div>

                                                                <div class="progress" id="progressbar">
                                                                    <div class="played" id="fillbar">
                                                                        <!--<div class="circle"></div>-->
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="controls">
                                                                    <audio id="music_player">
                                                                        <source id="playaudio" src="mix/2.mp3"/>
                                                                    </audio>
                                                                    <i class="material-icons t-play" id="prevSong" onclick="prev()">skip_previous</i>
                                                                    <i class="material-icons t-play main-play" id="pause_button" onclick="playOrPauseSong()">play_arrow</i>
                                                                    <i class="material-icons t-play" id="nextSong" onclick="next()">skip_next</i>
                                                                </div>
                                                            </div>
                                                            <!--<div class="btn">
                                                                <i class="material-icons">shuffle</i>
                                                            </div>-->
                                                            <div class="music-playlist">
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/sample%201.mp3">Sample 1</h5>
                                                                                <p>sample 1</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample2.mp3">Sample 2</h5>
                                                                                <p>sample 2</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample3.mp3">Sample 3</h5>
                                                                                <p>sample 4</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample4.mp3">Sample 4</h5>
                                                                                <p>sample 4</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample5.mp3">Sample 5</h5>
                                                                                <p>sample 5</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample6.mp3">Sample 6</h5>
                                                                                <p>sample 6</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample7.mp3">Sample 7</h5>
                                                                                <p>sample 7</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample8.mp3">Sample 8</h5>
                                                                                <p>sample 8</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample10.mp3">Sample 9</h5>
                                                                                <p>sample 9</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample11.mp3">Sample 10</h5>
                                                                                <p>sample 10</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample12.mp3">Sample 11</h5>
                                                                                <p>sample 11</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample13.mp3">Sample 12</h5>
                                                                                <p>sample 12</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample14.mp3">Sample 13</h5>
                                                                                <p>sample 13</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample15.mp3">Sample 14</h5>
                                                                                <p>sample 14</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                <div class="music">
                                                                    <div class="song-1">
                                                                        <div class="info">
                                                                            <div class="img first"></div>
                                                                            <div class="titles">
                                                                                <h5 class="t-play audioPlay playlist"data-audio="/public/user/data/gvd%20sample%2016.mp3">Sample 15</h5>
                                                                                <p>sample 15</p>
                                                                            </div>
                                                                        </div>
                                                                        <!--<div class="state playing">
                                                                            <i class="material-icons">equalizer</i>
                                                                        </div>-->
                                                                    </div>
                                                                </div>
                                                                
                                                                 
                                                            </div>
                                                            
                                                                
                                                               <!--  <div class="song-2">
                                                                    <div class="info">
                                                                        <div class="img second"></div>
                                                                        <div class="titles">
                                                                            <h5 class="t-play" data-audio="mix/2.mp3">Californication</h5>
                                                                            <p>Red Hot Chili Pepers</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="state">
                                                                        <i class="material-icons">play_arrow</i>
                                                                    </div>
                                                                </div> --><!--
                                                                <div class="song-3">
                                                                    <div class="info">
                                                                        <div class="img third"></div>
                                                                        <div class="titles">
                                                                            <h5>6 INCH</h5>
                                                                            <p>beyoncé</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="state">
                                                                        <i class="material-icons">play_arrow</i>
                                                                    </div>
                                                                </div>
                                                                <div class="song-4">
                                                                    <div class="info">
                                                                        <div class="img fourth"></div>
                                                                        <div class="titles">
                                                                            <h5>Purple rain</h5>
                                                                            <p>Prince & The Revolution</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="state">
                                                                        <i class="material-icons">play_arrow</i>
                                                                    </div>
                                                                </div> -->
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
<script>/* Prefill from Phone Scripts page (one-time, editable fields only) */
document.addEventListener('DOMContentLoaded', function(){ try { var j = sessionStorage.getItem('gvd_script_prefill'); if (!j) return; sessionStorage.removeItem('gvd_script_prefill'); var x = JSON.parse(j);
 var m = {main_script: x.main, hours_script: x.after, message_onhold: x.hold, special_pronunciation_words: x.pron};
 Object.keys(m).forEach(function(n){ var el = document.querySelector('form [name="'+n+'"]:not([readonly])'); if (el && m[n]) el.value = m[n]; }); } catch (e) {} });
</script>
<script type="text/javascript">

        /////////////////////////////
        var songs = [
        {
        mp3:'/public/user/data/sample%201.mp3',
        oga:'/public/user/data/01.ogg',
        title:'Sample1',
        artist:'global voice direct',
        duration:'0:29',
        cover:'public/user/mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample2.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample2',
            artist:'global voice direct',
            duration:'0:29',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample3.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample3',
            artist:'global voice direct',
            duration:'0:17',
            cover:'public/user/mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample4.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample4',
            artist:'global voice direct',
            duration:'0:27',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample5.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample5',
            artist:'global voice direct',
            duration:'0:48',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample6.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample6',
            artist:'global voice direct',
            duration:'0:25',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample7.mp3',
            oga:'data/01.ogg',
            title:'Sample7',
            artist:'global voice direct',
            duration:'0:13',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample8.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample8',
            artist:'global voice direct',
            duration:'0:12',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample10.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample9',
            artist:'global voice direct',
            duration:'3:01',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample11.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample10',
            artist:'global voice direct',
            duration:'0:27',
            cover:'mix/1.png'
        },
         {
            mp3:'/public/user/data/gvd%20sample12.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample11',
            artist:'global voice direct',
            duration:'0:31',
            cover:'mix/1.png'
        },
        {
            mp3:'data/gvd%20sample13.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample12',
            artist:'global voice direct',
            duration:'0:49',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample14.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample13',
            artist:'global voice direct',
            duration:'1:00',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample15.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample14',
            artist:'global voice direct',
            duration:'0:40',
            cover:'mix/1.png'
        },
        {
            mp3:'/public/user/data/gvd%20sample%2016.mp3',
            oga:'/public/user/data/01.ogg',
            title:'Sample15',
            artist:'global voice direct',
            duration:'0:49',
            cover:'mix/1.png'
        },
    ];

    ///////////////////////////

    var fillbar = document.getElementById("fillbar");
    var songTitle = document.getElementById("change-title");
    var song = new Audio(); 
    var currentSong = 0;
    //window.onload = playSong;
    window.onload = function(){
        //song.src= songs[currentSong];
        song.src = songs[currentSong].mp3;//songs.mp3;
        song.play();
        playOrPauseSong();
        document.getElementById("pause_button").innerHTML = "play_arrow";
        
        var sample = currentSong + 1;
        songTitle.textContent = songs[currentSong].title; //songs[currentSong];
        
    }
   
    function playSong(){
        song.src= songs[currentSong].mp3;
        song.play();
        songTitle.textContent =songs[currentSong].title;
    }
    function playOrPauseSong(){
        if(song.paused){
            song.play();
            document.getElementById("pause_button").innerHTML = "pause";
        }else{
            song.pause();
            document.getElementById("pause_button").innerHTML = "play_arrow";
        }
    }
    song.addEventListener('timeupdate', function(){
        var position = song.currentTime/song.duration
        fillbar.style.width = position * 100 +'%';
        if(song.ended){
            next();
        }
        

    });
    function next(){
        currentSong++;
        if(currentSong > songs.length - 1){
            currentSong = 0;
        }
        playSong();
        var sample = currentSong + 1;
        document.getElementById("pause_button").innerHTML = "pause";
        songTitle.textContent = songs[currentSong].title; //songs[currentSong];
        //document.getElementById("change-title").innerHTML = "Sample " + sample;
    }
    function prev(){
        currentSong--;
        //alert(currentSong);
        if(currentSong < 0){
            currentSong = songs.length - 1;
        }
        playSong();
        if(currentSong == 0)
        {
            sample = 1;
        }
        else{
            var sample = currentSong + 1 ; //- 1;
        }
        
        document.getElementById("pause_button").innerHTML = "pause";
        songTitle.textContent = songs[currentSong].title; //songs[currentSong];
        
    }
    var cvol = change_vol();
    function mute(){
        if(song.volume == 0){
            change_vol();
            
            document.getElementById("vol").innerHTML = "volume_up";
        }else{
            song.volume = 0;
            document.getElementById("vol").innerHTML = "volume_off";
        }
        
    }
    function change_vol(){
        song.volume=document.getElementById("change_vol").value;
    }
function search(nameKey, myArray){
    for (var i=0; i < myArray.length; i++) {
        if (myArray[i].mp3 === nameKey) {
            //return myArray[i];
            return i;
            break;
        }
    }
}
$(function(){

    $(".playlist").click(function(){
        var play = $(this).attr("data-audio");
        song.src = play;
        song.play();
        var title = $(this).text();
        $("#change-title").text(title);
        document.getElementById("pause_button").innerHTML = "pause";
        
        var resultObject = search(play, songs);
        currentSong = resultObject;
        

        songTitle.textContent = songs[currentSong].title; //songs[currentSong];
        
    });
    
});

    var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b>Script Recording</b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/user/script_recording"]').parents().addClass('active');

</script>
@endsection