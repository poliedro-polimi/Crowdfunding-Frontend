#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$( dirname "$DIR" )"

JOIN=""

if [ -f $DIR/messages.pot ]
then
    mv $DIR/messages.pot $DIR/messages.po
    JOIN="-j"
fi

find $PROJECT_DIR -iname "*.php" > $DIR/phps

if [ -f $DIR/phps ]
then
  xgettext -L PHP --keyword=l:1 -F --copyright-holder=PoliEdro $JOIN -d messages -p $DIR --from-code=UTF-8 -f $DIR/phps

  mv $DIR/messages.po $DIR/messages.pot
  rm $DIR/phps

  for i in $(find $DIR -iname "*.po");
  do
    msgmerge -U $i $DIR/messages.pot
    echo "Updated file $i "
  done

fi
