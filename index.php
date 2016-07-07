<?php 

error_reporting(E_ALL); ini_set('display_errors', 1);

include('./assets/helpers/functions.php'); 

?>
<!DOCTYPE html> 
<html lang="en-US">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
        <!-- Title & Description: Change the title and description to suit your needs. -->
        <title><?php if(isset($_GET['client']) && isset($_GET['invoice'])) { ?><?php if(get_client('client') && get_invoice('invoice')) { ?>Invoice <?php get_invoice('number'); ?> | <?php echo(get_invoice('status')); ?><?php } else { ?>Sorry, no invoices here.<?php } ?><?php } else { ?><?php echo($business_name); ?><?php } ?></title>
        <meta name="description" content="<?php echo($business_name); ?>">
        
        <!-- Viewport Meta: Just taming mobile devices like iPads and iPhones. -->
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0"/>
        
        <!-- Styles: The primary styles for this template. -->
        <link rel="stylesheet" type="text/css" href="assets/styles/normalize.css">
        <link rel="stylesheet" type="text/css" href="assets/styles/main.css?ver=1.0">
        <link rel="stylesheet" type="text/css" href="assets/styles/responsive.css?ver=1.0">
        
        <!-- Favicon: Change to whatever you like within the “assets/images” folder. -->
        <link rel="shortcut icon" type="" href="assets/images/favicon.png">
        
        <!-- Required Scripts: Not too much needed for this template. -->
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.0.3.min.js"></script>
        <script src="https://js.stripe.com/v2/" type="text/javascript"></script>
        <script type="text/javascript" src="assets/scripts/main.js"></script>
        <?php include('./assets/scripts/stripe.php'); ?>
    </head>

    <body>
        <?php if(isset($_GET['client']) && isset($_GET['invoice'])) { ?>
        <?php if(get_client('client') && get_invoice('invoice')) { ?>
        <section id="invoice" <?php if(get_invoice('status') != 'Not Paid') { ?>class="paid"<?php } ?>>
            <div class="content">
                <div class="row intro">
                    <?php if($avatar_image) { ?>
                    <img src="assets/images/profile.jpg" />
                    <?php } ?>
                    
                    <h1><?php echo($business_name); ?><br><strong><a href="mailto:<?php echo($business_email); ?>"><?php echo($business_email); ?></a></strong></h1>
                </div>
                
                <div class="row details">
                    <div class="client">
                        <span>To: <?php get_client('name'); ?><br><strong><?php get_client('email'); ?></strong></span>
                    </div>
                    
                    <div class="status">
                        <span>Invoice: <?php get_invoice('number'); ?><br><strong><?php if(get_invoice('status') == 'Not Paid') { ?>Not Paid<?php } else { ?>Paid<?php } ?></strong></span>
                    </div>
                </div>
                
                <div class="row title">
                    <h2>Work Completed</h2>
                </div>
                
                <ul class="row items">
                    <?php get_invoice('items'); ?>
                </ul>
                
                <div class="row title">
                    <h2>Tax &amp; Total</h2>
                </div>
                
                <ul class="row items">
                    <li class="row item">
                        <h3>Subtotal</h3>
                        <span class="subtotal"><?php get_invoice('subtotal'); ?></span>
                    </li>
                    
                    <li class="row item">
                        <h3>Tax</h3>
                        <span class="tax"><?php get_invoice('tax'); ?></span>
                    </li>
                    
                    <li class="row item">
                        <h3><strong>Total <?php if(get_invoice('status') == 'Not Paid') { ?>Due<?php } else { ?>Paid<?php } ?></strong></h3>
                        <span class="total"><?php get_invoice('total'); ?></span>
                    </li>
                </ul>
            </div>
            
            <?php if(get_invoice('status') == 'Not Paid') { ?>
            <div class="actions">
                <div class="content">
                    <span><?php get_invoice('due'); ?></span>
                    <a class="open button" href="#order">Make a <strong>Payment</strong></a> 
                </div>
            </div>
            <?php } ?>
        </section>
        
        <section class="panel closed" id="order">
            <div class="processing">
                <!-- Processing the Payment -->
            </div>
            
            <a class="icon close" href="#order"></a>
            
            <div class="content centered">
                <h2>Make a Payment<br><strong>Invoice: <?php get_invoice('number'); ?></strong></h2>
                <p>Enter your payment information below to pay this invoice. A receipt for your records will be sent to you. Thank you very much!</p>
            
                <form id="purchase-form" autocomplete="on" method="post" action="" novalidate>
                    <input type="hidden" id="invoice_number" name="invoice_number" value="<?php get_invoice('number'); ?>" />
                    <input type="hidden" id="invoice_subtotal" name="invoice_subtotal" value="<?php get_invoice('subtotal'); ?>" />
                    <input type="hidden" id="invoice_tax" name="invoice_tax" value="<?php get_invoice('tax'); ?>" />
                    <input type="hidden" id="invoice_total" name="invoice_total" value="<?php get_invoice('total'); ?>" />
                    <input type="hidden" id="stripe_total" name="stripe_total" value="<?php get_invoice('stripe'); ?>" />
                    
                    <div class="row">
                        <input type="text" id="name" name="name" placeholder="Your Name &#42;" required>
                        <input type="text" id="email" name="email" placeholder="Your Email &#42;" required>
                        <input type="text" id="number" pattern="\d*" autocomplete="number" placeholder="Credit Card Number &#42;" required>
                        <input type="text" id="expiration" pattern="\d*" autocomplete="expiration" placeholder="MM / YYYY &#42;" required>
                        <input type="text" id="cvc" pattern="\d*" autocomplete="off" placeholder="CVC &#42;" required>
                        <input type="text" id="zip" autocomplete="off" placeholder="Zip / Postal &#42;" required>
                    </div>
                    
                    <button type="submit" class="button">Pay <strong><?php get_invoice('total'); ?></strong></button>
                </form>
            </div>
        </section>
        
        <section class="panel status closed" id="errors">
            <a class="icon close" href="#errors"></a>
            
            <div class="content centered">
                <span class="icon error"></span>
                <h2>Oops!</h2>
                <p></p>
            </div>
        </section>
        
        <?php if(get_invoice('status') != 'Not Paid') { ?>
        <section class="panel status opened" id="paid">
            <a class="icon close" href="#paid"></a>
            
            <div class="content centered">
                <span class="icon paid"></span>
                <h2><?php echo(get_invoice('status')); ?></h2>
                <p>Thanks for your business.</p>
            </div>
        </section>
        <?php } ?>
        
        <?php } else { ?>
        <section class="panel status opened" id="errors">
            <div class="content centered">
                <span class="icon error"></span>
                <h2>Oops!</h2>
                <p>Sorry, no invoices here.</p>
            </div>
        </section>
        <?php } ?>
        
        <?php } else { ?>
        <section id="brand">
            <div class="content centered">
                <?php if($avatar_image) { ?>
                <img src="assets/images/profile.jpg" />
                <?php } ?>
                <h1><?php echo($business_name); ?><br><strong><a href="mailto:<?php echo($business_email); ?>"><?php echo($business_email); ?></a></strong></h1>
            </div>
        </section>
        <?php } ?>
    </body>
</html>