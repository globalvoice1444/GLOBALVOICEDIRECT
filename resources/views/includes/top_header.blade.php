<section class="header-bar">
            <div class="container">
                <div class="row"> 
                    <div class="col-lg-3 col-md-3 col-sm-6 logo-part">
                        <div class="logo">
                            <a href="/">
                                <img src="{{ asset('new_design/img/logo/gvdlogo.png')}}" alt="Global Voice Direct">
                            </a>
                        </div>
                    </div>
                    
                    <div class="offset-5 col-lg-4 col-md-4 col-sm-6">
                        <div class="right-side">
 
                            <div class="search">
                                <div class="search-icon">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                            <div class="user-bar">
                                <div class="user-item">
                                    <i class="fa fa-user"></i>
                                </div>
                            </div>
                             
                            <div class="search-top">
                                <form action="search" class="search-form" method="get">
                                <input
                                    type="search"
                                    name="search"
                                    value=""
                                    placeholder="Search here"
                                />
                                <button type="submit" id="searchsubmit">
                                    <i class="fa fa-search"></i>
                                </button>
                                </form>
                            </div>
                            <?php
                              if(session()->has('cart_item')){

                                $item_total = 0;
                                $total = 0;
                                $i=1;
                                $cartSize = 0;
                                $totalPrice = 0;
                                foreach (session()->get('cart_item') as $item1){

                                    $cartSize = $cartSize + 1;
                                    $id = $item1["code"];
                                    $qty = $item1["quantity"];
                                    $stmtLists =App\Http\Controllers\storeController::fetchProductsById($id);
                                    
                                    $rowListsCount =count($stmtLists);

                                   
                                    if($rowListsCount > 0){
                                        /*while($rowLists = $stmtLists->fetch()){*/
                                     foreach($stmtLists as $rowLists)
                                     {       

                                            $totalPrice = $totalPrice + ($rowLists->ProductPrice * $qty );
                                            break;
                                        }
                                    }
                                }
                            
                            ?>
                            <div class="top-cart">
                                <div class="cart">
                                    <i class="fa fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="shopping-cart">
                                <div class="shopping-cart-header">  
                                    <div class="shopping-cart-total">
                                        <span class="lighter-text">Total:</span>
                                        <span class="main-color-text">${{ $totalPrice }}</span>
                                    </div>
                                </div>
                                <ul class="shopping-cart-items">
                                	<?php 
                                    foreach (session()->get('cart_item') as $item1){
                                      $id = $item1["code"];
  
                                       $stmtLists =App\Http\Controllers\storeController::fetchItems($id);
                             
                                        $rowListsCount = count($stmtLists);

                                        if($rowListsCount > 0){
                                          foreach($stmtLists as $list){
     

                                      ?>
                                    <li class="clearfix">
                                        <img src="public/productImages/{{$list->ProductImage}}" alt="Yealink WF40 Wi-Fi USB Dongle">
                                        <div class="list-cart">
                                            <span class="item-name"><?php echo $list->ProductName; ?></span>
                                            <span class="item-price">
                                          <?php if($list->addonFixedPrice != null)
                                          {
                                            $proPrice = $list->addonFixedPrice;
                                            echo "$".$proPrice;
                                          }
                                          else
                                          {
                                            $discount = $list->ProductPrice * $list->addonDiscount/100;
                                            $proPrice = $list->ProductPrice - $discount;
                                            echo "$".$proPrice;

                                          }?></span>
                                            <span class="item-quantity">Quantity: <?php echo $item1["quantity"];?></span>
                                        </div>
                                    </li> 
                                    <?php }}} ?>
                                </ul>
                                <a href="/checkout" class="button-cart">Checkout</a>
                                <a href="/cartItems" class="button-cart">View Cart</a>
                            </div>
                            <?php }else{ ?>
                              <div class="top-cart" id="cart">
                                <div class="cart">
                                  <i class="fa fa-shopping-cart"></i>
                                </div>
                              </div>
                            <?php } ?> 
                            <div class="user-bar-top">
                                <ul class="list-item">
                                	@if (session()->has('user_id'))
                                	<li>
                                        <i class="fa fa-tachometer-alt"></i>
                                        <a href="">Dashboard</a>   
                                    </li>
                                    <li>
                                        <i class="fa fa-sign-out-alt"></i>
                                        <a href="/user/logout">Logout</a>   
                                    </li>
                                    @else
                                    @if(Request::path()!='user-login')
                                    <li> 
                                        <i class="fa fa-lock"></i>
                                        <a href="#" data-toggle="modal" data-target="#myModal" >login</a> 
                                        <div class="modal" id="myModal" style="display: none;" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content" style="background-color: unset; border: unset;"> 
                                                    <div class="loginmodal-container">
                                                        <h1>Login to Your Account</h1><br>
                                                        <form class="form-modal" action="/loginAction" method="post">
                                                            @csrf
                                                            <input type="hidden" name="" value="">
                                                            <input type="text" name="user" placeholder="Username">
                                                            <input type="password" name="pass" placeholder="Password">
                                                            <input type="submit" name="login" class="login loginmodal-submit" value="Login">
                                                        </form>
                                                        <p class="login-popup-text">
                                                            <a class="sn-a" href="/package-pricing">Register</a>-
                                                            <a class="sn-a" href="/forgotpassword">Forget Password</a>
                                                        </p>                
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                    <li>
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <a href="/package-pricing">Register</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </section>
        