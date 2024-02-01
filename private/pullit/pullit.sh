#/bin/bash
#title              : pullit.sh
#description        : This script will download the database and import it locally.
#author             : Jens de Rond <jens@redkiwi.nl>
#date               : 18-03-2020
#version            : 0.4.1
#usage              : bash pullit.sh
#==============================================================================
RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

function green() {
  printf "${GREEN}$*${NC}\n"
}

function red() {
  printf "${RED}$*${NC}\n"
}

if ! [ -x "$(command -v jq)" ]; then
  red "Error: jq is not installed."
  green "Install this using: \`brew install jq\`" >&2
  exit 1
fi
/usr/bin/clear

# ------------------------------------------------------------------------------
# SET VARIABLES
# ------------------------------------------------------------------------------
export EXTERNAL_MYSQL_USER=$(jq -r .externalMysqlUser project.json)
export LOCAL_MYSQL_DBNAME=$(jq -r .mysqlDatabase project.json)
export REMOTE_USERNAME=$(jq -r .remoteUsername project.json)
export REMOTE_HOST_IP=$(jq -r .remoteHostIp project.json)
export REMOTE_PATH=$(jq -r .remotePath project.json)
export TOWER_USERNAME=root
export TOWER=$TOWER_USERNAME@$REMOTE_HOST_IP
# Retrieve password
export DB_PASSWORD=$(ssh -o StrictHostKeyChecking=no -o LogLevel=QUIET $TOWER "sudo -u $REMOTE_USERNAME -i cat /etc/my.$EXTERNAL_MYSQL_USER.pass")
# ------------------------------------------------------------------------------

green "Connecting over SSH with user $TOWER_USERNAME"
echo "------------------------------------------------"
green "Downloading database dump for user $EXTERNAL_MYSQL_USER"
ssh -o StrictHostKeyChecking=no -o LogLevel=QUIET -C $TOWER "export MYSQL_PWD=$DB_PASSWORD; mysqldump -u$EXTERNAL_MYSQL_USER $REMOTE_USERNAME" --no-tablespaces > db_dump.sql

echo "------------------------------------------------"

while getopts ":d" opt; do
  case ${opt} in
    * ) green "Importing database dump into DDEV..."
      ddev=true
      ddev import-db --file=db_dump.sql
      ;;
  esac
done
echo "Import done, removing dumpfile."
rm db_dump.sql

echo "------------------------------------------------"
