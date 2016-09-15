<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>{{trans('email.Request_Payment_Charged')}}</title>
        <style type="text/css" media="screen">

            .ExternalClass * {line-height: 100%}

            /* Début style responsive (via media queries) */

            @media only screen and (max-width: 480px) {
                *[id=email-penrose-conteneur] {width: 100% !important;}
                table[class=resp-full-table] {width: 100%!important; clear: both;}
                td[class=resp-full-td] {width: 100%!important; clear: both;}
                img[class="email-penrose-img-header"] {width:100% !important; max-width: 340px !important;}
            } 

            /* Fin style responsive */

        </style>

    </head>
    <body style="background-color:#ecf0f1">
        <div align="center" style="background-color:#ecf0f1;">

            <!-- Début en-tête -->

            <table id="email-penrose-conteneur" width="660" align="center" style="padding:20px 0px;" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="50%" style="text-align:left;">
                                    <a href="{{ Settings::getProviderUrl() }}" style="text-decoration:none;"><img src="<?php echo asset_url(); ?>/prestador/img/assets/logo.png" style="width: 160px;" /></a>
                                </td>
                                <td width="50%" style="text-align:right;">
                                    <table align="right" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                        <h5 style="font-size: 20px;font-family: 'Helvetica Neue', helvetica, arial, sans-serif;font-weight: bold;color: #6B6B6B;margin: 0;"><?php echo date("d-m-Y"); ?></h5>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Fin en-tête -->

<table id="email-penrose-conteneur" width="660" align="center" style="border-right:1px solid #e2e8ea; border-bottom:1px solid #e2e8ea; border-left:1px solid #e2e8ea; background-color:#ffffff;" border="0" cellspacing="0" cellpadding="0">

    <!-- Début bloc "mise en avant" -->

    <tr>
        <td style="background-color:#ffffff">
            <table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="resp-full-td" valign="top" style="padding:20px; text-align:center;">
                        <span style="font-size:25px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#ffffff"><a href="{{ web_url() }}/provider/signin" style="color:#545454; outline:none; text-decoration:none;">{{trans('email.Payment_Done_With')}} {{ $vars['req_id'] }}</a></span>
                    </td>
                </tr>
               

            </table>
        </td>
    </tr>





    <!-- Début article 1 -->

    <tr>
        <td style="border-bottom: 1px solid #e2e8ea">
            <table width="660" class="resp-full-table" align="center" border="0" cellspacing="0" cellpadding="0" style="padding:20px;">
                <tr>
                    <td width="100%">

                        <table width="100%" align="right" class="resp-full-table" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="100%" class="resp-full-td" valign="top" style="text-align : justify;">

                                    <div style="padding: 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;text-align:left;">
                                         {{trans('email.Hi')}}, <strong style="font-weight: bold;">{{ $vars['name'] }}</strong>,
                                        <br>
                                        {{trans('email.Your_payment_with')}} <?php /* echo "App Name"; */ echo ucwords(Config::get('app.website_title')); ?> {{trans('email.is_done_of')}} <strong style="font-weight: bold;">{{ $vars['amount'] }}</strong> {{trans('email.with')}} <strong style="font-weight: bold;">{{ $vars['req_id'] }}</strong>. 
                                        {{trans('email.please_feedback')}}
                                        <br>
                                        <br>
                                        {{trans('email.Thanks')}},
                                        <br>
                                        <?php /* echo "App Name"; */ echo ucwords(Config::get('app.website_title')); ?> {{trans('email.team')}}.
                                    </div>


                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td width="100%" class="resp-full-td" valign="top" style="text-align : justify;">

                                    <div style="padding: 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;text-align:center;">
                                        {{trans('email.any_question')}} {{ $vars['admin_eamil'] }}
                                    </div>
                                   
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Fin article 1 -->

</table>

<!-- Début footer -->


<!-- Fin footer -->

</div>
</body>
</html>