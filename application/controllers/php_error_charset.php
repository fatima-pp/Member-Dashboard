A PHP Error was encountered
Severity: Warning

Message: mysqli::real_connect(): Server sent charset (255) unknown to the client. Please, report to the developers

Filename: mysqli/mysqli_driver.php

Line Number: 201

Backtrace:

File: /var/www/html/application/third_party/MX/Loader.php
Line: 107
Function: DB

File: /var/www/html/application/modules/Zain/models/Zain_model.php
Line: 57
Function: database

File: /var/www/html/application/modules/Zain/controllers/Zain.php
Line: 44
Function: get_client_info

File: /var/www/html/index.php
Line: 315
Function: require_once

A PHP Error was encountered
Severity: Warning

Message: mysqli::real_connect(): (HY000/2054): Server sent charset unknown to the client. Please, report to the developers

Filename: mysqli/mysqli_driver.php

Line Number: 201

Backtrace:

File: /var/www/html/application/third_party/MX/Loader.php
Line: 107
Function: DB

File: /var/www/html/application/modules/Zain/models/Zain_model.php
Line: 57
Function: database

File: /var/www/html/application/modules/Zain/controllers/Zain.php
Line: 44
Function: get_client_info

File: /var/www/html/index.php
Line: 315
Function: require_once


Fatal error: Call to a member function real_escape_string() on boolean in /var/www/html/system/database/drivers/mysqli/mysqli_driver.php on line 391
A PHP Error was encountered
Severity: Error

Message: Call to a member function real_escape_string() on boolean

Filename: mysqli/mysqli_driver.php

Line Number: 391

Backtrace: