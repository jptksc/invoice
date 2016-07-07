<?php

/***********************************************************************************
  
Get Required Settings
  
************************************************************************************/

include('././settings.php');

/***********************************************************************************
  
Get the Stripe API
  
************************************************************************************/

include('././assets/helpers/Stripe.php');

/***********************************************************************************
  
Get the Mailer Class
  
************************************************************************************/

include('././assets/helpers/mail.php');


/***********************************************************************************
  
The Get Client Function
  
************************************************************************************/

function get_client($part) {

    // Set the clients directory.
    $clients_directory = '././content/clients/';
    
    // Get the client.
    if(file_exists($clients_directory . $_GET['client'] . '.txt')) {
        $client = file($clients_directory . $_GET['client'] . '.txt');
    }
    
    // If the client exists.
    if($part == 'client') {
        if(file_exists($clients_directory . $_GET['client'] . '.txt')) {
            return true;
        } else {
            return false;
        }
    }
    
    // Display the client name.
    if($part == 'name') {
        echo(str_replace(array("\n"), '', $client[0]));
    }
    
    // Display the client email.
    if($part == 'email') {
        echo(str_replace(array("\n"), '', $client[1]));
    }
}

/***********************************************************************************
  
The Get Invoice Function
  
************************************************************************************/
    
function get_invoice($part) {

    // Extract globals.
    GLOBAL $currency;
    GLOBAL $tax_percentage;
    
    // Set the currency format.
    setlocale(LC_MONETARY, $currency);
    
    // The invoice number.
    $invoice_number = $_GET['invoice'];
    
    // Set the invoices directory.
    $invoices_directory = '././content/invoices/';
    
    // Get the invoice.
    if(file_exists($invoices_directory . $_GET['invoice'] . '.txt')) {
        $invoice_file = $invoices_directory . $invoice_number . '.txt';
        
        // Invoice lines.
        $invoice_lines = file($invoice_file);
    }
    
    // If the invoice exists.
    if($part == 'invoice') {
        if(file_exists($invoices_directory . $_GET['invoice'] . '.txt')) {
            return true;
        } else {
            return false;
        }
    }
    
    // Display the invoice number.
    if($part == 'number') {
        echo($invoice_number);
    }
    
    // Display the invoice due date.
    if($part == 'due') {
        
        // Get the due date.
        $date = str_replace(array("\n"), '', $invoice_lines[0]);
        
        // Calculate days to due date.
        $days = (isset($date)) ? floor((strtotime($date) - time())/60/60/24) : false;
        
        // Display the due in text.
        echo('<strong>Due in ' . $days . ' Days</strong> - ' . $date);
    }
    
    // Display the invoice status.
    if($part == 'status') {
        return(str_replace(array("\n"), '', $invoice_lines[1]));
    }
    
    // Display the invoice items.
    if($part == 'items') {
        
        // Unset the first 3 lines.
        unset($invoice_lines[0]);
        unset($invoice_lines[1]);
        unset($invoice_lines[2]);
    
        // Loop through the invoice items.
        foreach($invoice_lines as $invoice_line => $invoice_items) {   
        
        // Explode invoice items.   
        $invoice_item = explode(' - ', $invoice_items);
        
        ?>
        <li class="row item">
            <h3><span class="description"><?php echo($invoice_item[0]); ?></span><span class="units"><?php echo($invoice_item[1]); ?></span></h3>
            <span class="amount"><?php echo(money_format('%n', str_replace(array("\n"), '', $invoice_item[2]))); ?></span>
        </li>
        <?php }
    }
    
    // Display the subtotal, tax and total.
    if($part == 'subtotal' | $part == 'tax' | $part == 'total' | $part == 'stripe') {
        
        // Unset the first 3 lines.
        unset($invoice_lines[0]);
        unset($invoice_lines[1]);
        unset($invoice_lines[2]);
    
        // Set the subtotal to "0".
        $subtotal = 0;
        
        // Loop through the invoice items.
        foreach($invoice_lines as $invoice_line => $invoice_items) {   
        
            // Explode invoice items.    
            $invoice_item = explode(' - ', $invoice_items);
            
            // Add up the item totals.
            $subtotal += $invoice_item[2];
            
            // The tax.
            $tax = ($tax_percentage / 100) * $subtotal;
            
            // The total.
            $total = $subtotal + $tax;
        } 
        
        if($part == 'subtotal') {
            echo(money_format('%n', $subtotal));
        }
        
        if($part == 'tax') {
            echo(money_format('%n', $tax));
        }
        
        if($part == 'total') {
            echo(money_format('%n', $total));
        }
        
        if($part == 'stripe') {
            echo(number_format($total, 2, '', ''));
        }
    }
}

/***********************************************************************************
  
Sales
  
************************************************************************************/

// Set the Stripe API key.
Stripe::setApiKey($stripe_secret_key);

// Let’s do this thing.
if(isset($_POST['stripeToken'])) {
    
    // Get all of the transaction information.
    $token = $_POST['stripeToken'];
    $transaction_id = str_replace('tok_', '', $token);
    $invoice_number = $_POST['invoice_number'];
    $invoice_subtotal = $_POST['invoice_subtotal'];
    $invoice_tax = $_POST['invoice_tax'];
    $invoice_total = $_POST['invoice_total'];
    $stripe_total = $_POST['stripe_total'];
    $client_name = $_POST['name'];
    $client_email = $_POST['email'];
    
    // Complete the sale.
    try {
        
        // Create the customer.
        $customer = Stripe_Customer::create(array(
            'email' => $client_email,
            'card' => $token
        ));
        
        // Complete the charge.
        $charge = Stripe_Charge::create(array(
            'customer' => $customer->id,
            'amount' => $stripe_total,
            'description' => 'Payment for Invoice ' . $invoice_number,
            'currency' => 'usd'
        ));
        
        // Compile the receipt.
        $mail_subject = 'Thanks for Your Payment - Invoice #' . $invoice_number;
        $mail_message = '<!DOCTYPE html>
        <head>
        	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        </head>
        <body style="background: #f5f5f5; font-family: Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 20px; color: #777777; text-rendering: optimizeLegibility; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; width: auto; height: auto; margin: 0; padding: 0;">
            <div style="width: 520px; margin: 0px auto; overflow: hidden;">
                <div style="border-bottom: 1px solid #ebebeb; width: 520px; float: left; margin: 0 0 6px; padding: 30px 0 20px;"> 
                    <p style="margin: 43px 0 15px 0; padding: 0;">Thank you very much for your payment — please do feel free to email me with any questions you might have (<a href="mailto:' . $business_email . '" style="font-size: 14px; font-weight: bold; color: #555555; text-decoration: none;">' . $business_email . '</a>).</p>
                </div>
                
                <div style="border-bottom: 1px solid #ebebeb; width: 520px; float: left; margin: 0 0 6px; padding: 30px 0 20px;">   
                    <p style="margin: 0 0 5px 0; padding: 0;"><strong>Name</strong>: ' . $client_name . '</p>
                    <p style="margin: 0 0 15px 0; padding: 0;"><strong>Email</strong>: <a href="mailto:' . $client_email . '" style="font-size: 14px; font-weight: bold; color: #555555; text-decoration: none;">' . $client_email . '</a></p>
                </div>
                
                <div style="border-bottom: 1px solid #ebebeb; width: 520px; float: left; margin: 0 0 6px; padding: 30px 0 20px;">     
                    <p style="margin: 0 0 5px 0; padding: 0;"><strong>Payment Date</strong>: ' . date('F jS, Y', strtotime('now'))  . '</p>
                    <p style="margin: 0 0 5px 0; padding: 0;"><strong>Invoice Number</strong>: ' . $invoice_number . '</p>
                    <p style="margin: 0 0 5px 0; padding: 0;"><strong>Subtotal</strong>: ' . $invoice_subtotal . '</p>
                    <p style="margin: 0 0 5px 0; padding: 0;"><strong>Tax</strong>: ' . $invoice_tax . '</p>
                    <p style="margin: 0 0 15px 0; padding: 0;"><strong>Total Payment</strong>: ' . $invoice_total . '</p>
                </div>
                	
            	<div style="border-bottom: 1px solid #ebebeb; width: 520px; float: left; margin: 0 0 6px; padding: 30px 0 0; border: none; font-size: 12px; line-height: 16px; color: #cccccc;">
                    <p style="margin: 0 0 5px 0; padding: 0;">' . $business_name . '</p>
                    <p style="margin: 0 0 70px 0; padding: 0;"><a href="mailto:' . $business_email . '" style="font-size: 14px; font-weight: bold; color: #555555; text-decoration: none;">' . $business_email . '</a></p>
                </div>
            </div>
        </body>';
        
        // Mail the receipt.
        $mail = new SimpleMail();
        $mail->setTo($client_email, $client_name)
             ->setSubject($mail_subject)
             ->setFrom($business_email, $business_name)
             ->addMailHeader('Reply-To', $business_email, $business_name)
             ->addMailHeader('Bcc', $business_email, $business_name)
             ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
             ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
             ->setMessage($mail_message)
             ->setWrap(100);
        $send = $mail->send();
    } 
    
    // If something went wrong.
    catch(Stripe_CardError $e) {
    }
    
    // Update the payment status.
    $status = file_get_contents('././content/invoices/' . $invoice_number . '.txt');
    $status = str_replace('Not Paid', 'Paid on ' . date('F jS, Y'), $status);
    file_put_contents('././content/invoices/' . $invoice_number . '.txt', $status);
}