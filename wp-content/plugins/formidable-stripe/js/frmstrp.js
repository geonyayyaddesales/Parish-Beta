function frmStrpProcessJS(){

	var thisForm = false;
	var event = false;

	function validateForm( e ) {
		var formID, action, saveDraft, isDraft, ccField;

		thisForm = this;

		formID = jQuery(thisForm).find('input[name="form_id"]').val();
		if ( formID == frm_stripe_vars.form_id ) {
			action = jQuery(thisForm).find('input[name="frm_action"]').val();
			saveDraft = savingDraft( thisForm );
			isDraft = ( 'update' === action && saveDraft === '' );

			if ( ( 'create' === action && saveDraft !== 1 ) || isDraft ) {
				ccField = jQuery(thisForm).find('.frm_cc_number input');
				if ( ccField.length && ! ccField.is(':hidden') && ccField.val() !== '' ) {
					e.preventDefault();
					event = e;
					processForm( ccField );
					return;
				}
			}
		}

		if ( typeof frmFrontForm.submitFormManual === 'function' ) {
			frmFrontForm.submitFormManual( e, thisForm );
		}else{
			thisForm.submit();
		}

		return false;
	}

	function savingDraft( thisForm ) {
		var isDraft = false;
		if ( typeof frmProForm === 'undefined' ) {
			isDraft = frmFrontForm.savingDraft(thisForm);
		} else{
			isDraft = frmProForm.savingDraft(thisForm);
		}
		return isDraft;
	}

	function processForm( ccField ) {
		var $form = jQuery(thisForm);

		// disable the submit button to prevent repeated clicks
		if ( typeof frmFrontForm.showSubmitLoading === 'function' ) {
			frmFrontForm.showSubmitLoading( $form );
		} else {
			$form.find('input[type="submit"],input[type="button"],button[type="submit"]').attr('disabled','disabled');
		}

		var cardObject = {
			number: ccField.val(),
			cvc: $form.find('.frm_cc_cvc input').val(),
			exp_month: $form.find('.frm_cc_exp_month select').val(),
			exp_year: $form.find('.frm_cc_exp_year select').val()
		};

		cardObject = addNameAndAddress( cardObject, $form );

		// send the card details to Stripe
		Stripe.createToken(cardObject, responseHandler);

		// prevent the form from submitting with the default action
		return false;
	}

	function addNameAndAddress( cardObject, $form ) {
		var settings = frm_stripe_vars.settings;
		var addressID = '';
		var firstNameID = '';
		var lastNameID = '';

		for ( var i = 0; i < settings.length; i++ ) {
			if ( jQuery.inArray('stripe', settings[i].gateways) !== -1 ) {
				addressID = settings[i].address;
				firstNameID = settings[i].first_name;
				lastNameID = settings[i].last_name;
			}
		}

		if ( addressID !== '' ) {
			var addressContainer = $form.find('#frm_field_'+addressID+'_container');
			if ( addressContainer.length ) {
				cardObject = addValToRequest( addressContainer, 'line1', cardObject, 'address_line1' );
				cardObject = addValToRequest( addressContainer, 'line2', cardObject, 'address_line2' );
				cardObject = addValToRequest( addressContainer, 'city', cardObject, 'address_city' );
				cardObject = addValToRequest( addressContainer, 'state', cardObject, 'address_state' );
				cardObject = addValToRequest( addressContainer, 'zip', cardObject, 'address_zip' );
				cardObject = addValToRequest( addressContainer, 'country', cardObject, 'address_country' );
			}
		}

		if ( firstNameID !== '' ) {
			var firstField = $form.find('#frm_field_'+firstNameID+'_container input');
			if ( firstField.length && firstField.val() ) {
				cardObject.name = firstField.val();
			}
		}

		if ( lastNameID !== '' ) {
			var lastField = $form.find('#frm_field_'+lastNameID+'_container input');
			if ( lastField.length && lastField.val() ) {
				cardObject.name = cardObject.name +' '+ lastField.val();
			}
		}

		return cardObject;
	}

	function addValToRequest( container, inputName, cardObject, objectName ) {
		var input = container.find('input[name$="['+inputName+']"]');
		if ( input.length && input.val() ) {
			cardObject[objectName] = input.val();
		}
		return cardObject;
	}

	function responseHandler(status, response) {
		// re-enable the submit button
		if ( typeof frmFrontForm.removeSubmitLoading === 'function' ) {
			frmFrontForm.removeSubmitLoading( jQuery(thisForm), 'enable', 0 );
		} else {
			jQuery(thisForm).find('input[type="submit"],input[type="button"],button[type="submit"]').attr('disabled',false);
		}

	    if (response.error) {
			// show errors returned by Stripe
			addError(response.error);
	    } else {
	        // token contains id, last4, and card type
	        var token = response.id;
	        // insert the token into the form so it gets submitted to the server
	        jQuery(thisForm).append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
	        // and submit
			if ( typeof frmFrontForm.submitFormManual == 'function' ) {
				frmFrontForm.submitFormManual( event, thisForm );
			}else{
				jQuery(thisForm).get(0).submit();
			}
	    }
	}

	function addError(error) {
		var param = error.param;
		if ( param === '' || typeof param == 'undefined' ) {
			param = 'number';
		}

		var $fieldCont = jQuery(thisForm).find('.frm_cc_' + param);

		if ( $fieldCont.length ) {
			jQuery('.form-field').removeClass('frm_blank_field');
			jQuery('.form-field .frm_error').replaceWith('');

			$fieldCont.addClass('frm_blank_field');
			$fieldCont.append( '<div class="frm_error">'+ error.message +'</div>' );

			frmFrontForm.scrollMsg( $fieldCont, thisForm, true );
		}
	}

	function deleteCard() {
		var button = this;
		var cardId = button.dataset.cid;
		jQuery.ajax({
		    url: frm_stripe_vars.root +'frm-strp/v1/card/'+ cardId,
		    method: 'DELETE',
			dataType: 'json',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', frm_stripe_vars.api_nonce );
			},
		    success: function(result) {
				if ( result.success == true ) {
					jQuery(button).closest('tr').fadeOut();
				} else {
					button.innerHTML = result.error;
				}
			}
		});
	}

	return{
		init: function(){
			Stripe.setPublishableKey(frm_stripe_vars.publishable_key);
			jQuery(document).off( 'submit.formidable', '.frm-show-form' );
			jQuery(document).on( 'submit.frmstrp', '.frm-show-form', validateForm );

			jQuery('button.frm-stripe-delete-card').click( deleteCard );
		}
	};
}

var frmStrpProcess = frmStrpProcessJS();

jQuery(document).ready(function($){
	frmStrpProcess.init();
});
