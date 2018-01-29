<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <!--   <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
        <link rel="stylesheet" type="text/css" href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{asset('assets/fonts/font-awesome.min.css')}}">


        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: cursive;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .position-ref {
                position: relative;
            }   

            ul{
                list-style: none;
            }

            ul a, ul a:hover,ul a:visited{
                color: #fff;
                text-decoration: none;
            }

            .menu {
                background: #A51D21;
                color: #ffffff; 
                position: fixed;
                z-index: 999999;
                width: 100%;
                top: 0;
                left: 0;                
            }        

            .menu li{
                    float: left;
                    padding: 30px 23px 30px 0px;
            }                   
            .pages{
                height: 567px; 
            }
            .light-green{
                color: #9CBEA9;
            }

             #home{
                background-image: url('{{asset('assets/images/b1.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;                
                color: #fff;
                margin-top: 80px;
                font-family: cursive;
                padding: 57px;      
            }

            #home img{
                height: 69px;
            }

            #home h1{
                    font-size: 50px;                    
                    font-weight: bold;                    
            }

            .italic{
                font-style: italic;
            }

            .margin-bottom-100{
                margin-bottom:100px;
            }
            .special-ruler{
                display: -webkit-inline-box;
            }
            .special-ruler p{
                    width: 100px;
                    border-top: 3px solid #fff;
                    margin-top: 10px;
                    margin-right: 10px;
                    margin-left: 10px;
            }

            .special-ruler i{
                font-size: 20px;
            }

            .icon-menu{
                padding-right: 6px;
            }

            /*About*/
            #about .bg-img{
                background-image: url('{{asset('assets/images/about.jpg')}}');                
                background-repeat: no-repeat;
                background-size: contain;
                height: 260px;

            }          

            .btn-outline{
                border: 1px solid #fff;
                background: #171516;
                padding: 3px;
                font-size: 10px;

            }

            #about .section1{
                    margin: 57px 202px;
                    height: 260px;
                    background: #171516;
                    color: #fff;
            }

            #about .section1 .inner h3{
                margin-bottom: 20px;
            }

            #about .section1 .inner p{
                color: #ccc;
                margin-bottom: 19px;
                font-size: 12px;
            }

            #about .section2{
                padding: 0 15px;
            }

            #about .section2 .inner{
                height: 446px;
                background-image: url('{{asset('assets/images/modal.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;
            }

            .small-sections{
                height: 223px;
            }

           #about .section2 .first{
                background-color: #FAA026;                
                color: #fff;
            }

            #about .section2 .first h3,#about .section2 .fourth h3{
                margin-bottom: 10px;
            }


            #about .section2 .first p{
                font-size: 10px;
                color: #000;
            }
            #about .section2 .first div,#about .section2 .fourth div{
                padding: 45px;                
            }

             #about .section2 .second{
                 background-image: url('{{asset('assets/images/m4.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;

             }

              #about .section2 .third{
                 background-image: url('{{asset('assets/images/g7.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;

             }

             #about .section2 .fourth{
                background-color: #00BB84;                
                color: #fff;
            }

             #about .section2 .fourth p{
                font-size: 10px;
            }

            #about .section3{
                    margin: 57px 202px;
                    height: 260px;
                    background: #ffffff;                   
            }

            .ads{
                height: 183px;
                width: 210px;
                border: 1px solid #ddd;
            }

            .section3 .first{
                background-image: url('{{asset('assets/images/g7.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;
            }

            .section3 .second{
                background-image: url('{{asset('assets/images/g2.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;
            }

            .section3 .third{
                background-image: url('{{asset('assets/images/g3.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;
            }

             .section3 .fourth{
                background-image: url('{{asset('assets/images/g4.jpg')}}');
                background-repeat: no-repeat;
                background-size: cover;
            }

            .section3 .cont{
                margin-right: 10px;
                float: left;
            }

            .owl-btn{
                position: absolute;
                top: 75px;
                font-size: 18px;
            }

            .ads-conitainer{
                padding: 0 32px;
            }

            .owl-btn span{
                padding: 5px 5px;
                border: 3px solid #ddd;
                border-radius: 11px;
                font-size: 14px;
                cursor: pointer;
                color: #ddd;

            }

            .section3 h3{
                    color: #A21321;
                    margin-bottom: 26px;
                    font-weight: bold;
            }

            #menu{
                background-image: url('{{asset('assets/images/mbg.png')}}');                
                background-repeat: no-repeat;
                background-size: cover;
            }

        </style>
    </head>
    <body>
         <nav class="menu">            
                <div class="container">
                    <div class="col-md-8">
                         <ul>
                             <li><a href="#home">HOME</a></li>
                             <li><a href="#about">ABOUT</a></li>
                             <li>MENU</li>
                             <li>TEAM</li>
                             <li>TESTIMONIAL</li>
                             <li>CONTACT</li>
                         </ul>
                    </div>
                    <div class="col-md-4">
                         <ul>
                             <li><i class="fa fa-envelope-o light-green icon-menu"></i> info@example.com</li>
                             <li><i class="fa fa-phone light-green icon-menu"></i> 1837799</li>                           
                         </ul>
                    </div>
                </div>
         </nav>
         <div id="home" class="pages text-center">
             <img src="{{asset('assets/images/logo.png')}}">
             <h1 class="margin-bottom-100 italic">Spicy Bite</h1>
             <h1>Tasty experience in every bite</h1>
             <div class="special-ruler">
                 <p>&nbsp;</p> <i class="fa fa-cutlery"></i> <p>&nbsp;</p>
             </div>
             <p>Make Your kinda Meal</p>
         </div>
         <div id="about">
             <div class="section1">
                 <div class="row">
                     <div class="col-md-6 col-xs-6 bg-img">                         
                     </div>
                     <div class="col-md-6 col-xs-6">
                         <div class="panel-body inner">
                            <h3>Welcome To Spicy Bite</h3>

                            <h5>Feel the flavour, feel the aroma</h5>
                            <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has  making it look like readable English. </p>
                            <button class="btn-outline">FIND OUT MORE</button>
                         </div>
                     </div>
                 </div>
             </div>

             <div class="section2">
                 <div class="row">
                    <div class="col-md-6">
                         <div class="row">
                             <div class="col-md-6 col-xs-6 small-sections first text-center">
                                 <div>
                                     <h3>Our story</h3>
                                    <p>by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has  making it look is that it has  making it lookis that it has  making it look hsh ddjj </p>
                                 </div>
                             </div>
                             <div class="col-md-6 col-xs-6 small-sections second">
                                 
                             </div>
                             <div class="col-md-6 col-xs-6 small-sections third">
                                 
                             </div>
                             <div class="col-md-6 col-xs-6 small-sections fourth text-center">
                                 <div>
                                      <h3>Delicious Food</h3>
                                    <p>by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has  making it look is that it has  making it lookis that it has  making it look hsh ddjj </p>
                                 </div>
                             </div>
                         </div>
                    </div>
                    <div class="col-md-6 inner">
                        
                    </div>
                 </div>
             </div>

             <div class="section3">
                 <h3>Taste the Best!</h3>
                 <div class="scroll" style="position: relative;">
                     <div class="owl-btn" style="left: -22px;">
                        <span>
                            <i class="fa  fa-caret-left"></i>
                        </span>
                     </div>
                     <div class="ads-conitainer">
                        <div class="cont">
                            <div class="ads first">
                                
                            </div>
                        </div>
                        <div class="cont">
                            <div class="ads second">
                                
                            </div>
                        </div>
                        <div class="cont">
                            <div class="ads third">
                                
                            </div>
                        </div>
                        <div class="cont">
                            <div class="ads fourth">
                                
                            </div>
                        </div>
                     </div>
                     <div class="owl-btn" style="right: 0">
                         <span>
                            <i class="fa  fa-caret-right"></i>
                        </span>
                     </div>
                 </div>
             </div>
         </div>
         <div id="menu" class="pages">
             
         </div>
    </body>
</html>
