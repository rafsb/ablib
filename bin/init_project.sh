#/bin/bash -e
PDIR=$1;
if [[ ! ${PDIR} ]]; then
	echo "usage: sh init_project.sh project_name";
else
	cd ../..
	TDIR=$(pwd)
	echo "initializing ABOX's project on "$TDIR" for "$(whoami)
	echo "creating lib folder estrucutre..."
	##mkdir -p $TDIR"/../"$PDIR"/lib";
	##echo "seeting temporary folder at apps.data...";
	##mkdir -p $TDIR"/../apps.data/"$PDIR;
	##echo "installing framework...";
	##cp -rv lib/init/* .;
	echo "copying default files..."
	sudo cp -rv lib/init/* .;
	##echo "linking data folder...";
	##ln -sf $TDIR"/../apps.data/"$PDIR $TDIR"/../"$PDIR"/data";
	echo "alright";
	echo "";
	echo "organizing things...";
	echo "";
	sudo -u $(whoami) sh lib/bin/fix_permissions.sh $TDIR;
fi
echo "done =}";
echo "";
