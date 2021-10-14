<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml"
style="font-family:&quot;Montserrat&quot;sans-serif; margin: 0; padding: 0;">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- If you delete this meta tag, Half Life 3 will never be released. -->
    <meta name="viewport" content="width=device-width" />							
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>ParkPass</title>					
<style>
    :root{
	    --peach: #d3564c;
        --white:#fff;
        --blue:#054168;
    }
    img {
        max-width: 100%;
        width:12.5rem !important;
        height:auto !important;
    }

    body {
        -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%;
        font-family: 'Montserrat' !important;
        /* display:flex !important;
        flex-direction:column !important;
        justify-content:space-between !important; */
        height:100vh !important;
    }

    p{
        font-size:1rem !important;
        font-family:'Montserrat',sans-serif;  
    }

    @media only screen and (max-width: 600px) {
        a[class="btn"] {
            display: block !important; margin-bottom: 10px !important; background-image: none !important; margin-right: 0 !important;
        }
        div[class="column"] {
            width: auto !important; float: none !important;
        }
        table.social div[class="column"] {
            width: auto !important;
        }
        p{
            font-size:16px !important;
        }
    }

    .text-start-align{
        text-align:start !important;
    }
    .text-end-align{
        text-align:start !important;
    }

    .text-start-align-arabic{
        text-align:end !important;
    }
    .text-end-align-arabic{
        text-align:end !important;
    }

    .arabic-section{
        text-align:end !important;
    }
    .sub-centre{
        margin-left: auto !important;
        margin-right: auto !important;
    }
    .sub-btn-color{
        background: #054168 !important;
        width: fit-content !important;
        display: flex !important;
        /* height: 1.25rem!important; */
        margin-bottom: 5% !important;
        text-decoration: none !important;
        color: white !important;
        font-size: larger !important;
        font-weight: 500 !important;
        padding: 1rem 4rem !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50px !important;
    }

    .header-row{
        margin:0;
        padding: 0;
        display: flex;
        justify-content: space-between !important;
        align-items: end !important;
        width: 100% !important;
    }
    .header-label{
        font-weight:500 !important;
        margin:0 !important;
    }

    .seperator{
        margin:0 !important;
    }

    .btn-holder{
        margin:3.5rem auto;
    }

    .priv-name{
        font-weight:600 !important;
    }

    .m_4607128639904173143header-row{
        justify-content:space-between !important;
    }

    @media only screen and (min-width: 768px) {
        .email_ver_tbl{
            width: 50%!important;
            height: 67px !important;
        }
        .email_ver_tbody{
            padding-right: 0;
            vertical-align: middle;
            height: 67px !important;
        }

        .email_ver_td{
            padding-right: 0;
            vertical-align: middle;
            height: 67px !important;
        }
    }

    @media only screen and (min-width: 1024px) {
        .container{
            width: 65% !important;
            margin: 0 auto !important;
        }

        .header-label{
            font-size: 1.5rem !important;
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 1024px) {
        .header-label{
            font-size: 1rem !important;
        }
    }

    @media only screen and (min-width: 320px) and (max-width: 425px) {
        .logo-img{
            height: inherit !important;
        }

        .email_ver_tbody{
            vertical-align: baseline;
        }

        .email_ver_td{
            vertical-align: baseline;
        }
    }

    @media only screen and (min-width: 425px) {
        .logo-img{
            height: 3rem  !important;
        }
    }



    
</style>
</head>
    
<body bgcolor="#fff" style="-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; margin: 0; padding: 0;">

    <!-- HEADER -->
    <!-- <table class="head-wrap" bgcolor="#fff" style="width: 100%; margin: 0; padding: 0;">
        <tr style="margin: 0; padding: 0;">
            <td style="margin: 0; padding: 0;"></td>
            <td class="header container" style="display: block !important; width: 100% !important !important; clear: both !important; margin: 0 5rem; padding: 0;">
                <div class="content" style="width: 100% !important; display: block; margin: 0 auto; padding: 0;">
                    <table style="width: 100%; margin: 0; padding: 0;">
                        <tr class="header-row" style="width:89% !important;padding: 15px 5rem">
                            <td style="margin: 0; padding: 0;width:50% !important">
                                <a href="<?php echo base_url('sign_in') ?>">
                                    <img src="https://park-pass.com/src/new-images/ParkPass_Black_Original_Expanded-01.png" alt="ParkPass Logo" style="width: 200px; display:flex;max-width: 100%; padding: 0;">
                                </a>
                            </td>
                            <td style="width:50% !important">
                                <h2 class="header-label" style="text-align:right !important;">Email Verification</h2>
                            </td>
                        </tr>
                    </table>
                </div>        
            </td>
            <td style="margin: 0; padding: 0;"></td>
        </tr>
    </table>

    <hr class="seperator"> -->

    <!-- /HEADER -->	
    
    
    <!-- header 2 -->
    <div class="container">
        <table class="body-wrap" bgcolor="#fff" style="font-family:&quot;Montserrat&quot;,sans-serif; width: 100%; margin: 0; padding: 0;">
            <tr>
                <td>
                    <div style="background: #fff none no-repeat center/cover;background-color: #fff;background-image: none;background-repeat: no-repeat;background-position: center;background-size: cover;border-top: 0;border-bottom: 0;padding-top: 45px;padding-bottom: 63px;	padding-left:0 !important;padding-right:0 !important;">
                        <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse;width:50% !important;" class="email_ver_tbl">
                            <tbody>
                                <tr>
                                    <td valign="top" style="padding-right:10px;padding-bottom:9px">	
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                            <tbody>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:9px">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" valign="middle" width="100% !important;">
                                                                         <a href="<?php echo base_url('sign_in') ?>">
                                                                            <img src="https://park-pass.com/src/new-images/ParkPass_Black_Original_Expanded-01.png" alt="ParkPass Logo" style="width:100% !important;display:flex;max-width: 100%;height: 3rem;" class="logo-img">
                                                                        </a>
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
                            </tbody>
                        </table>                                                                           
                                                                        
                        <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse;width:50% !important;">
                            <tbody style="float:right !important;" class="email_ver_tbody">
                                <tr>
                                    <td valign="top" style="padding-right:0;padding-bottom:9px" class="email_ver_td">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                            <tbody>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:9px">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" valign="middle" width="100%">
                                                                        <h2 class="header-label" style="text-align:right !important;">Membership Renewal</h2>
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
                            </tbody>
                        </table> 
                    </div>
                </td>
            </tr>
        </table>
        <!-- /header 2 -->
        <hr class="seperator">

        <!-- BODY -->
        <table class="body-wrap" bgcolor="#fff" style="font-family:&quot;Montserrat&quot;,sans-serif; width: 100%; margin: 0; padding: 0;">
            <tr style="font-family:&quot;Montserrat&quot;,sans-serif; margin: 0; padding: 0;">
                <td class="container" bgcolor="#FFFFFF" style="font-family:&quot;Montserrat&quot;,sans-serif; display: block !important; width: 100% !important !important; clear: both !important; margin: 0; padding: 0;">

                    <div class="content" style="font-family:&quot;Montserrat&quot;,sans-serif; width: 100% !important; display: block; margin: 0; padding: 15px 0;">
                        <table style="font-family:&quot;Montserrat&quot;,sans-serif; width: 100%; margin: 0; padding: 0;">
                            <tr style="font-family:&quot;Montserrat&quot;,sans-serif; margin: 0; padding: 0;">
                                <td style="font-family:&quot;Montserrat&quot;,sans-serif; margin: 0; padding: 0;">
                                    <br/>

                                    <!-- <h1>Verify Your Email</h1> -->
                                    <p ><?php echo 'Dear Mr.' . $first_name .'!';?>, </p> 
                                    <p>	Greetings from ParkPass! </p> 

                                    <p>Thank you for renewing your membership with ParkPoint at Bahrain Defence Force Royal Medical Services Hospital.</p>

                                    <p>We hope you have a great experience ahead with our premier services at ParkPoint.</p>
                                    <p>Your Membership Details:</p>
                                    <br>
                                    <p>To reset your password, please click the button below within 24 hours.</p>

                                    <br>
                                    
                                    <table>
                                            <tr>
                                                <th class="text-start-align">Name</th>
                                                <td></td>
                                                <td  class="text-end-align"> <?php echo $first_name;?> <?php echo $last_name;?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Email</th>
                                                <td></td>
                                                <td class="text-end-align"><?php echo $email;?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Mobile</th>
                                                <td></td>
                                                <td class="text-end-align"><?php echo $mobile_number;?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Start Date</th>
                                                <td></td>
                                                <td class="text-end-align"><?php echo date('d-M-Y', strtotime($create_date));?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">End Date</th>
                                                <td></td>
                                                <td class="text-end-align"><?php echo date('t-M-Y', strtotime($expiry_date));?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Paid Amount</th>
                                                <td></td>
                                                <td class="text-end-align"><?=  $price; ?> BD</td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Membership Number</th>
                                                <td></td>
                                                <td class="text-end-align"><?php echo $id;?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-start-align">Registered Number Plate</th>
                                                <td></td>
                                                <td class="text-end-align">
                                                <?php foreach($cars as $car) { ?>
                                                    <?php echo $car['car_plate_number']." - ";?>
                                                <?php } ?>
                                                </td>
                                            </tr>
                                    </table> 

                                    <br><br>

                                    <section class="arabic-section">

                                        <p> ،عزيزي العضو </p> 
                                        <p>	!تحية من بارك بوينت </p> 
                                        <p>	. شكرًا  على تجديد عضويتك من  بارك بوينت في <?php 
                                        echo ($location_a_id == '19') ? 'الخدمات الطبية الملكية التابعة لقوة دفاع البحرين' : 
                                        (($location_a_id == '22') ? 'خليج البحرين للتطوير':
                                        (($location_a_id == '32') ? 'ذا ديستريكت':'إسكان'));   
                                        ?></p> 
                                        <p>	.نأمل أن تحظى بتجربة رائعة مع خدماتنا الرائدة في بارك بوينت</p> 
                                        <p> :تفاصيل العضوية  </p> <br>
                                    </section>

                                    <table class="arabic-table" style="width: 100% !important;">
                                        <tr>                                    
                                            <td  class="text-end-align-arabic"><?php echo $first_name;?> <?php echo $last_name;?></td>
                                            <th class="text-start-align-arabic">الاسم</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?php echo $email;?> </td>
                                            <th class="text-start-align-arabic">البريد الإلكتروني</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?php echo $mobile_number;?></td>
                                            <th class="text-start-align-arabic">رقم الموبايل</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?php echo date('d-M-Y', strtotime($create_date)); ?> </td>
                                            <th class="text-start-align-arabic">تاريخ البدء</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?php echo date('d-M-Y', strtotime($expiry_date))?></td>
                                            <th class="text-start-align-arabic">تاريخ الانتهاء</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?=  $price;  ?> BD</td>
                                            <th class="text-start-align-arabic">المبلغ المدفوع </th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic"><?php echo $id;?></td>
                                            <th class="text-start-align-arabic">رقم العضوية</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="text-end-align-arabic">
                                                <?php foreach($cars as $car) { ?>
                                                    <?php echo $car['car_plate_number']." - ";?>
                                                <?php } ?>
                                            </td>
                                            <th class="text-start-align-arabic">رقم اللوحة المسجل</th>
                                            <td></td>
                                        </tr>
                                    </table>    
                                    
                                    <br>

                                                        
                                    <section class="arabic-section">
                                    <p> .الرجاء زيارة مكتب بارك بوينت للعضوية لتغيير رقم اللوحة الخاص بك </p>                                
                                    </section>

                                    <p> For more information and inquiries, please contact us on +973- 38888231 or info@park-pass.com </p>
                                    <p> Sincerely, ParkPass</p>
                                    <p> ParkPass </p>

                                    <br/>

                                </td>
                            </tr>
                        </table>
                    </div><!-- /content -->											  
                </td>
            </tr>

            <tr>
                <td>
                    <div style="background: #333333 none no-repeat center/cover;background-color: #333333;background-image: none;background-repeat: no-repeat;background-position: center;background-size: cover;border-top: 0;border-bottom: 0;padding-top: 45px;padding-bottom: 63px;	padding-left:40%;">
                        <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td valign="top" style="padding-right:10px;padding-bottom:9px">	
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                            <tbody>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:9px">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" valign="middle" width="24">
                                                                        <a href="http://www.facebook.com?utm_source=ParkPoint&amp;utm_campaign=d40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14&amp;utm_medium=email&amp;utm_term=0_baaf53391f-d40d6a8212-" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://www.facebook.com?utm_source%3DParkPoint%26utm_campaign%3Dd40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14%26utm_medium%3Demail%26utm_term%3D0_baaf53391f-d40d6a8212-&amp;source=gmail&amp;ust=1591216534009000&amp;usg=AFQjCNFtMclfvJY1rw8nSh2kcYVV1vwPTA"><img src="https://ci6.googleusercontent.com/proxy/iZE-48sXvszGHh6MUoqCYHnlP8ohfGJI6V1fj23YRaJHEaKjOb2V7stez03tl97kcCY9ebD52HlFfqGKcTQbPlQaysAL26ZKjUSa5NGX7CU3WUodCbzb-vFMkIXxvIREY4PT879oIw=s0-d-e1-ft#https://cdn-images.mailchimp.com/icons/social-block-v2/outline-light-facebook-48.png" alt="ParkPointParkingSolutions" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a>
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
                            </tbody>
                        </table>                                                                           
                        <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td valign="top" style="padding-right:10px;padding-bottom:9px">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                            <tbody>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:9px">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" valign="middle" width="24">
                                                                        <a href="http://www.instagram.com/Parkpoint?utm_source=ParkPoint&amp;utm_campaign=d40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14&amp;utm_medium=email&amp;utm_term=0_baaf53391f-d40d6a8212-" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://www.instagram.com/Parkpoint?utm_source%3DParkPoint%26utm_campaign%3Dd40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14%26utm_medium%3Demail%26utm_term%3D0_baaf53391f-d40d6a8212-&amp;source=gmail&amp;ust=1591216534009000&amp;usg=AFQjCNFyDLxj0kNxgeow9c0TILoALuiXzw"><img src="https://ci5.googleusercontent.com/proxy/Ihh9hEwk_36d3lzL_tLmGaqmGhc-dLqZP-II9LpKgUDCh37Kvw1N4-DJsrxuyAA9V1NNx3975BQO5w7DNVWvFHpPM4gkDm8eMVCLYy_PtGWEZAxMuaULgOR-6W0K_1sgXOcwNMtgGVE=s0-d-e1-ft#https://cdn-images.mailchimp.com/icons/social-block-v2/outline-light-instagram-48.png" alt="Link" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a>
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
                            </tbody>
                        </table>                                                                           
                        <table align="left" border="0" cellpadding="0" cellspacing="0" style="display:inline;border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td valign="top" style="padding-right:0;padding-bottom:9px">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse">
                                            <tbody>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding-top:5px;padding-right:10px;padding-bottom:5px;padding-left:9px">
                                                        <table align="left" border="0" cellpadding="0" cellspacing="0" width="" style="border-collapse:collapse">
                                                            <tbody>
                                                                <tr>
                                                                    <td align="center" valign="middle" width="24">
                                                                        <a href="http://park-point.com/newsite/?utm_source=ParkPoint&amp;utm_campaign=d40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14&amp;utm_medium=email&amp;utm_term=0_baaf53391f-d40d6a8212-" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://park-point.com/newsite/?utm_source%3DParkPoint%26utm_campaign%3Dd40d6a8212-EMAIL_CAMPAIGN_2020_06_02_07_14%26utm_medium%3Demail%26utm_term%3D0_baaf53391f-d40d6a8212-&amp;source=gmail&amp;ust=1591216534009000&amp;usg=AFQjCNGeGkfakU8k13JaFqANks7Q8HaaUg"><img src="https://ci6.googleusercontent.com/proxy/uZ0yuxmORppOSAVlAI9An9dTGgd5WLSQ0CBL7MLu_J4uk8Z1QO7RWFmdlkUYkmd_GLhwph5RoVCp9eKrXzEQnDQ91cNlGygasb_4p2fT-TnBvWoJAX8mqJXeyuG36Kto6QrY=s0-d-e1-ft#https://cdn-images.mailchimp.com/icons/social-block-v2/outline-light-link-48.png" alt="Website" style="display:block;border:0;height:auto;outline:none;text-decoration:none" height="24" width="24" class="CToWUd"></a>
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
                            </tbody>
                        </table> 
                    </div>
                </td>
            </tr>
        </table>
    </div>
        <!-- /BODY -->							
</body>
</html>