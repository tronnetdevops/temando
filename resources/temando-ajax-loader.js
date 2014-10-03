;(function(d){
	console.log("HERE WE GO!");
	
	var form = d.forms[0];

	form.appendChild( 
		shipping = $("<div/>").css({
			"width": "100%", 
			"float": "left"
		}).attr("id", "total-shipping").get(0)
	);

	return document.temandoAjaxProxy = {
		"data": {
			"requests": 0,
			"requestLimit": 5,
			"elements": {
				"form": form,
				"city": form.city,
				"state": form.state,
				"country": form.country,
				"shipping": shipping,
				"quantity": null
			}
		},
		"init": function(){
			var els = this.data.elements;
			for (var i =0;i<els.form.length;i++){
				if (els.form[i].type == "number"){
					els.quantity = els.form[i];
					break;
				}
			}

			this.bind();
		},
		"bind": function(){
			var _this = this,
				els = this.data.elements;

			$(els.city).bind("blur", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.quantity).bind("blur", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.state).bind("change", function(){ _this.data.requests = 0; _this.update.call(_this); });
			$(els.country).bind("change", function(){ _this.data.requests = 0; _this.update.call(_this); });
		},
		"update": function(){
			var _this = this,
				els = this.data.elements,
				country = "AU", //els.country.options[ els.country.selectedIndex ].value,
				postalCode = 4000, //"",
				suburb = "BRISBANE", //els.city.options[ els.city.selectedIndex ].value,
				quantity = els.quantity.value || 0;

			if (country && postalCode && suburb && quantity){
				$.ajax({
					"url": "https://api.temando.tronnet.me/",
					"timeout": 15000,
					"async": false,
					"dataType": "json",
					"data": {
						"country": country,
						"postalCode": postalCode, //4000
						"suburb": suburb, //BRISBANE
						"quantity": quantity
					},
					"done": function(response){
						console.log("UPdated this shit!");
						console.log(arguments);	

						$(_this.data.elements.shipping).text("Shipping is: ");
					},
					"error": function(){
						console.log("Timed out, lets try again in 20 seconds");
						if (++_this.data.requests < _this.data.requestsLimit){
							setTimeout(function(){ _this.update.call(_this) }, 20000);
						}
					}
				});
			}
		}
	};
})(document).init();

// $(document.head).append( $('<script src="http://temandotest.loc/temando-ajax-loader.js" async="true"></script>') );