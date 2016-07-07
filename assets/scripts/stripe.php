<?php

include('././settings.php');

?>
<script>
// Stripe form handling.
$(document).ready(function() {

    // Field formatting.
    $('#number').payment('formatCardNumber');
    $('#expiration').payment('formatCardExpiry');
    $('#cvc').payment('formatCardCVC');
    
    // If there are errors, toggle the error class.
    $.fn.toggleInputError = function(erred) {
        this.toggleClass('has-error', erred);
        return this;
    };
    
    // Set the Stripe live publishable key.
    Stripe.setPublishableKey('<?php echo($stripe_publishable_key); ?>');
    
    // The Stripe response handler.
    function stripeResponseHandler(status, response) {
        var $stripeToken = $("input[name='stripeToken']", this);
        
        // If there’s an error.
        if (response.error) {
        
            // Fade out the loader.
            $('.processing').fadeOut();
            
            // Fade in the payment errors.
            $('#errors').removeClass('slide-out closed');
            $('#errors').addClass('opened animated fast fade-in');
            $('#errors p').text(response.error.message);
            
            // Finish up.
            $('button', this).prop('disabled', false);
        
        // If we’re good to go.    
        } else {
        
            // Generate the token.
            if(!$stripeToken.length) {
                $stripeToken = $('<input type="hidden" name="stripeToken" />').appendTo(this);
            }
            
            // Submit the form with the generated token.
            $stripeToken.val(response.id);
            this.submit();
        }
    };
    
    // Stripe form validation.
    $('#purchase-form').submit(function(e) {
        e.preventDefault();
        
        // Fade in the loader.
        $('.processing').fadeIn();
        
        // jQuery Payment validation.
        var cardType = $.payment.cardType($('#number').val());
        $('#number').toggleInputError(!$.payment.validateCardNumber($('#number').val()));
        $('#expiration').toggleInputError(!$.payment.validateCardExpiry($('#expiration').payment('cardExpiryVal')));
        $('#cvc').toggleInputError(!$.payment.validateCardCVC($('#cvc').val(), cardType));
        
        // Stripe validation.
        var expiration = $('#expiration', this).payment('cardExpiryVal');
        Stripe.card.createToken({
            name: $('#name', this).val(),
            number: $('#number', this).val(),
            cvc: $('#cvc', this).val(),
            exp_month: (expiration.month || 0),
            exp_year: (expiration.year || 0),
            address_zip: $('#zip', this).val()
        }, stripeResponseHandler.bind(this));
    });
});
</script>