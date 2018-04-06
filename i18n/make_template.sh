#!/bin/sh

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

find $DIR/.. -type f -name *.php > phps

if [ -f messages.pot ]
then
    mv messages.pot messages.po
fi

xgettext -L PHP --keyword=l:1 -F --copyright-holder=PoliEdro -j -f phps -d messages -p $DIR

mv messages.po messages.pot

rm phps
