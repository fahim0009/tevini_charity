/*
$jq(window).load(function () {

	$('.wpcf7-field-groups').on('wpcf7-field-groups/change', function () {

		var $divs = $(".wpcf7-field-group").toArray().length;

		if ($divs === 1) {
			$jq(".wpcf7-field-group:nth-child(1) #birth-date").mask("99/99/9999");
			console.log('g 1');
		} else if ($divs === 2) {
			$jq(".wpcf7-field-group:nth-child(2) #birth-date").mask("99/99/9999");
			console.log('g 2');
		} else if ($divs === 3) {
			$jq(".wpcf7-field-group:nth-child(3) #birth-date").mask("99/99/9999");
			console.log('g 3');
		} else if ($divs === 4) {
			$jq(".wpcf7-field-group:nth-child(4) #birth-date").mask("99/99/9999");
			console.log('g 4');
		} else if ($divs === 5) {
			$jq(".wpcf7-field-group:nth-child(5) #birth-date").mask("99/99/9999");
			console.log('g 5');
		} else if ($divs === 6) {
			$jq(".wpcf7-field-group:nth-child(6) #birth-date").mask("99/99/9999");
			console.log('g 6');
		} else {
			$jq(".wpcf7-field-group:nth-child(7) #birth-date").mask("99/99/9999");
			console.log('g 7');
		}


	}).trigger('wpcf7-field-groups/change');

});
*/



