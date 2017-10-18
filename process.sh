#!/bin/bash

function processJs {
	echo -e "\n\033[93m┌─────────── Javascript ───────────┐\033[0m"

	mkdir -p public_html/assets/js/

	# Lint source files
	if [ "$npm_package_config_lint" == "true" ]
	then
		echo 'Linting JS...'
		eslint -c '.es-lint.yml' 'raw/js/*.js'
	else
		echo -e "\033[90mDid not lint JS\033[0m"
	fi

	# Truncate final.js
	: > public_html/assets/js/final.js

	# Libs
	while read -r line || [ -n "$line" ];
	do
		cat raw/js/libs/$line >> public_html/assets/js/final.js
	done < raw/js/libs/_order.txt
	echo 'Consolidated libs'

	# Solo libs
	cp -r raw/js/libs-solo/* public_html/assets/js 2>/dev/null && echo 'Moved all solo libs'

	# Non-libs
	if [ "$npm_package_config_production" == "true" ]
	then
		cat raw/js/*.js | uglifyjs >> public_html/assets/js/final.js && echo 'Consolidated and minified non-libs'
	else
		cat raw/js/*.js >> public_html/assets/js/final.js && echo 'Consolidated non-libs'
		echo -e "\033[90mDid not minify non-libs\033[0m"
	fi

	echo -e "\033[93m└──────────────────────────────────┘\033[0m"
}

function processCss {
	echo -e "\n\033[96m┌─────────── Stylesheets ──────────┐\033[0m"

	mkdir -p public_html/assets/css/

	# Lint source files
	if [ "$npm_package_config_lint" == "true" ]
	then
		echo 'Linting SCSS...'
		sass-lint -v -q 'raw/sass/{main,site/**/*}.scss'
	else
		echo -e "\033[90mDid not lint SCSS\033[0m"
	fi

	# Process source files
	if [ "$npm_package_config_production" == "true" ]
	then
		cat raw/sass/main.scss | node-sass -q --include-path 'raw/sass/' --output-style=compressed > public_html/assets/css/main.css && echo 'Processed and minified Main SCSS'
		cat raw/sass/ie8.scss | node-sass -q --include-path 'raw/sass/' --output-style=compressed > public_html/assets/css/ie8.css && echo 'Processed and minified IE8 SCSS'
	else
		cat raw/sass/main.scss | node-sass -q --include-path 'raw/sass/' > public_html/assets/css/main.css && echo 'Processed Main SCSS'
		cat raw/sass/ie8.scss | node-sass -q --include-path 'raw/sass/' > public_html/assets/css/ie8.css && echo 'Processed IE8 SCSS'
	fi

	# Lint output files if production mode is off
	if [ "$npm_package_config_lint" == "true" ]
	then
		if [ "$npm_package_config_production" == "true" ]
		then
			echo -e "\033[90mDid not lint CSS (production mode on)\033[0m"
		else
			echo 'Linting CSS...'
			csslint --quiet --format=compact --ignore=adjoining-classes,box-sizing,box-model,font-sizes,fallback-colors,qualified-headings,unique-headings,order-alphabetical,outline-none public_html/assets/css/*.css
		fi
	else
		echo -e "\033[90mDid not lint CSS\033[0m"
	fi

	# Autoprefix output files if production mode is on
	if [ "$npm_package_config_production" == "true" ]
	then
		echo 'Prefixing CSS...'
		postcss -u autoprefixer --no-autoprefixer.remove --autoprefixer.browsers "> 1%, last 2 versions, IE 9" -d public_html/assets/css/ public_html/assets/css/main.css && echo 'Autoprefixed Main SCSS'
		postcss -u autoprefixer --no-autoprefixer.remove --autoprefixer.browsers "IE 8" -d public_html/assets/css/ public_html/assets/css/ie8.css && echo 'Autoprefixed IE8 SCSS'
	else
		echo -e "\033[90mDid not prefix CSS (production mode off)\033[0m"
	fi

	echo -e "\033[96m└──────────────────────────────────┘\033[0m"
}

function processImages {
	echo -e "\n\033[92m┌───────────── Images ─────────────┐\033[0m"

	mkdir -p public_html/assets/img/

	cp -r raw/img public_html/assets && echo 'Moved all images'

	if [ "$npm_package_config_production" == "true" ]
	then
		imagemin public_html/assets/img/ public_html/assets/img/ && echo 'Optimised all images'
	else
		echo -e "\033[90mDid not optimise images (production mode off)\033[0m"
	fi

	echo -e "\033[92m└──────────────────────────────────┘\033[0m"
}

function processFonts {
	echo -e "\n\033[95m┌────────────── Fonts ─────────────┐\033[0m"

	mkdir -p public_html/assets/fonts/

	cp -r raw/fonts public_html/assets && echo 'Moved all fonts'

	echo -e "\033[95m└──────────────────────────────────┘\033[0m"
}

function purgeAll {
	rm -rf public_html/assets/{css,fonts,img,js}/
	echo -e "\033[91m───────── Purged all assets ────────\033[0m"
}

#if [ "$npm_package_config_production" == "true" ]
#then
#	echo -e "\n\033[92m─────── Production mode is ON ──────\033[0m"
#else
#	echo -e "\n\033[91m────── Production mode is OFF ──────\033[0m"
#fi

case "$1" in
	js)
		processJs
		;;
	css)
		processCss
		;;
	img)
		processImages
		;;
	fonts)
		processFonts
		;;
	purge)
		purgeAll
		;;
	*)
		purgeAll
		processJs
		processCss
		processImages
		processFonts
esac
