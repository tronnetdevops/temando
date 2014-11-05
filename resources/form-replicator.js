(function(){
	"use strict"

	return (window.TNDOFormReplicator = {
		"init": function(){
			var $section = $("<section>"),
				$primary = $("label:contains('First Name')").parent(),
				$head = $primary,
				$form = $primary.parent(),
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

				$head = $head.next();
			}

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

				$form.append( $clone );
			}

			return this;
		}
	}).init();
})();



// $(document.head).append( $('<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js" async="true"></script>') );
// $(document.head).append( $('<script src="//api.temando.tronnet.me/resources/form-replicator.js" async="true"></script>') );