$(window).load(function () {

	// Menu Dropdown animation
	if ($(window).width() > 977) {
		$('.dropdown-toggle.regular').dropdownHover();
	}


	// ScrollTo
	$('#nav-account a').click(function (e) {
		e.preventDefault();		//evitar el eventos del enlace normal
		var strAncla = $(this).attr('href'); //id del ancla
		$('body,html').stop(true, true).animate({
			scrollTop: $(strAncla).offset().top - 140
		}, 1000);

	});

	// Add class 'form-donor' to form modal Our Services > Payroll Giving
	$('#modal-individual form').addClass('form-donor');
	$('.payroll-modal form').addClass('form-donor');


	// ------------------------------------------------------------------------------------
	// Custom Functions CONTACT FORM 7
	// ------------------------------------------------------------------------------------

	// Add class if Back Button Exist (Steps Forms)
	$('.wpcf7-form fieldset:nth-of-type(2) .cf7mls_next').addClass('sideright');
	$('.wpcf7-form fieldset:nth-of-type(3) .cf7mls_next').addClass('sideright');
	$('.wpcf7-form fieldset:nth-of-type(4) .cf7mls_next').addClass('sideright');
	$('.wpcf7-form fieldset:nth-of-type(5) .cf7mls_next').addClass('sideright');
	$('.wpcf7-form fieldset:nth-of-type(6) .cf7mls_next').addClass('sideright');
	$('.wpcf7-form fieldset:nth-of-type(7) .cf7mls_next').addClass('sideright');


	// Add check icon to Form Steps
	$('.cf7mls_progress_bar li').append('<span></span>');


	// Detect which option is checked
	$('input:radio[name="has-passport-radio"]').change(
		function () {
			if ($(this).is(':checked') && $(this).val() == 'No') {
				$('.passport-text').addClass('show-text');
			} else {
				$('.passport-text').removeClass('show-text');
			}
		}
	);


	// Validacion Grupos de Fields (Step 6 - Company Form)
	var g1 = null;
	var g2 = null;
	var g3 = null;
	var g4 = null;
	var g5 = null;
	var g6 = null;
	var g7 = null;
	var g8 = null;

	var check1 = false;
	var check2 = false;
	var check3 = false;
	var check4 = false;
	var check5 = false;
	var check6 = false;
	var check7 = false;
	var check8 = false;

	var flag = false; // variable para verificar si el grupo anterior era valido

	// Oculto mensaje error al inicio
	$('.msg-error').hide();


	// Limit to 7 - Repeater Form group + Validations
	$('.wpcf7-field-groups').on('wpcf7-field-groups/change', function () {
		var $groups = $(this).find('.group-index');

		// Cuento candidad de grupos
		var $divs = $(".wpcf7-field-group").toArray().length;
		//console.log("Hay " + $divs + " elementos");

		// Desabilito NEXT por defecto
		//$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);

		// VALIDO GRUPO 1
		if ($divs === 1) {

			// Reseteo ckecks de grupos siguientes
			check2 = false;
			check3 = false;
			check4 = false;
			check5 = false;
			check6 = false;
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check1) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido Campos completos en el grupo
			$(".wpcf7-field-group:nth-child(1) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(1)");
				check1 = checkCampos(form);
				if (check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 2) {

			// Reseteo ckecks de grupos siguientes
			check3 = false;
			check4 = false;
			check5 = false;
			check6 = false;
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check2) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido Campos completos en el grupo
			$(".wpcf7-field-group:nth-child(2) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(2)");
				check2 = checkCampos(form);
				if (check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 3) {

			// Reseteo ckecks de grupos siguientes
			check4 = false;
			check5 = false;
			check6 = false;
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check3) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos Grupo
			$(".wpcf7-field-group:nth-child(3) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(3)");
				check3 = checkCampos(form);
				if (check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 4) {

			// Reseteo ckecks de grupos siguientes
			check5 = false;
			check6 = false;
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check4) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos Grupo
			$(".wpcf7-field-group:nth-child(4) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(4)");
				check4 = checkCampos(form);
				if (check4 && check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 5) {

			// Reseteo ckecks de grupos siguientes
			check6 = false;
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check5) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos Grupo
			$(".wpcf7-field-group:nth-child(5) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(5)");
				check5 = checkCampos(form);
				if (check5 && check4 && check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 6) {

			// Reseteo ckecks de grupos siguientes
			check7 = false;
			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check6) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos del Grupo
			$(".wpcf7-field-group:nth-child(6) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(6)");
				check6 = checkCampos(form);
				if (check6 && check5 && check4 && check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		} else if ($divs === 7) {

			check8 = false;

			// Valido si antes estaba OK, sino desabilito Next
			if (check7) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos Grupo
			$(".wpcf7-field-group:nth-child(7) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(7)");
				check7 = checkCampos(form);
				if (check7 && check6 && check5 && check4 && check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});


		} else if ($divs === 8 ) {

			// Valido si antes estaba OK, sino desabilito Next
			if (check8) {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
			} else {
				$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
				$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
			}

			// Valido campos completos Grupo
			$(".wpcf7-field-group:nth-child(7) input").keyup(function () {
				var form = $(this).parents(".wpcf7-field-group:nth-child(8)");
				check8 = checkCampos(form);
				if (check8 && check7 && check6 && check5 && check4 && check3 && check2 && check1) {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', false);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', false);
					$('.msg-error').hide();
				}
				else {
					$('.fieldset-cf7mls:nth-child(7) .cf7mls_next').attr('disabled', true);
					$('.kyc-company-form .fieldset-cf7mls:nth-child(6) .cf7mls_next').attr('disabled', true);
					$('.msg-error').show();
				}
			});

		}


		$groups.each(function () {
			$(this).text($groups.index(this) + 1);

		});

		// Limitar a 7 Grupos de Fields
		if ($divs > 7) {
			$('.wpcf7-field-group-add').attr('disabled', true);
			$('.wpcf7-field-group-add').hide();
		} else {
			$('.wpcf7-field-group-add').attr('disabled', false);
			$('.wpcf7-field-group-add').show();
		}


	}).trigger('wpcf7-field-groups/change');



	// Checkbox required
	$('.checkbox-required input[type="checkbox"]').change(function () {
		if (this.checked) {
			$(this).next().addClass('check');
		} else {
			$(this).next().removeClass('check');
		}
	});


	// Display Datepicker on Safari Browser
	//if ($('.wpcf7-date')[0].type != 'date') $('.wpcf7-date').datepicker();
	// End



	// ------------------------------------------------------------------------------------
	// END Custom Functions CF7
	// ------------------------------------------------------------------------------------

});



