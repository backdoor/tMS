#!/bin/sh

#  Copyright (c) 2011, theMeStream.  This file is
#  licensed under the Affero General Public License version 3 or later.  See
#  the COPYRIGHT.txt file.

rm -rfv ../.assembled_draft/
mkdir ../.assembled_draft
mkdir ../.assembled_draft/profile
chmod -R 777 ../.assembled_draft/profile
cp -vr ../.plusDataTest/*.* ../.assembled_draft/
cp -vr ./_htaccess ../.assembled_draft/.htaccess

cp -vr ./*.txt ../.assembled_draft/
cp -vr ./*.md ../.assembled_draft/

mkdir ../.assembled_draft/client
mkdir ../.assembled_draft/client/js
mkdir ../.assembled_draft/client/css
mkdir ../.assembled_draft/api
mkdir ../.assembled_draft/serverBots
mkdir ../.assembled_draft/serverstream

cp -vr ./settings_sys/*.* ../.assembled_draft/

cp -vr ./client/ ../.assembled_draft/
cp -vr ./serverBots/ ../.assembled_draft/
cp -vr ./serverstream/ ../.assembled_draft/


cat ./_client/js/jquery.tmpl.js \
./_client/js/jquery.json-2.2.js \
./_client/js/jquery.cookie.js \
./_client/js/tsm.indexeddb.js \
./_client/js/jquery.simpletip-1.3.1.js \
./_client/js/jquery.easing.1.3.js \
./_client/js/mousewheel.js \
./_client/js/jquery.jb.shortscroll.js \
./_client/js/jquery.Jcrop.js \
./_client/js/jquery.scrollTo.js \
./_client/js/jquery.favicon.js \
./_client/js/jquery.contextmenu.r2.js \
./_client/js/jquery.tagbox.js \
./_client/js/jquery.oembed.js \
./_client/js/sliderHistory.js \
./_client/js/navmenu.js \
./_client/js/wave.js \
./_client/js/wui_infU.js \
./_client/js/wui_frndReqs.js \
./_client/js/wui_wave.js \
./_client/js/wui_stream.js \
./_client/js/wui_following.js \
./_client/js/wui_spam.js \
./_client/js/wui_trash.js \
./_client/js/wui_widget.js \
./_client/js/users.js \
./_client/js/dnd.js \
./_client/js/controller.js \
./_client/js/wstour.js \
./_client/js/uploaderObject.js \
./_client/js/wui_loadAvat.js \
./_client/js/ajaxupload.js \
./_client/js/slide.js \
./_client/js/bbcode.js \
./_client/js/editor.js \
./_client/js/jquery.timers.js \
\
> ../.build/tmp/_themestream_max.js

# Сжатие JavaScript'a

java -jar ../.build/devTools/compiler.jar \
--compilation_level SIMPLE_OPTIMIZATIONS \
--js ../.build/tmp/_themestream_max.js \
\
--js_output_file ../.assembled_draft/client/js/_themestream.js

java -jar ../.build/devTools/compiler.jar \
--compilation_level SIMPLE_OPTIMIZATIONS \
--js ./_client/js/init.js \
\
--js_output_file ../.assembled_draft/client/js/_init.js

java -jar ../.build/devTools/compiler.jar \
--compilation_level SIMPLE_OPTIMIZATIONS \
--js ./api/widget_api_dev.js \
\
--js_output_file ../.assembled_draft/api/widget_api.js

#--compilation_level ADVANCED_OPTIMIZATIONS \
#--compilation_level SIMPLE_OPTIMIZATIONS \

gzip -c -9 -n ../.assembled_draft/client/js/_themestream.js > ../.assembled_draft/client/js/_themestream.js.gz
chown --reference=../.assembled_draft/client/js/_themestream.js ../.assembled_draft/client/js/_themestream.js.gz
touch -r ../.assembled_draft/client/js/_themestream.js ../.assembled_draft/client/js/_themestream.js.gz
#mv ../client/js/_themestream.js ../client/js/_themestream.nogzip.js
#mv ../client/js/_themestream.js.gz ../client/js/_themestream.js
gzip -c -9 -n ../.assembled_draft/client/js/_init.js > ../.assembled_draft/client/js/_init.js.gz
chown --reference=../.assembled_draft/client/js/_init.js ../.assembled_draft/client/js/_init.js.gz
touch -r ../.assembled_draft/client/js/_init.js ../.assembled_draft/client/js/_init.js.gz
#mv ../client/js/_init.js ../client/js/_init.nogzip.js
#mv ../client/js/_init.js.gz ../client/js/_init.js


cat ./_client/css/wave.css \
./_client/css/jquery.jb.shortscroll.css \
./_client/css/slide.css \
./_client/css/jquery.Jcrop.css \
./_client/css/jquerytour.css \
./_client/css/tags.css \
\
> ../.build/tmp/_amax.css

# Сжатие CSS'a

java -jar ../.build/devTools/yuicompressor-2.4.2.jar \
--type css \
../.build/tmp/_amax.css \
-o ../.assembled_draft/client/css/_amin.css

gzip -c -9 -n ../.assembled_draft/client/css/_amin.css > ../.assembled_draft/client/css/_amin.css.gz
chown --reference=../.assembled_draft/client/css/_amin.css ../.assembled_draft/client/css/_amin.css.gz
touch -r ../.assembled_draft/client/css/_amin.css ../.assembled_draft/client/css/_amin.css.gz
#mv ../client/css/_amin.css ../client/css/_amin.nogzip.css
#mv ../client/css/_amin.css.gz ../client/css/_amin.css

# Очищаем папку темп
rm -v ../.build/tmp/*.* 

# developer
mkdir ../.assembled_draft/_client
mkdir ../.assembled_draft/client/js/dev
cp -rv ./_client/js/*.* ../.assembled_draft/client/js/dev
cp -rv ./_client/css/*.* ../.assembled_draft/client/css
cp -vr ../.plusDataTest/profile/*.* ../.assembled_draft/profile/