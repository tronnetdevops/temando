;(function(d,w){
	console.log("HERE WE GO!");
	
	var form = d.forms[0];

	return w.temandoAjaxProxy = {
		"data": {
			"requests": 0,
			"requestLimit": 5,
			"elements": {
				"form": form,
				"city": form.city,
				"state": form.state,
				"zip": form.zip,
				"country": form.country,
				"shipping": null,
				"quantity": null
			}
		},
		"init": function(){
			var els = this.data.elements;
			var firstFind = true;
			for (var i =0;i<els.form.length;i++){
				if (els.form[i].type == "number"){
					if (firstFind){
						els.quantity = els.form[i];
						firstFind = false;
					} else {
						els.shipping = els.form[i];						
					}
				}
			}

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

			$(els.city).bind("blur", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.quantity).bind("blur", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.zip).bind("blur", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.state).bind("change", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.country).bind("change", function(){ _this.data.requests = 0; _this.update.call(_this); });
		},
		"warn": function(title, text){
			var $ele = $('<div class="warning-modal" id="calc-warning-modal" style="background: white; position: absolute; top: 47%; left: 40%; width: 400px; height: 200px; z-index: 9999; text-align: center; border-radius: 5px; border: 1px solid rgba(0,0,0,0.7);">\
				<div style="background: whitesmoke; text-align: center; height: 50px; width: 100%; font-size: 20px; padding-top: 10px; font-weight: bold; border-radius: 5px;">'+title+'</div>\
				<div>'+text+'</div>\
				<button onclick="$(this.parentNode).remove();">OK</button>\
			</div>');

			$ele.click(function(){ $(this).remove(); });

			console.log("Adding warning to body!");
			$(document.body).append( $ele );

			setTimeout(function($ele){
				console.log("Close it...");
				$ele.remove();
			}, 9000, $ele);
		},
		"update": function(){
			var _this = this,
				els = this.data.elements,
				data = {
					"country": els.country.options[ els.country.selectedIndex ].value,
					"postalCode": els.zip.value,
					"suburb": els.city.value, //els.state.options[ els.state.selectedIndex ].value,
					"quantity": els.quantity.value || 0
				};

			console.log("Getting shipping for: ", data);

			if (data.country == "AU" && data.country && data.postalCode && data.suburb && data.quantity){

				console.log("GO GO GOOO");

				_this.data.elements.shipping.value = 0;
				$("#total-shipping").text("Calculating shipping...");

				$.ajax({
					"url": "https://api.temando.tronnet.me/",
					"async": true,
					"dataType": "json",
					"data": data
				}).done(function(response){
					if (response == null || !response){
						return;// _this.warn("Hmm...", "We're not getting a response from the quote service. Is everything spelled correctly?");
					}

					var price = response.data["General (Road)"];

					console.log("GOT SHIPPING!", price)

					$("#total-shipping").text("")

					_this.data.elements.shipping.value = price;
				}).fail(function(){
					console.log("fucken fail...");
					if (!_this.data.requests){
						_this.warn("Shoot!", "We haven't processed orders from your area before, so we have to crunch some numbers real quick! Give us ~20 seconds...");
					}

					if (++_this.data.requests < _this.data.requestLimit){
						setTimeout(function(){ _this.update.call(_this) }, 7000);
					}
				}).always(function(failed){
					if (failed == null || !failed){
						console.log("fucken fail...");
						if (!_this.data.requests){
							_this.warn("Shoot!", "We haven't processed orders from your area before, so we have to crunch some numbers real quick! Give us ~20 seconds...");
						}

						if (++_this.data.requests < _this.data.requestLimit){
							setTimeout(function(){ _this.update.call(_this) }, 7000);
						} else {
							_this.warn("No go...", "We are having some issues attempting to get a quote for your region. Please contact us and let us know!");						}
					}
				});
			}
		}
	};
})(document, window).init();

// $(document.head).append( $('<script src="http://temandotest.loc/resources/temando-ajax-loader.js" async="true"></script>') );