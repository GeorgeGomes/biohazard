<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="Dashboard">
        <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">


        <title>{{Config::get('app.website_title')}}</title>

        <?php
        $theme = Theme::all();

        $logo = '/image/logo.png';
        $favicon = '/image/favicon.ico';
        foreach ($theme as $themes) {
            $logo = '/uploads/' . $themes->logo;
            $favicon = '/uploads/' . $themes->favicon;
        }
        if ($logo == '/uploads/') {
            $logo = '/image/logo.png';
        }
        if ($favicon == '/uploads/') {
            $favicon = '/image/favicon.ico';
        }
        ?>

        <link rel="icon" type="image/ico" href="<?php echo asset_url(); ?><?php echo $favicon; ?>">

        <!-- Bootstrap core CSS -->
        <link href="<?php echo asset_url(); ?>/web/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo asset_url(); ?>/web/css/bootstrap-tour.min.css" rel="stylesheet">
        <!--external css-->
        <link href="<?php echo asset_url(); ?>/web/font-awesome/css/font-awesome.css" rel="stylesheet" />

        <!-- Custom styles for this template -->
        <link href="<?php echo asset_url(); ?>/web/css/style.css" rel="stylesheet">
        <link href="<?php echo asset_url(); ?>/web/css/style-responsive.css" rel="stylesheet">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="<?php echo asset_url(); ?>/web/js/validation.js"></script>
    </head>

    <body>

        <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->

        <div id="login-page">
            <div class="container">
                <a href="<?php echo web_url(); ?>" class="col-md-2 col-xs-12"><img class="imghome" src="<?php echo asset_url(); ?><?php echo $logo; ?>" alt="" style="width: 100%"></a>
                <form class="form-login" action="{{route('/user/verify')}}" method="post">
                    <!-- <h2 class="form-login-heading">sign in now</h2> -->
                    <h2 class="form-login-heading">{{ trans('login.enter_now');}}</h2>
                    <div class="login-wrap">
                        <span id="no_email_error1" style="display: none"> </span>
                        <input type="text" name="email" class="form-control" placeholder="{{ trans('login.mail');}}" onblur="ValidateEmail(1)" id="email_check1" required="" autofocus>

<!--<input type="text" name="email" class="form-control" placeholder="{{ trans('login.mail');}}" autofocus>-->
                        <br>
                        <input type="password" name="password" class="form-control"  placeholder="{{ trans('login.password');}}">
                        <label class="checkbox">
                            <span class="pull-right">
                                <!-- <a data-toggle="modal" href="login.html#myModal"> Forgot Password?</a> -->
                                <a data-toggle="modal" href="login.html#myModal">{{ trans('login.forgot_password');}}</a>

                            </span>
                        </label>

                        @if(Session::has('error'))
                        <div class="alert alert-danger">
                            <b>{{ Session::get('error') }}</b> 
                        </div>
                        @endif
                        @if(Session::has('success'))
                        <div class="alert alert-success">
                            <b>{{ Session::get('success') }}</b> 
                        </div>
                        @endif


                        <!-- <button class="btn btn-theme btn-block" type="submit" id="provider-signin"><i class="fa fa-lock"></i> SIGN IN</button> -->
                        <button class="btn btn-theme btn-block" type="submit" id="provider-signin"><i class="fa fa-lock"></i>{{ trans('login.enter');}}</button>
                        <hr>
                        <!--
                        <div class="login-social-link centered">
                        <p>or you can sign in via your social network</p>
                            <button class="btn btn-facebook" type="submit"><i class="fa fa-facebook"></i> Facebook</button>
                            <button class="btn btn-twitter" type="submit"><i class="fa fa-twitter"></i> Twitter</button>
                        </div>
                        -->
                        <div class="registration">
                            <!-- Don't have an account yet?<br/> -->
                            {{ trans('login.dont_account');}}<br/>
                            <a class="" href="{{route('/user/signup')}}">
                                <!-- Create an account -->
                                {{ trans('login.create_one');}}
                            </a>
                        </div>

                    </div>
                </form>

                <!-- Modal -->
                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <!-- <h4 class="modal-title">Forgot Password ?</h4> -->
                                <h4 class="modal-title">{{ trans('login.forgot_password');}}</h4>
                            </div>
                            <form method="POST" action="{{route('/user/forgot-password')}}">
                                <div class="modal-body">
                                    <!-- <p>Enter your e-mail address below to reset your password.</p> -->
                                    <p>{{ trans('login.password_message');}}</p>
                                    <span id="no_email_error2" style="display: none"> </span>
                                    <input type="text" name="email" placeholder="{{ trans('login.mail');}}" autocomplete="off"  class="form-control placeholder-no-fix" required="" onblur="ValidateEmail(2)" id="email_check2" >


                                </div>
                                <div class="modal-footer">
                                    <!-- <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button> -->
                                    <!-- <button class="btn btn-theme" type="submit">Submit</button> -->
                                    <button data-dismiss="modal" class="btn btn-default" type="button">{{ trans('login.cancel');}}</button>
                                    <button class="btn btn-theme" type="submit">{{ trans('login.submit');}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- modal -->



            </div>
        </div>

        <!-- js placed at the end of the document so the pages load faster -->
        
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
        <script src="<?php echo asset_url(); ?>/web/js/bootstrap.min.js"></script>


        <!--BACKSTRETCH-->
        <!-- You can use an image of whatever size. This script will stretch to fit in any screen size.-->
        <script type="text/javascript" src="<?php echo asset_url(); ?>/web/js/jquery.backstretch.min.js"></script>
        <script>
                                        $.backstretch("<?php echo asset_url(); ?>/web/img/bh.jpg", {speed: 500});
        </script>


        <script src="<?php echo asset_url(); ?>/web/js/bootstrap-tour.min.js"></script>
        <script type="text/javascript">
                                        var tour = new Tour(
                                                {
                                                    name: "userappLogin",
                                                });

                                        // Add your steps. Not too many, you don't really want to get your users sleepy
                                        tour.addSteps([
                                            {
                                                // title: "Sign in as a User",
                                                // content: "Enter your login credentials and Click on signin button",
                                                title: "{{trans("login.enter_user");}}",
                                                content: "{{trans("login.enter_user_message");}}",
                                            },
                                        ]);

                                        // Initialize the tour
                                        tour.init();

                                        // Start the tour
                                        tour.start();
        </script>



    </body>
</html>
