<!DOCTYPE html>
<html>
	<!-- START Head -->
	<head>
		<?php $theme = Theme::all(); ?>
		<!-- START META SECTION -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="author" content="pampersdry.info">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
		<title><?= $title ?> | <?= Config::get('app.website_title') ?> {{trans('blade.web_panel_control')}}</title>

		<?php
		$adminPermission = Session::get('adminPermission');

		$array[] = array();

		foreach ($adminPermission as $permission) {
			$array[] = $permission->permission_id;
		}

		$theme = Theme::first();
		$active = '#000066';
		$logo = '/image/logo.png';
		$favicon = '/image/favicon.ico';
		if ($theme) {
			$active = $theme->active_color;
			$favicon = '/uploads/' . $theme->favicon;
			$logo = '/uploads/' . $theme->logo;
		}
		if ($logo == '/uploads/') {
			$logo = '/image/logo.png';
		}
		if ($favicon == '/uploads/') {
			$favicon = '/image/favicon.ico';
		}
		
		?>

		<link rel="icon" type="image/ico" href="<?php echo asset_url(); ?><?php echo $favicon; ?>">

		<link href="<?php echo asset_url(); ?>/admins/bootstrap.min.css" rel="stylesheet" type="text/css" />
	
		<link href="<?php echo asset_url(); ?>/admins/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<!-- Ionicons -->

		<link href="<?php echo asset_url(); ?>/admins/ionicons.min.css" rel="stylesheet" type="text/css" />

		<!-- Theme style -->
		<link href="<?php echo asset_url(); ?>/admins/css/AdminLTE.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo asset_url(); ?>/admins/css/custom-admin.css" rel="stylesheet" type="text/css" />

		<!-- select2 -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />  

		<!-- Croppie -->
        <link href="<?php echo asset_url(); ?>/web/css/croppie.css" rel="stylesheet" />  

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
		<!-- <script src="<?php echo asset_url(); ?>/web/js/jquery.js"></script>  -->
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>
		<script src="<?php echo asset_url(); ?>/admins/js/validator/jquery.validate.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
        <script src="<?php echo asset_url(); ?>/web/js/croppie.js"></script>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->

		<style type="text/css">
			.error{
				color:red;
			}
		</style>
		<script src="<?php echo asset_url(); ?>/web/js/validation.js"></script>
		<script src="<?php echo asset_url(); ?>/javascript/jquery.mask.min.js" type="text/javascript"></script>
	</head>

	<body class="skin-blue" >
		<!-- header logo: style can be found in header.less -->
		<header class="header">
			<a  class="logo" href="{{ URL::Route('AdminMapview') }}" >
				<!-- Add the class icon to your logo image or logo icon to add the margining -->
				<img src="<?php echo asset_url(); ?><?php echo $logo; ?>"  width="140" height="40"> 
			</a>
			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top" role="navigation">
				<!-- Sidebar toggle button-->
				<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">{{trans('blade.nav')}}</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<div class="navbar-right">
					<ul class="nav navbar-nav">

						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="glyphicon glyphicon-user"></i>
								<span>{{trans('customize.Admin'); }}</span>
							</a>
							<ul class="dropdown-menu">

								<!-- Menu Body -->
								<?php if(in_array("14", $array)) { ?>
								<li class="user-body">
									<div class="col-xs-12 text-center">
										<a href="{{ URL::Route('AdminAdmins') }}">{{trans('customize.admin_control'); }}</a>
									</div>

								</li>
								<?php } ?>
								<!-- Menu Footer-->
								<li class="user-footer">
									<div class="col-md-12">
										<a class="btn btn-default btn-flat btn-block" href="{{ URL::Route('AdminLogout') }}">{{trans('customize.log_out'); }}</a>
									</div>

								</li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		</header>

		<div class="wrapper row-offcanvas row-offcanvas-left">
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="left-side sidebar-offcanvas">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
					<ul class="sidebar-menu">


						<li id="dashboard" title="{{trans('customize.Dashboard'); }}">
							<a href="{{ URL::Route('AdminReport') }}"><i class="fa fa-dashboard"></i> <span>{{trans('customize.Dashboard'); }}</span></a>
						</li>

						@if(in_array("1", $array))
						<li id="map-view" title="{{trans('customize.map_view'); }}">
							<a href="{{ URL::Route('AdminMapview') }}"><i class="fa fa-map-marker"></i> <span>{{trans('customize.map_view'); }}</span></a>
						</li>
						@endif
						
						<?php if(in_array("2", $array)) { ?>
						<li id="providers" title="{{trans('customize.Provider').'s'; }}" >
							<a href="{{ URL::Route('AdminProviders') }}"><i class="fa fa-users"></i> <span>{{trans('customize.Provider').'s'; }}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("3", $array)) { ?>
						<li id="walks" title="{{ trans('customize.Request'); }}">
							<a href="{{ URL::Route('AdminRequests') }}"><i class="fa fa-location-arrow"></i> <span>{{ trans('customize.Request'); }}</span></a>
						</li>
						<?php } ?>
						<!-- <li id="schedule" title="Schedule">
							<a href="{{ URL::Route('AdminSchedule') }}"><i class="fa fa-calendar"></i> <span>{{ trans('customize.Schedules').'s'; }}</span></a>
						</li> -->
						<?php if(in_array("4", $array)) { ?>
						<li id="users" title="{{ trans('customize.User').'s'; }}">
							<a href="{{ URL::Route('AdminUsers') }}"><i class="fa fa-users"></i> <span>{{ trans('customize.User').'s'; }}</span></a> 
						</li>
						<?php } ?>
						<?php if(in_array("5", $array)) { ?>
						<li id="reviews" title="{{ trans('customize.Reviews'); }}">
							<a href="{{ URL::Route('AdminReviews') }}"><i class="fa fa-thumbs-o-up"></i> <span>{{ trans('customize.Reviews'); }}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("6", $array)) { ?>
						<li id="information" title="{{ trans('customize.Information'); }}">
							<a href="{{ URL::Route('AdminInformations') }}"><i class="fa fa-info-circle"></i> <span>{{ trans('customize.Information'); }}</span></a>
						</li> 
						<?php } ?>
						<?php if(in_array("7", $array)) { ?>
						<li id="price-policy" title="{{ trans('user_provider_web.price_policy'); }}">
							<a href="{{ URL::Route('AdminPricePolicy', 0) }}"><i class="fa fa-money"></i> <span>{{ trans('user_provider_web.price_policy'); }}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("8", $array)) { ?>
						<li id="document-type" title="{{ trans('customize.Documents'); }}">
							<a href="{{ URL::Route('AdminDocumentTypes') }}"><i class="fa fa-file-text-o"></i> <span>{{ trans('customize.Documents'); }}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("9", $array)) { ?>
						<li id="promo_code" title="{{ trans('customize.promo_codes'); }}">
							<a href="{{ URL::Route('AdminPromoCodes') }}"><i class="fa fa-gift"></i> <span>{{ trans('customize.promo_codes'); }}</span></a>
						</li>
						<?php } ?>
						
						{{-- link for ngo --}}
						@if(in_array("15", $array)) 
							<li id="ngo" title="ngo">
								<a href="{{ URL::Route('AdminNgo') }}"><i class="fa fa-file-text-o"></i> <span>{{trans('customize.Ngo');}}</span></a>
							</li>
						@endif
						
						<?php if(in_array("10", $array)) { ?>
						<li id="keywords" title="{{ trans('customize.Customize'); }}">
							<a href="{{ URL::Route('AdminKeywords') }}"><i class="fa fa-pencil-square"></i> <span>{{ trans('customize.Customize'); }}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("11", $array)) { ?>
						<li id="payments" title="{{trans('customize.payment_details');}}">
							<a href="{{ URL::Route('AdminPayment') }}"><i class="fa fa-money"></i> <span>{{trans('customize.payment_details');}}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("12", $array)) { ?>
						 <li id="week_statement" title="{{trans('customize.WeekStatement');}}">
							<a href="{{ URL::Route('AdminRequests_payment') }}"><i class="fa fa-bar-chart-o"></i> <span>{{trans('customize.WeekStatement');}}</span></a>
						</li>
						<?php } ?>
						<?php if(in_array("14", $array)) { ?>
                        <li id="templateEmail" title="templateEmail">
                            <a href="{{ URL::Route('EmailTemplates') }}"><i class="fa fa-file-text-o"></i> <span>{{trans('customize.email_template');}}</span></a>
                        </li>
                        <?php } ?>
                        <?php if(in_array("13", $array)) { ?>
                        <li id="settings" title="Setings">
                            <a href="{{ URL::Route('AdminSettings') }}"><i class="fa fa-cogs"></i> <span>{{trans('customize.Settings');}}</span></a>
                        </li>
                        <?php } ?>

					</ul>
				</section>
				<!-- /.sidebar -->
			</aside>

			<!-- Right side column. Contains the navbar and content of the page -->
			<aside class="right-side">

				<section class="content-header">
					<h1>
						<?= $title ?>
					</h1>

				</section>

				<!-- Main content -->
				<section class="content">
					<!-- sucess or error msgs-->
					@include('messages')

					@yield('content')
				</section><!-- /.content -->
			</aside><!-- /.right-side -->
		</div><!-- ./wrapper -->
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js" type="text/javascript"></script>
		
		<!-- AdminLTE App -->
		<script src="<?php echo asset_url(); ?>/admins/js/AdminLTE/app.js" type="text/javascript"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="<?php echo asset_url(); ?>/admins/js/AdminLTE/demo.js" type="text/javascript"></script>

		<script type="text/javascript">
			$("#<?= $page ?>").addClass("active");
			$('#option3').show();
			$('.fade').css('opacity', '1');
			$('.nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus').css('color', '#ffffff');
			$('.nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus').css('background-color', '<?php echo $active; ?>');
		</script>

		<script>
			$(function () {

				$("#start-date").datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1,
					onClose: function (selectedDate) {
						$("#end-date").datepicker("option", "minDate", selectedDate);
					}
				});
				$("#end-date").datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 1,
					onClose: function (selectedDate) {
						$("#start-date").datepicker("option", "maxDate", selectedDate);
					}
				});
			});
		</script>
		<script type="text/javascript">
			$(document).ready(function () {
				$("#myModal").modal('show');
			});
		</script>
	</body>
	<!--/ END Body -->
</html>