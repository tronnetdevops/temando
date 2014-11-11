;setTimeout(function(){
	"use strict"

	return (window.temandoQuantityLoader = {
		"data": {
			"elements": {
				"quantity": $("label:contains('Number of Students')").siblings("input").get(0),
				"teacherQuantity": $("label:contains('Number of Teachers')").siblings("input").get(0),
			}
		},
		"init": function(){

			this.bind();

			return true;	
		},
		"bind": function(){
			var _this = this,
				els = this.data.elements;

			$(els.quantity).bind("blur", function(){ 
				var $prodTotal = $(".ussr-component-gird-cell[data-modelattr='quantity']:first input");

				if ($prodTotal.length){
					var newTotal = parseInt($(this).val() || 0);
					$prodTotal.val( newTotal ).trigger("change")
				}
			});
			$(els.teacherQuantity).bind("blur", function(){ 
				var $prodTotal = $(".ussr-component-gird-cell[data-modelattr='quantity']:last input");

				if ($prodTotal.length){
					var newTotal = parseInt($(this).val() || 0);
					$prodTotal.val( newTotal ).trigger("change")
				}
			});
		}
	}).init();
}, 6000);

// $(document.head).append( $('<script src="//api.temando.tronnet.me/resources/temando-ajax-loader.js" async="true"></script>') );