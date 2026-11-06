<nav class="navbar navbar-expand-lg navbar-light">
            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/"
                    >HOME<span class="sr-only">(current)</span></a>
                    </li>
                    <li @if(Request::path()=='howitworks') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/howitworks">HOW IT WORKS</a>
                    </li>
                    <li @if(Request::path()=='affiliate-referrals') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/affiliate-referrals">REFERRAL PROGRAM</a>
                    </li>
                    <li @if(Request::path()=='store') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/store">VOIP PHONES</a>
                    </li>
                    <li @if(Request::path()=='feature') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/feature">FEATURES</a>
                    </li>
                    <li @if(Request::path()=='contact-us') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/contact-us">CONTACT</a>
                    </li>
                    <?php if(!isset($_SESSION["user_id"])) { ?>
                    <li @if(Request::path()=='chose-pakage') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/chose-pakage">CREDIT APPLICATION</a>
                    </li>
                    <?php } ?>
                    <li @if(Request::path()=='portin-request-form') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/portin-request-form">PORT-IN-REQ FORM</a>
                    </li>
                    <li @if(Request::path()=='download-app') class="nav-items active" @else class="nav-items" @endif>
                        <a class="nav-link" href="/download-app">APPS</a>
                    </li>
                    <li class="nav-items">
                        <a class="nav-link" href="https://mybilling.globalvoicedirect.com:8092/login">PHONE LOGIN PAGE</a>
                    </li>
                </ul>
            </div>
        </nav>