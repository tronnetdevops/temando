(function(){
	"use strict"

	return (window.TNDOFormReplicator = {
		"init": function(){
			var $group = $("<div>"),
				$section = $("<div><h3 class='teacher-num'>1</h3></div>").css({
					"border-top": "1px solid rgba(0,0,0,0.6)"
				}).addClass("moonray-form-element-wrapper moonray-form-input-type-text"),
				$primary = $("label:contains('First Name')").parent(),
				$head = $primary,
				$form = $primary.parent(),
				$submit = $form.find("input[type='submit']").parent(),
				$initialElements = [],
				$clone, key;

			for(var fieldsCloned=0; fieldsCloned<5; fieldsCloned++){
				key = $head.find("label").text().replace(" ", "-").toLowerCase();

				$head.find("input").attr({
					"name": "teachers[0]["+key+"]"
				}).data({
					"teacherPos": 0,
					"key": key
				});

				$section.append( $head.clone() );

				$initialElements.push( $head );

				$head = $head.next();
			}

			$group.append( $section );

			for(var fieldsReplicated = 1; fieldsReplicated<10; fieldsReplicated++){
				$clone = $section.clone();

				$clone.find("input").each(function(){
					var $this = $(this);

					key = $this.data("key");

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

			return this;
		}
	}).init();
})();



// $(document.head).append( $('<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" async="true"></script>') );
// $(document.head).append( $('<script src="//api.temando.tronnet.me/resources/form-replicator.js" async="true"></script>') );