$(document).ready(

	function () {

		$('.box-account.post-9188').on('click', function () {
			window.location.href = "https://achisomoch.org/services/individual/";
		});

		$('.box-account.post-117').on('click', function () {
			window.location.href = "https://achisomoch.org/services/donations/";
		});

		$('.box-account.post-119').on('click', function () {
			window.location.href = "https://achisomoch.org/services/payroll-giving/";
		});

		$('.box-account button').on('click', function (e) {
			e.stopPropagation();
			$($(this).data('target')).modal('show');
		});

		$('.header-onlinesystem .btn-outline').on('click', function (e) {
			event.preventDefault();

			$('html, body').animate({
				scrollTop: $($.attr(this, 'href')).offset().top - 90
			}, 500);
		});


		/* DATE PICKER
			Get the actual date and print it as value in the Gift Aid FORM
			Deleted 2016-11-03
		*/

		$('.online-complete').on('click', function () {
			$('.first-body').fadeOut(function () {
				$('.second-body').fadeIn();
			});
		});

		$('.modal-back').on("click", function () {
			$('.second-body').fadeToggle(function () {
				$('.first-body').fadeToggle();
			});
		});

		//var d = new Date();

		//var month = d.getMonth()+1;
		//var day = d.getDate();

		/*var output = d.getFullYear() + '/' +
		    ((''+month).length<2 ? '0' : '') + month + '/' +
		    ((''+day).length<2 ? '0' : '') + day;
		    */

		//var output = ((''+day).length<2 ? '0' : '') + day + '/' + ((''+month).length<2 ? '0' : '') + month + '/' + d.getFullYear();

		//$('#wpcf7-f130-p115-o2 input[name="date"]').attr('placeholder', output);
		//$('#wpcf7-f130-p115-o2 input[name="date"]').attr('value', output);


		/* FORM */
		$('.page-id-126 .wpcf7-form, .page-id-7 .wpcf7-form, .page-id-203 .wpcf7-form, .page-id-443 .wpcf7-form').attr('id', 'form');
		$('.single-services #find-out .wpcf7-form').addClass('hideme');
		$('.wpcf7-submit, #row-contact .wpcf7-submit').addClass('btn-send');

		$('.single-services .wpcf7-form').addClass('form-donor hideme anim');

		$('#row-contact .wpcf7').addClass('contact-form');
		$('input.wpcf7-validates-as-required').parent().addClass('required');

		// Placeholder in Country fields
		$('#wpcf7-f9184-p9188-o1 input[name="country"], #wpcf7-f9184-p9188-o1 input[name="country-other"], #wpcf7-f9183-p117-o1 input[name="country"], #wpcf7-f9183-p117-o1 input[name="country-other"]').attr('placeholder', 'Country');

		// Remove white spaces in National Insurance Number
		$('#wpcf7-f9184-p9188-o1 input[name="insurance-number"], #wpcf7-f9183-p117-o1 input[name="insurance-number"], #wpcf7-f9184-o3 input[name="insurance-number"], #wpcf7-f9183-o4 input[name="insurance-number"]').on('change', function () {
			$(this).val($(this).val().replace(/\s+/g, ''));
		});



		/* Services */

		$('.box-account ul li, .modal ul li, .single-services .bg-container li').prepend('<span><i class="fa fa-check"></i></span>');

		/* Gift Aid Form
		var radio = $('.wpcf7-form-control-wrap.checkbox-900').clone(); // Clone radio buttons
	    $('#radio-form').html(radio);

	    $('#radio-form input').click(function(e){ // Copy states of radio buttnos cloned
		    var pet = $(this).val()
		        $('.form-donor input[name="checkbox-900[]"][value="'+pet+'"]')
		            .attr('checked','checked');
		});
		var checkboxes = $('.wpcf7-form-control-wrap.checkbox-971').clone(); // Clone radio buttons
	    $('#checkbox-form').html(checkboxes);

	    $('#checkbox-form input').click(function(e){ // Copy states of radio buttnos cloned
		    var tep = $(this).val()
		        $('.form-donor .apply-boxes input[name="checkbox-971[]"][value="'+tep+'"]')
		            .attr('checked','checked');
		}); */

	    /*$('#wpcf7-f130-p115-o2').submit(function(){
	    	if($("input[name='checkbox-900[]']:checked").length>0){
	    		alert("Something");
	    	} else{
	    		$( "#radio-form" ).append( '<p style="color:red;">Please check at least one option</p>' );
	    	}
	    });*/

		/* News */


		$('.post-categories li a').prepend('<span><i class="fa fa-arrow-right"></i></span>');

		//

		// Sitemap //

		/* MENU */

		$('.lkn-login a').attr('target', '_blank');

		/* move div */

		$('#menu-item-2669 a').first().addClass("dropdown-toggle regular");
		var quick = $('#drop-quick').clone();
		$('#drop-quick').remove();
		$('.lkn-quick').append(quick);
		$('.lkn-quick #drop-quick').addClass("quick-visible");
		$('.lkn-quick').addClass("menu-item-has-children");
		$('.lkn-quick .sub-menu').first().prepend('<span class="caret hidden-xs hidden-sm"></span>');
		$('.lkn-login .sub-menu, #menu-item-2669 .sub-menu').first().prepend('<span class="caret hidden-xs hidden-sm"></span>');
		$('.lkn-quick a').first().addClass("dropdown-toggle regular");
		$('.lkn-login a').first().addClass("dropdown-toggle regular");

		/**/

		var url = window.location.href;
		//console.log(url);

		if (url.indexOf("declaration") >= 0) {
			//alert("has declaration");
			function anim_form() {
				$('.form-container').addClass("active");
				$('#declaration .fa').removeClass("fa-chevron-down");
				$('#declaration .fa').addClass("fa-chevron-up");
			}
			setTimeout(anim_form, 1000);
		} else {
			//alert("not has");
		};

		/* SUBMENU */

		$('.menu-item-has-children .sub-menu').first().prepend('<span class="caret hidden-xs hidden-sm"></span>');
		$('.menu-item-has-children a').first().addClass("dropdown-toggle regular");
		$('#menu-header .sub-menu').addClass("dropdown-menu");
		$('#menu-header .sub-menu').attr("role", "menu");


		$("#accordion .title-box a").click(

			function (event) {
				event.preventDefault();

				$(this).parent().parent().toggleClass('active');
				$(this).parent().parent().find('.info-box').slideToggle();

			});

		$(".navbar-toggle").click(

			function (event) {
				event.preventDefault();

				$('#navs-container,.navbar-toggle').toggleClass('active');

			});

		$('body').on('click', '.bt-expandable', function (event) {

			event.preventDefault();

			$('.form-container').toggleClass('active');
			if ($('.form-container').hasClass('active')) { //You can also use $(this).hasClass
				$('.title-box .fa').removeClass('fa-chevron-down');
				$('.title-box .fa').addClass('fa-chevron-up');
			}
			else {
				$('.title-box .fa').removeClass('fa-chevron-up');
				$('.title-box .fa').addClass('fa-chevron-down');
			}

		});

		$(window).load(function () {
			$('#slider-marcas.flexslider').flexslider({
				animation: "slide",
				directionNav: false,
				animationLoop: false,
				slideshow: false,
				itemWidth: 250,
				itemMargin: 0,
				useCSS: false,
				minItems: 2,
				maxItems: 4

			});

			$('#slider-marcas .flex-prev').on('click', function () {
				$('#slider-marcas').flexslider('prev');
				return false;
			})

			$('#slider-marcas .flex-next').on('click', function () {
				$('#slider-marcas').flexslider('next');
				return false;
			})

			/* SLIDER HOME ACTUALLY DESABLED
			$('#slider-top.flexslider').flexslider({
			    directionNav: false,
			    animationLoop: false,
			    animation: "fade",
			    manualControls : '.slide-control-nav li a'
			  });

  			$('#slider-top .slide-prev').on('click', function(){
			    $('#slider-top').flexslider('prev');
			    return false;
			})

			$('.slide-next').on('click', function(){
			    $('#slider-top').flexslider('next');
			    //alert('as')
			    return false;
			})
			*/


		});

		if ($('.no-touchevents').length && $(window).width() > 1200) {
			$(window).scroll(

				function () {/*

	  				var scrollTop = $( window ).scrollTop ( );

	  				if( scrollTop < 100){
	  					//$('#header').addClass('header-min');
	  					$('#header #nav').css('margin-top', 58 - scrollTop  * 0.15 );
	  					$('#header h1 img').css('width', 190 - scrollTop  * 0.6 );
	  					$('#header ').css('height', 145 - scrollTop * 0.4 );
	  				}
	  				else{
	  					$('#header #nav').css('margin-top', 58 - 100  * 0.15 );
	  					$('#header h1 img').css('width', 190 - 100  * 0.6 );
	  					$('#header ').css('height', 145 - 100 * 0.4 );
	  				}
				*/
				}
			);
		}

		$("#searchcamp").submit(function (e) {
			e.preventDefault();
			var q = $('input[name=q]').val();
			q = q.toLowerCase();
			$('.elements').each(function () {
				var a1 = $(this).find('h5').html();
				a1 = a1.toLowerCase();
				var a2 = $(this).find('p').html();
				a2 = a2.toLowerCase();
				var va = 0;
				if (a1.indexOf(q) != -1 || q == '') {
					va = 1;
				}
				if (a2.indexOf(q) != -1 || q == '') {
					va = 1;
				}
				if (va == 1) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});
		$(".genbtn").click(function (e) {
			e.preventDefault();
			$(".genbtn").not(this).removeClass('active');
			$(this).addClass('active');
			var gen = $(this).data("category");
			$('.elements').each(function () {
				if ($(this).data('genre') == gen || gen == '') {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});
		$(".campaings-section .filter-btn").click(function () {
			if ($(".filter").hasClass("active")) {
				$(".filter").removeClass("active");
			}
			else {
				$(".filter").addClass("active");
			}
		});



		// Detect changes in Date Field
		$("body").on('DOMSubtreeModified', ".birth-date", function () {
			if ($('.birth-date .wpcf7-not-valid-tip').length) {
				var value = $('.birth-date .wpcf7-not-valid-tip').text();

				if (value.indexOf("More characters are needed in this field") > -1) {
					$('.birth-date .wpcf7-not-valid-tip').text('');
					$('.birth-date .wpcf7-not-valid-tip').text('Enter your birthdate in the format of dd/mm/yyyy');
				}
			}
		});










	}
);

$('body.archive, body.single-post').addClass('blog');

$(document).ready(function () {

	if ($('#nav-account').length > 0) {

		var nav_menu = $('.nav-account').offset().top - 100;
		var row_1_top = $('#account-detail-row').offset().top;
		var row_2_top = $('#features-row').offset().top;
		var row_3_top = $('#how-join-row').offset().top;
		var row_4_top = $('#how-works-row').offset().top;
		if ($('#declaration').length > 0) {
			var declaration_top = $('#declaration').offset().top;
		}
		var faqs_top = $('#some-questions').offset().top;
		//console.log(row_1_top);
	}




	// Inicializo Mensaje de 'Fecha InvÃ¡lida' y lo oculto (Donation Form - Step 6)
	$('.wpcf7-field-group input.date').after('<span class="date-incomplete" style="display: block;color: #f00;font-size: 12px;">Please, complete in DD/MM/YYYY format</span>');
	$('.date-incomplete').css('display', 'none');
	// Valido Birth Date mediante KeyUp y muestro/oculto mensaje 
	$('.wpcf7-field-group input.date').keyup(function () {
		if ($(this).val().length < 10) {
			$('.date-incomplete').css('display', 'block');
		} else {
			$('.date-incomplete').css('display', 'none');
		}
	});



	// Habilitar/Desabilitar checkbox segun estado de Checkbox 'TAXPAYER' (Individual Form - Step 4)
	$(".three-box-checkboxs input[type=checkbox]").attr("disabled", true); // Inicializo checkboxs en 'disabled'
	$(".claim-gift-aid-checkbox input").click(function () {
		if ($(this).is(':checked')) {
			$(".three-box-checkboxs input[type=checkbox]").attr("disabled", false);
			$('.three-box-checkboxs input[type=checkbox]').attr('checked', true);
			$('.three-box-checkboxs .last input[type=checkbox]').attr('checked', false);
		} else {
			$(".three-box-checkboxs input[type=checkbox]").attr("disabled", true);
			//$('.three-box-checkboxs input[type=checkbox]').attr('checked', false);
		}
	});




	/* Every time the window is scrolled ... */
	$(window).scroll(function () {


		/* Sticky menu */

		if ($('#nav-account').length > 0) {

			var scroll = $(window).scrollTop(); // For sticky menu
			var scroll_top = $(window).scrollTop() + 150; // For selected sticky bt menu

			if (scroll > nav_menu) {
				$('.nav-account').addClass('fixed');
				$('#gif-aid').addClass('padding-top');
			}
			else {
				$('.nav-account').removeClass('fixed');
				$('#gif-aid').removeClass('padding-top');
			}

			if (scroll_top < row_1_top) {
				$('.nav-account a').removeClass('active');
			}
			if (scroll_top > row_1_top) {
				$('.nav-account a').removeClass('active');
				$('#account_link').addClass('active');
			}
			if (scroll_top > row_2_top) {
				$('.nav-account a').removeClass('active');
				$('#features_link').addClass('active');
			}
			if (scroll_top > row_3_top) {
				$('.nav-account a').removeClass('active');
				$('#how_link').addClass('active');
			}
			if (scroll_top > row_4_top) {
				$('.nav-account a').removeClass('active');
				$('#works_link').addClass('active');
			}
			if (scroll_top > declaration_top) {
				$('.nav-account a').removeClass('active');
				$('#declaration_link').addClass('active');
			}
			if (scroll_top > faqs_top) {
				$('.nav-account a').removeClass('active');
				$('#faqs_link').addClass('active');
			}

		}


		/* Check the location of each desired element */
		$('.hideme').each(function (i) {

			var bottom_of_object = $(this).offset().top + $(this).outerHeight();
			var bottom_of_window = $(window).scrollTop() + $(window).height();


			/* If the object is completely visible in the window, fade it it */
			if (bottom_of_window > bottom_of_object - 150) {

				//$(this).animate({'opacity':'1'},500);
				$(this).addClass('anim');
			}


		});

	});

});


// ------------------------------------------------------------------------------------
//FunciÃ³n para comprobar los campos de texto
// ------------------------------------------------------------------------------------
function checkCampos(obj) {
	var camposRellenados = true;
	var esFecha = true;

	obj.find("input.wpcf7-validates-as-required").each(function () {
		var $this = $(this);

		if ($this.val().length <= 0) {
			camposRellenados = false;
			return false;
		}
	});

	// Compruebo si Birth Date tiene 10 caracteres
	obj.find("input.date").each(function () {
		var $this = $(this);

		if ($this.val().length < 10) {
			esFecha = false;
			return false;
		}
	});


	if ((camposRellenados == true) && (esFecha == true)) {
		return true;
	} else {
		return false;
	}
}

// ------------------------------------------------------------------------------------
// END Funcion CheckCampos()
// ------------------------------------------------------------------------------------
if ($(window).width() < 768) {
	if ($('.content-show-more').length) {
		initReadMore();
	}
}
function initReadMore() {
    var charQuantity = $('.content-show-more').attr('data-chars');
    charQuantity = parseInt(charQuantity);

    $('.content-show-more').readmore({
        speed: 200,
        collapsedHeight: charQuantity,
        embedCSS: false,
        blockCSS: 'display: block; width: 100%;',
        moreLink: '<div class="morelink"><a href="#" class="read-lnk arrow-down">View more</a></div>',
        lessLink: '<div class="morelink"><a href="#" class="arrow-up read-lnk">View less</a></div>'
    });
}



$('#wpcf7-f9183-p117-o1 input[name="account-type"], #wpcf7-f9183-o4 input[name="account-type"]').on('change', function(){
	if ($(this).val() === 'Charitable Trust') {
		$('#trust-requirement-list').show();
		$('#company-requirement-list').hide();
		$('#charity-trust-message-warning').show();
		$('#confirm-distribute-funds').removeAttr('checked');
		disableBtn();
		$('#confirm-distribute-funds').on('change', function() {			
			$(this).prop('checked') ? enableBtn() : disableBtn();
		})
	} else {
		$('#trust-requirement-list').hide();
		$('#company-requirement-list').show();
		$('#charity-trust-message-warning').hide();
		enableBtn();
	}

	function disableBtn(){
		$('[data-cf7mls-order="0"] .cf7mls_next.cf7mls_btn.action-button').prop('disabled', true);
		$('[data-cf7mls-order="0"] .cf7mls_next.cf7mls_btn.action-button').css('opacity', 0.5);
	}

	function enableBtn(){
		$('[data-cf7mls-order="0"] .cf7mls_next.cf7mls_btn.action-button').prop('disabled', false);
		$('[data-cf7mls-order="0"] .cf7mls_next.cf7mls_btn.action-button').css('opacity', 1);
	}
});



/* Gibraltar Form */
$('input[name="gibraltar-taxpayer"]').on('change', function(){
	if ($(this).val() === 'Yes') {
		//console.log('YES');
		$('.taxpayer-number').show();
		$('input[name="gibraltar-taxpayer-number"]').val('');
	} else {
		$('.taxpayer-number').hide();
		$('input[name="gibraltar-taxpayer-number"]').val('-');
	}
});


$('input[name="address-line-1b"]').val('-');		
$('input[name="address-line-2b"]').val('-');		
$('input[name="correspondence-address-same"]').on('change', function(){
	if ($(this).val() === 'Yes') {
		//console.log('YES');
		$('.correspondence-address').hide();
		$('input[name="address-line-1b"]').val('-');		
		$('input[name="address-line-2b"]').val('-');		
	} else {
		$('.correspondence-address').show();
		$('input[name="address-line-1b"]').val('');
		$('input[name="address-line-2b"]').val('');
	}
});