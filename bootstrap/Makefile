BOOTSTRAP = ./docs/assets/css/bootstrap.css
BOOTSTRAP_LESS = ./less/bootstrap.less
BOOTSTRAP_RESPONSIVE = ./docs/assets/css/bootstrap-responsive.css
BOOTSTRAP_RESPONSIVE_LESS = ./less/responsive.less
DATE=$(shell date +%I:%M%p)
CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

#
# BUILD SIMPLE BOOTSTRAP DIRECTORY
# recess & uglifyjs are required
#

bootstrap:
	mkdir -p ../../../public/bootstrap/img
	mkdir -p ../../../public/bootstrap/css
	mkdir -p ../../../public/bootstrap/js
	cp img/* ../../../public/bootstrap/img/
	recess --compile ${BOOTSTRAP_LESS} > ../../../public/bootstrap/css/bootstrap.css
	recess --compress ${BOOTSTRAP_LESS} > ../../../public/bootstrap/css/bootstrap.min.css
	recess --compile ${BOOTSTRAP_RESPONSIVE_LESS} > ../../../public/bootstrap/css/bootstrap-responsive.css
	recess --compress ${BOOTSTRAP_RESPONSIVE_LESS} > ../../../public/bootstrap/css/bootstrap-responsive.min.css
	cat js/bootstrap-transition.js js/bootstrap-alert.js js/bootstrap-button.js js/bootstrap-carousel.js js/bootstrap-collapse.js js/bootstrap-dropdown.js js/bootstrap-modal.js js/bootstrap-tooltip.js js/bootstrap-popover.js js/bootstrap-scrollspy.js js/bootstrap-tab.js js/bootstrap-typeahead.js > ../../../public/bootstrap/js/bootstrap.js
	uglifyjs -nc ../../../public/bootstrap/js/bootstrap.js > ../../../public/bootstrap/js/bootstrap.min.tmp.js
	echo "/*!\n* Bootstrap.js by @fat & @mdo\n* Copyright 2012 Twitter, Inc.\n* http://www.apache.org/licenses/LICENSE-2.0.txt\n*/" > bootstrap/js/copyright.js

#
# WATCH LESS FILES
#

watch:
	echo "Watching less files..."; \
	watchr -e "watch('less/.*\.less') { system 'make' }"


.PHONY: docs watch gh-pages