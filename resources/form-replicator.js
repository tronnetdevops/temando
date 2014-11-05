(function(){
	"use strict"

	return (window.TNDOFormReplicator = {
		"init": function(){
			var API_URI = "api.temando.tronnet.me/ontraport.php",
				$group = $("<div>"),
				$section = $("<div><h3 class='teacher-num'>1</h3></div>").css({
					"border-top": "1px solid rgba(0,0,0,0.6)"
				}).addClass("moonray-form-element-wrapper moonray-form-input-type-text"),
				$originHiddenField = $("<input/>").attr({
					"name": "form-origin",
					"type": "hidden"
				}).val( location.pathname.substr(1) ),
				$primary = $("label:contains('First Name')").parent(),
				$head = $primary,
				$form = $primary.parent(),
				$submit = $form.find("input[type='submit']").parent(),
				$initialElements = [],
				$clone, key;

			for(var fieldsCloned=0; fieldsCloned<5; fieldsCloned++){
				$clone = $head.clone();

				key = $head.find("label").text().replace(/-/gim, " ").replace(/\s+/gim, "-").toLowerCase();

				$clone.find("input").attr({
					"data-teacher-pos": 0,
					"data-teacher-key": key,
					"name": "teachers[0]["+key+"]"
				});

				$section.append( $clone );

				$initialElements.push( $head );

				$head = $head.next();
			}

			$group.append( $section );

			for(var fieldsReplicated = 1; fieldsReplicated<10; fieldsReplicated++){
				$clone = $section.clone();

				$clone.find("input").each(function(){
					var $this = $(this);

					key = $this.data("teacherKey");

					$this.attr({
						"name": "teachers["+fieldsReplicated+"]["+key+"]"
					}).data({
						"teacherPos": fieldsReplicated,
					});
				});

				$clone.find(".teacher-num").text( fieldsReplicated + 1 );

				$group.append( $clone );
			}

			// Remove the original ones so we can replace them all in one go
			$initialElements.map(function($ele){
				$ele.remove();
			});

			$submit.before( $group );

			$form.append( $originHiddenField );

			$form.attr({
				"action": API_URI
			});

			this.fixOntraportSuckyNaming();

			return this;
		},

		/**
		 * Fix automated names for inputs to be sensible.
		 * @return {Boolean} Always.
		 */
		"fixOntraportSuckyNaming": function(){
			$("label:contains('Organisation - School')").siblings("input").attr({
				"name": "organisation-school"
			});

			$("label:contains('City')").siblings("input").attr({
				"name": "organisation-city"
			});

			$("label:contains('State')").siblings("input").attr({
				"name": "organisation-state"
			});

			$("label:contains('Zip Code')").siblings("input").attr({
				"name": "organisation-zip"
			});

			return true;
		}
	}).init();
})();



// $(document.head).append( $('<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" async="true"></script>') );
// $(document.head).append( $('<script src="//api.temando.tronnet.me/resources/form-replicator.js" async="true"></script>') );