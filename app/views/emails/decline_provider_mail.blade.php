<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<style media="screen" type="text/css">.ExternalClass * {line-height: 100%}

            /* Début style responsive (via media queries) */

            @media only screen and (max-width: 480px) {
                *[id=email-penrose-conteneur] {width: 100% !important;}
                table[class=resp-full-table] {width: 100%!important; clear: both;}
                td[class=resp-full-td] {width: 100%!important; clear: both;}
                img[class="email-penrose-img-header"] {width:100% !important; max-width: 340px !important;}
            } 

            /* Fin style responsive */
</style>
<div align="center" style="background-color:#ecf0f1;"><!-- Début en-tête -->
<table align="center" border="0" cellpadding="0" cellspacing="0" id="email-penrose-conteneur" style="padding:20px 0px;" width="660">
	<tbody>
		<tr>
			<td>
			<table align="center" border="0" cellpadding="0" cellspacing="0" class="resp-full-table" width="660">
				<tbody>
					<tr>
						<td style="text-align:left;" width="50%"><a href="{{ Settings::getProviderUrl() }}" style="text-decoration:none;"><img src="http://pifouapp.com.br/pifou/web/img/assets/logo.png" style="width: 160px;" /></a></td>
						<td style="text-align:right;" width="50%">
						<h5 style="font-size: 20px;font-family: 'Helvetica Neue', helvetica, arial, sans-serif;font-weight: bold;color: #6B6B6B;margin: 0;"><!--?php echo date("d-m-Y"); ?--></h5>

						<table align="right" border="0" cellpadding="0" cellspacing="0">
							<tbody>
								<tr>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
<!-- Fin en-tête -->

<table align="center" border="0" cellpadding="0" cellspacing="0" id="email-penrose-conteneur" style="border-right:1px solid #e2e8ea; border-bottom:1px solid #e2e8ea; border-left:1px solid #e2e8ea; background-color:#ffffff;" width="660"><!-- Début bloc "mise en avant" -->
	<tbody>
		<tr>
			<td style="background-color:#ffffff">
			<table align="center" border="0" cellpadding="0" cellspacing="0" class="resp-full-table" width="660">
				<tbody>
					<tr>
						<td class="resp-full-td" style="padding:20px; text-align:center;" valign="top"><span style="font-size:25px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#ffffff"><a href="{{ web_url() }}/provider/signin" style="color:#545454; outline:none; text-decoration:none;">{{trans('email.fail_driver')}} <?php echo ucwords(Config::get('app.website_title')) ?> </a></span></td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<!-- Début article 1 -->
		<tr>
			<td style="border-bottom: 1px solid #e2e8ea">
			<table align="center" border="0" cellpadding="0" cellspacing="0" class="resp-full-table" style="padding:20px;" width="660">
				<tbody>
					<tr>
						<td width="100%">
						<table align="right" border="0" cellpadding="0" cellspacing="0" class="resp-full-table" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" width="100%">
							<tbody>
								<tr>
									<td class="resp-full-td" style="text-align : justify;" valign="top" width="100%">
									<div style="padding: 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;text-align:center;">{{ $vars['provider_name'] }}, infelizmente seu cadastro no PifouApp foi rejeitado pelos administradores. Entre em contato para maiores esclarecimentos.</div>
									</td>
								</tr>
								<tr>
									<td class="resp-full-td" style="text-align : justify;" valign="top" width="100%">
									<div style="padding: 10px;font-size:12px; font-family:'Helvetica Neue', helvetica, arial, sans-serif; font-weight:100; color:#545454;text-align:center;">  {{trans('email.any_question')}} <meta charset="utf-8" /><meta charset="utf-8" /><a href="mailto:contato@pifouapp.com.br"><b id="docs-internal-guid-89f63cbe-939f-adee-cee6-89e76a420f69">contato@pifouapp.com.br</b></a></div>
									</td>
								</tr>
							</tbody>
						</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
		<!-- Fin article 1 -->
	</tbody>
</table>
<!-- Début footer --><!-- Fin footer --></div>
