if [[ ! $1 ]]; then
	echo "please especify the root folder wich permissions might be set"
	echo ""
	echo "EXAMPLE"
	echo "sh fix_permissions.sh "$(pwd)
	echo ""
else
	echo "changing permissions for -R "$1" owned by "$(whoami)
	echo ""
	echo "finding httpd/nginx group"
	A=$(cut -d: -f1 /etc/group | grep http)
	B=$(cut -d: -f1 /etc/group | grep www-data)
	user=$(whoami)
	if [[ ! ${A} ]]; then
		if [[ ! ${B} ]]; then
			echo "could not find the http/www-data group, is httpd/nginx (or apache) installed? ={"
		else
			echo "group %www-data found!!!"
			usermod -aG www-data $user
			chown -R $user $1
			chgrp -R www-data $1
			echo "new ownership: "$(whoami)"@www-data"
		fi
	else
		echo "group %http found!!!"
		echo "adding "$user" to http group"
		usermod -aG http $user
		chown -R $user $1
		chgrp -R http $1
		echo "new ownership: "$user"@http"
	fi
	echo "reseting permissions as httpd/nginx rules"
	find $1 -type d -exec chmod 2775 {} \;
	find $1 -type f -exec chmod 2764 {} \;
	echo "new permissions setted: d2775 f2764 as for prodution"
	echo ""
	echo "done =}"
	echo ""
fi
