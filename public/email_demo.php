<?php
define("MANDRILL_API_KEY", "");
$email="abc@gmail.com";
$name="abc";
$code="CoDe";
$to = $email;
$from = 'taxi_now@taxinow.xyz';


            $subject = "Your Conformation code for Call a Cab Client Registration";

            $email_message = "<html>";
            $email_message .="<body >";
            $email_message .= "<div style=''>";
            $email_message .="Dear " . $name . ",<br>";
            $email_message .="Thank you for your registration in Call a Cab.<br><br>";
            $email_message .="</div>";
            $email_message .="<h4>Your Conformation code for Call a Cab Client Registration is :";
            $email_message .="<span style='color:#005e20;'> " . $code . " </span>";
            $email_message .="</h4><br>";
            $email_message .= "<div style=''>This mail is generated based on the client registration information</div><br><br>";
            $email_message .= "<div style='font-size: 10px; text-align: center;'>Please ignor this mail if you are already conformed in Call a Cab application.</div>";
            $email_message .= "</body>";
            $email_message .= "</html>";


            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <' . $from . '>' . "\r\n";

            if (MANDRILL_API_KEY == "") {
                $result = mail($to, $subject, $email_message, $headers);
                if ($result) {
                    echo "Mail Sent Successfully";
                } else {
                    echo "Mail Not Sent";
                }
            } else {
                $uri = 'https://mandrillapp.com/api/1.0/messages/send.json';
                $postString = '{
                                "key": "' . MANDRILL_API_KEY . '",
                                "message": {
                                        "html": "' . $email_message . '",
                                        "text": "' . $email_message . '",
                                        "subject": "' . $subject . '",
                                        "from_email": "' . $from . '",
                                        "from_name": "Appdupe",
                                        "to": [
                                                {
                                                        "email": "' . $to . '",
                                                        "name": "DEMO NAME"
                                                }
                                        ],
                                        "headers": {

                                        },
                                        "track_opens": true,
                                        "track_clicks": true,
                                        "auto_text": true,
                                        "url_strip_qs": true,
                                        "preserve_recipients": true,

                                        "merge": true,
                                        "global_merge_vars": [

                                        ],
                                        "merge_vars": [

                                        ],
                                        "tags": [

                                        ],
                                        "google_analytics_domains": [

                                        ],
                                        "google_analytics_campaign": "...",
                                        "metadata": [

                                        ],
                                        "recipient_metadata": [

                                        ],
                                        "attachments": [

                                        ]
                                },
                                "async": false
                                }';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $uri);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);

                $result = curl_exec($ch);
                $phpObj = json_decode($result);

                if ($phpObj[0]->status == "sent") {
                    echo "Mail Sent Successfully Mandrill";
                } else {
                    $result = mail($to, $subject, $email_message, $headers);
					if ($result) {
						echo "Mail Sent Successfully";
					} else {
						echo "Mail Not Sent";
					}
                }
            }
?>