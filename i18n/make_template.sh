#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$( dirname "$DIR" )"

if [ -f $DIR/messages.pot ]
then
    mv $DIR/messages.pot $DIR/messages.po
fi

find $PROJECT_DIR -iname "*.php" | xargs xgettext -L PHP --keyword=l:1 -F --copyright-holder=PoliEdro -j -d messages -p $DIR --from-code=UTF-8

mv $DIR/messages.po $DIR/messages.pot
