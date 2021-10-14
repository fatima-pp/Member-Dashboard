<html>
    <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        .centered {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -50px;
            margin-left: -100px;
        }
    </style>
        <script src="https://credimax.gateway.mastercard.com/checkout/version/53/checkout.js"
                data-error="errorCallback"
                data-cancel="cancelCallback"
                data-timeout="timeoutCallback"
                data-complete="completeCallback"
                data-beforeRedirect="Checkout.saveFormFields"
                data-afterRedirect="Checkout.restoreFormFields">
        </script>
        
        <script type="text/javascript">
            var cancelCallback  = 'https://park-pass.com/cancelPayment';
            var timeoutCallback = 'https://park-pass.com/new';
            var errorCallback   = 'https://park-pass.com/paymentError';
            
            function completeCallback(resultIndicator, sessionVersion) {
                console.log(resultIndicator);
                console.log(sessionVersion);
            }

            Checkout.configure({
                merchant: 'E10862951',
                order: {
                    amount: function() {
                        //Dynamic calculation of amount
                        return 0.010;
                    },
                    currency: 'BHD',
                    description: "BDF Hospital annual parking membership, parking name <?php echo $parkingLot . ', parking space number ' . $parkSpace;?>",
                    id: "<?php echo $invoiceNumber; ?>"
                },
                
                session: {
                    id: "<?php echo $session_id[1]; ?>",
                    version: "<?php echo $session_id[2]?>",
                },
                interaction: {
                    operation: 'PURCHASE', // set this field to 'PURCHASE' for Hosted Checkout to perform a Pay Operation.
                    merchant: {
                        name: 'ParkPoint WLL',
                        address: {
                            line1: 'Office 1602, Entrance 614, Road 1011',
                            line2: 'Sanabis 0410, Capital Governorate.'            
                        }    
                    },
                }
            });
        </script>
    </head>
    <body>
        <div class="centered">
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </body>

    <script>
        window.onload = function() {
            Checkout.showPaymentPage();
		}  
    </script>

</html>