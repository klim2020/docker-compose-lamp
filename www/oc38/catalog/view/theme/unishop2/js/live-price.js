function uniLivePrice() {
	const ChangePrice = (el) => {
		var el = $(el), elem;
		
		elem = el.closest('.product-thumb');
		
		if(!elem.length) {
			elem = el.closest('.product-block');
		}
	
		if(elem.length) {
			let quantity = elem.find('.qty-switch__input').val(), 
				option_price = 0;
				
			if(typeof(quantity) == 'undefined' || quantity <= 0) quantity = 1;
				
			let elem2 = elem.find('.price'), 
				price = parseFloat(elem2.data('price')), 
				price2 = parseFloat(elem2.data('old-price')), 
				special = parseFloat(elem2.data('special')), 
				special2 = parseFloat(elem2.data('old-special'));
				
			let oldPrice = price2 ? price2 : price, 
				old_special = special2 ? special2 : special,
				price_elem = elem2.find('.price-old'), 
				special_elem = elem2.find('.price-new');
	
			let discounts = elem2.data('discount');
	
			if(discounts && special <= 0) {
				discounts = JSON.parse(discounts.replace(/'/g, '"'));
	
				for (i in discounts) {
					d_quantity = parseFloat(discounts[i]['quantity']);
					d_price = parseFloat(discounts[i]['price']);
		
					if((quantity >= d_quantity) && (d_price < price)) {
						price = d_price;
					}
				}
			}
	
			elem.find('input:checked, option:selected').each(function() {
				const thisPrice = parseFloat($(this).data('price'));
				
				if ($(this).data('prefix') == '+') {
					option_price += thisPrice;
				}
				if ($(this).data('prefix') == '-') {
					option_price -= thisPrice;
				}
				if ($(this).data('prefix') == '*') {
					price *= thisPrice;
					special *= thisPrice;
				}
				if ($(this).data('prefix') == '/') {
					price /= thisPrice;
					special /= thisPrice;
				}
				if ($(this).data('prefix') == '=') {
					option_price += thisPrice;
					
					if(thisPrice > 0) {
						special = price-special;
						price = 0;
					}
				}
			});
	
			newPrice = (price + option_price) * quantity;
			new_special = (special + option_price) * quantity;

			if(special) {
				AnimatePrice(oldPrice, newPrice, price_elem);
				AnimatePrice(old_special, new_special, special_elem);
			} else {
				AnimatePrice(oldPrice, newPrice, elem2);
			}
	
			elem2.data('old-price', newPrice);
			elem2.data('old-special', new_special);
		}
	}
	
	const AnimatePrice = (oldPrice, newPrice, elem) => {
		if(newPrice != oldPrice) {
			$({val:oldPrice}).animate({val:newPrice}, {
				duration:100,
				step: function(val) {
					elem.text(PriceFormat(val));
				}
			});
		} else {
			elem.text(PriceFormat(newPrice));
		}
	}
	
	const PriceFormat = (n) => { 
		c = uniJsVars.currency.decimal != 0 ? uniJsVars.currency.decimal : '';
		d = uniJsVars.currency.decimal_p;
		t = uniJsVars.currency.thousand_p;
		s_left = uniJsVars.currency.symbol_l;
		s_right = uniJsVars.currency.symbol_r;
		i = parseInt(n = Math.abs(n).toFixed(c)) + ''; 
		j = ((j = i.length) > 3) ? j % 3 : 0; 
		
		return s_left + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '') + s_right; 
	}
	
	$(document).on('change', '.qty-switch__input, .option input[type="checkbox"], .option input[type="radio"], .option select', function() { 
		ChangePrice(this);  
	});
	
	if($('#hpmodel').length && $('.product-block .qty-switch__input').length) {
		const observer = new MutationObserver((e) => {
			/*
			setTimeout(() => {
				if($('.product-block .qty-switch__input').val() > 1) {
					$('.product-block .qty-switch__input').change();
				}
			}, 350);
			*/
			
			$(document).ajaxStop(() => {
				if($('.product-block .qty-switch__input').val() > 1) {
					$('.product-block .qty-switch__input').change();
				}
			});
		});
		
		observer.observe($('#hpmodel')[0], {childList:true, subtree:true});
	}
		
	$('.qty-switch__input').each(function() {
		if($(this).val() > 1) {
			ChangePrice(this); 
		}
	});
	
	$(document).ajaxStop(() => {
		$('.product-thumb .qty-switch__input').each(function() {
			if($(this).val() > 1) {
				ChangePrice(this); 
			}
		});
	});
};
	
$(function() {	
	uniLivePrice();
});