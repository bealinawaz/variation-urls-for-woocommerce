/**
 * Keeps WooCommerce's attribute query arguments in the address bar while a shopper
 * picks a variation, and copies the link on request.
 */
( function ( $ ) {
	'use strict';

	var settings = window.vurlSettings || {};
	var PREFIX = 'attribute_';
	var statusTimer;

	/**
	 * Removes every attribute argument from a URL object.
	 *
	 * @param {URL} url URL to clean.
	 */
	function stripAttributes( url ) {
		Array.from( url.searchParams.keys() ).forEach( function ( key ) {
			if ( 0 === key.indexOf( PREFIX ) ) {
				url.searchParams.delete( key );
			}
		} );
	}

	/**
	 * Builds the current page URL with the form's chosen attributes, or null when
	 * an attribute is still empty.
	 *
	 * @param {jQuery} $form Variation form.
	 * @return {string|null} URL.
	 */
	function buildUrl( $form ) {
		var url = new URL( window.location.href );
		var complete = true;
		var fields = $form.find( 'select[name^="' + PREFIX + '"], input[type="radio"][name^="' + PREFIX + '"]:checked' );

		stripAttributes( url );
		fields.each( function () {
			var value = $( this ).val();
			if ( ! value ) {
				complete = false;
				return false;
			}
			// URLSearchParams encodes spaces and special characters; WooCommerce decodes them when the page loads.
			url.searchParams.set( this.name, value );
		} );

		return complete && fields.length ? url.toString() : null;
	}

	/**
	 * Replaces the current history entry, so the Back button still leaves the page.
	 *
	 * @param {string} url New URL.
	 */
	function replaceUrl( url ) {
		if ( url && url !== window.location.href ) {
			window.history.replaceState( window.history.state, '', url );
		}
	}

	/**
	 * Whether the address bar already carries attribute arguments.
	 *
	 * @return {boolean} True when it does.
	 */
	function urlHasAttributes() {
		return Array.from( new URL( window.location.href ).searchParams.keys() ).some( function ( key ) {
			return 0 === key.indexOf( PREFIX );
		} );
	}

	/**
	 * Shows a status message to everyone, including screen reader users.
	 *
	 * @param {jQuery} $share  Share wrapper.
	 * @param {string} message Message.
	 */
	function announce( $share, message ) {
		var $status = $share.find( '.vurl-share__status' );
		window.clearTimeout( statusTimer );
		$status.text( message );
		statusTimer = window.setTimeout( function () {
			$status.text( '' );
		}, 5000 );
	}

	/**
	 * Copies text with a temporary text field, for browsers without the Clipboard API.
	 *
	 * @param {string} text Text to copy.
	 * @return {boolean} Whether the text was copied.
	 */
	function legacyCopy( text ) {
		var field = document.createElement( 'textarea' );
		var active = document.activeElement;
		var copied = false;
		field.value = text;
		field.setAttribute( 'readonly', '' );
		field.style.position = 'fixed';
		field.style.top = '-1000px';
		document.body.appendChild( field );
		field.select();
		try {
			copied = document.execCommand( 'copy' );
		} catch ( error ) {
			copied = false;
		}
		document.body.removeChild( field );
		if ( active && active.focus ) {
			active.focus();
		}
		return copied;
	}

	/**
	 * Copies text with the Clipboard API, falling back to a temporary text field.
	 *
	 * @param {string} text Text to copy.
	 * @return {Promise} Resolves when copied.
	 */
	function copyText( text ) {
		var fallback = function () {
			return legacyCopy( text ) ? Promise.resolve() : Promise.reject();
		};
		if ( window.navigator.clipboard && window.isSecureContext ) {
			return window.navigator.clipboard.writeText( text ).catch( fallback );
		}
		return fallback();
	}

	$( document )
		.on( 'change', 'form.variations_form [name^="' + PREFIX + '"]', function () {
			$( this ).closest( 'form.variations_form' ).data( 'vurlTouched', true );
		} )
		.on( 'found_variation', 'form.variations_form', function () {
			var $form = $( this );
			var url = buildUrl( $form );

			// Leave the address bar alone when WooCommerce only applied the product's default selection.
			if ( url && ( $form.data( 'vurlTouched' ) || urlHasAttributes() ) ) {
				replaceUrl( url );
			}
			$form.find( '.vurl-share' ).prop( 'hidden', ! url );
		} )
		.on( 'reset_data', 'form.variations_form', function () {
			var $form = $( this );
			var url = new URL( window.location.href );

			if ( $form.data( 'vurlTouched' ) ) {
				stripAttributes( url );
				replaceUrl( url.toString() );
			}
			$form.find( '.vurl-share' ).prop( 'hidden', true ).find( '.vurl-share__status' ).text( '' );
		} )
		.on( 'click', 'form.variations_form .vurl-share__button', function () {
			var $form = $( this ).closest( 'form.variations_form' );
			var $share = $form.find( '.vurl-share' );
			var url = buildUrl( $form ) || window.location.href;

			copyText( url ).then(
				function () {
					announce( $share, settings.copied || '' );
				},
				function () {
					announce( $share, settings.failed || '' );
				}
			);
		} );
}( jQuery ) );
