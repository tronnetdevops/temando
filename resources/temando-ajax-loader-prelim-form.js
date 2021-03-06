;setTimeout(function(){
	"use strict"

	return (window.temandoAjaxProxy = {
		"data": {
			"requests": 0,
			"requestLimit": 5,
			"wait": 2000,
			"buffer": 15,
			"elements": {
				"city": $("label:contains('Shipping City')").siblings("input").get(0),
				"zip": $("label:contains('Shipping Zip')").siblings("input").get(0),
				"country": $("label:contains('Shipping - Country')").siblings("select").get(0),
				"quantity": $("label:contains('Number of Students')").siblings("input").get(0),
				"teacherQuantity": $("label:contains('Number of Teachers')").siblings("input").get(0),
				"courier": $("label:contains('Courier ID')").siblings("input").get(0),
				"packageType": $("label:contains('Package Type')").siblings("select").get(0),
				"shipping": $("label:contains('Preliminary Shipping Total')").siblings("input").get(0)

			}
		},
		"init": function(){
			var els = this.data.elements;
			els.shipping.parentNode.appendChild(
				$("<div/>").css({
					"width": "100%", 
					"float": "left"
				}).attr("id", "total-shipping").get(0)
			 );

			this.bind();	
		},
		"bind": function(){
			var _this = this,
				els = this.data.elements;

			$(els.city).bind("blur", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 
			});
			$(els.quantity).bind("blur", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 

				var $prodTotal = $(".ussr-component-gird-cell[data-modelattr='quantity']:first input");

				if ($prodTotal.length){
					var newTotal = parseInt($(this).val())+parseInt(els.teacherQuantity	.value || 0);
					$prodTotal.val( newTotal ).trigger("change")
				}
			});
			$(els.teacherQuantity).bind("blur", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 

				var $prodTotal = $(".ussr-component-gird-cell[data-modelattr='quantity']:first input");

				if ($prodTotal.length){
					var newTotal = parseInt($(this).val())+parseInt(els.quantity.value || 0);
					$prodTotal.val( newTotal ).trigger("change")
				}
			});
			$(els.zip).bind("blur", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 
			});
			$(els.country).bind("change", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 
			});
			$(els.packageType).bind("change", function(){ 
				_this.data.requests = 0; 
				_this.update.call(_this); 
			});
		},
		"warn": function(title, text){
			var $ele = $('<div class="warning-modal" id="calc-warning-modal" style="background: white; position: absolute; top: '+(+window.scrollY + 300) +'px	; left: 40%; width: 400px; height: 200px; z-index: 9999; text-align: center; border-radius: 5px; border: 1px solid rgba(0,0,0,0.7);">\
				<div style="background: whitesmoke; text-align: center; height: 50px; width: 100%; font-size: 20px; padding-top: 10px; font-weight: bold; border-radius: 5px;">'+title+'</div>\
				<div>'+text+'</div>\
				<button onclick="$(this.parentNode).remove();">OK</button>\
			</div>');

			$ele.click(function(){ $(this).remove(); });

			$(document.body).append( $ele );

			setTimeout(function($ele){
				if ($ele && typeof $ele == "array" && $ele.length){
					$ele.remove();
				}
			}, 2000, $ele);
		},
		"update": function(){
			var _this = this,
				els = this.data.elements,
				selectedType = els.packageType.options[ els.packageType.selectedIndex ],
				type = selectedType[('innerText' in selectedType) ? 'innerText' : 'textContent'],
				data = {
					"country": "AU",
					"postalCode": els.zip.value,
					"suburb": els.city.value //els.state.options[ els.state.selectedIndex ].value,
				};

			switch(type){
				case "Original":
					data.quantity = (+els.quantity.value+(+els.teacherQuantity.value)) || 1;
					break;

				case "Digital":
					data.quantity = +els.teacherQuantity.value || 1;

					break;

				case "Original + Digital":
					data.quantity = (+els.quantity.value+(+els.teacherQuantity.value)) || 1;
					break;
			}

			console.log("Getting shipping for: ", data);

			if (data.country == "AU" && data.country && data.postalCode && data.suburb && data.quantity){

				window.clearTimeout(_this.data.firstTimeout);
				window.clearTimeout(_this.data.secondTimeout);


				console.log("GO GO GOOO");

				_this.data.elements.shipping.value = 0;
				$("#total-shipping").text("Calculating shipping...");

				_this.data.gotPrice = false;

				_this.data.firstTimeout = setTimeout(function(){
					if (!_this.data.gotPrice){
						$("#total-shipping").text("Crunching...we might not have your region yet...");
					}
				}, _this.data.wait + 1000);

				_this.data.secondTimeout = setTimeout(function(){
					if (!_this.data.gotPrice){
						$("#total-shipping").text("Still processing, thank you for your patience...");
					}
				}, _this.data.wait + 8000);

				$.ajax({
					"url": "http://api.temando.tronnet.me/",
					"async": true,
					"dataType": "json",
					"data": data
				}).done(function(response){
					if (response.status.code == 1){
						if (!_this.data.requests){
							_this.warn("Shoot!", "We haven't processed orders from your area before, so we have to crunch some numbers real quick! Give us ~5 seconds...");
						}

						if (++_this.data.requests < _this.data.requestLimit){
							setTimeout(function(){ _this.update.call(_this) }, _this.data.wait);
						} else {
							_this.warn("No go...", "We are having some issues attempting to get a quote for your region. Please contact us and let us know!");
						}
					} else {
						_this.data.gotPrice = true;

						var price = response.data[ response.data._lowest ]["total"]; // General (Road)

						$("#total-shipping").text("")

						_this.data.elements.shipping.value = parseInt(+parseInt(price)+(+_this.data.buffer));
						_this.data.elements.courier.value = response.data[ response.data._lowest ]["carrier_id"];
					}
				});
			}
		}
	}).init();
}, 6000);

// $(document.head).append( $('<script src="//api.temando.tronnet.me/resources/temando-ajax-loader.js" async="true"></script>') );