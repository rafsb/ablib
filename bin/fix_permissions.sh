			if [[ ! $1 ]]; then
	echo "please especify the root folder wich permissions might be set"
	echo ""
	echo "EXAMPLE"
	echo "sh fix_permissions.sh "$(pwd)
	echo ""
else
	echo "changing permissions for -R "$1
	echo ""
	echo "finding httpd/nginx group"
	A=$(cut -d: -f1 /etc/group | grep http)
	B=$(cut -d: -f1 /etc/group | grep www-data)
	if [[ ! ${A} ]]; then
		if [[ ! ${B} ]]; then
			echo "could not find the http/www-data group, is httpd/nginx (or apache) installed? ={"
		else
			echo "group %www-data found!!!"
			sudo usermod -aG www-data $(whoami)
			sudo chown -R $(whoami) $1
			sudo chgrp -R www-data $1
			echo "new ownership: "$(whoami)"@www-data"
		fi
	else
		echo "group %http found!!!"
		sudo usermod -aG http $(whoami)
		sudo chown -R $(whoami) $1
		sudo chgrp -R http $1
		echo "new ownership: "$(whoami)"@http"
	fi
	echo "reseting permissions as httpd/nginx rules"
	find $1 -type d -exec sudo chmod 2755 {} \;
	find $1 -type f -exec sudo chmod 2644 {} \;
	echo "new permissions setted: d2755 f2644"
	echo ""
	echo "done =}"
	echo ""
fi
