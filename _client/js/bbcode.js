/*
  Copyright (c) 2011, theMeStream.  This file is
  licensed under the Affero General Public License version 3 or later.  See
  the COPYRIGHT.txt file.
*/
// Конструктор регулярных выражений http://www.regexpres.narod.ru/calculator.html

function rep2(content, re, str) {
	return content.replace(re, str);
}

function html2bbcode2(content) {
        content=rep2(content,/\\n/gi,"[br]");
        content=rep2(content,/'\n'/gi,"[br]");
        content=rep2(content,/"\n"/gi,"[br]");
	content=rep2(content,/\n/gi, "[br]");
        content=rep2(content,/<br(\s[^<>]*)?>/gi,"[br]");
        content=rep2(content,/<\/p>/gi, "[br]");

        content=rep2(content,/<input type=\"hidden\" id=\"([^<>]*?)\" value=\"([^<>]*?)\">/gi, "[varwg=$1]$2[/varwg]");
        //content=rep2(content,/<iframe src=\"serverstream\/widget.php\?u=([^<>]*?)\" id=\"wg_([^<>]*?)\" onload=\"([^<>]*?)\" width=\"([^<>]*?)\"><\/iframe>/gi, "[widget]$1[/widget]");
        content=rep2(content,/<iframe(\s[^<>]*)?src=\"http:\/\/themestream.ru\/store\/widget.php\?u=([^<>]*?)\"(\s[^<>]*)?><\/iframe>/gi, "[widget]$2[/widget]");
	
	//2011.05.16 - content=rep2(content,/<iframe(\s[^<>]*)?src=\"https:\/\/www.youtube.com\/embed\/([^<>]*?)?rel=0\"(\s[^<>]*)?><\/iframe>/gi, "[video=youtube]$2[/video]");	

	content=rep2(content,/<img\s[^<>]*?src=\"?([^<>]*?)\"?(\s[^<>]*)?\/?>/gi,"[img]$1[/img]");
	content=rep2(content,/<\/(strong|b)>/gi, "[/b]");
	content=rep2(content,/<(strong|b)(\s[^<>]*)?>/gi,"[b]");
	content=rep2(content,/<\/(em|i)>/gi,"[/i]");
	content=rep2(content,/<(em|i)(\s[^<>]*)?>/gi,"[i]");
	content=rep2(content,/<\/u>/gi, "[/u]");
	content=rep2(content,/\n/gi, " ");
	content=rep2(content,/\r/gi, " ");
	content=rep2(content,/<u(\s[^<>]*)?>/gi, "[u]");
	content=rep2(content,/<div><br(\s[^<>]*)?>/gi, "<div>");//chrome-safari fix to prevent double linefeeds

	content=rep2(content,/<p(\s[^<>]*)?>/gi,"");
	content=rep2(content,/<ul>/gi, "[ul]");
	content=rep2(content,/<\/ul>/gi, "[/ul]");
	content=rep2(content,/<ol>/gi, "[ol]");
	content=rep2(content,/<\/ol>/gi, "[/ol]");
	content=rep2(content,/<li>/gi, "[li]");
	content=rep2(content,/<\/li>/gi, "[/li]");
	content=rep2(content,/<\/div>\s*<div([^<>]*)>/gi, "</span>[br]<span$1>");//chrome-safari fix to prevent double linefeeds
	content=rep2(content,/<div([^<>]*)>/gi,"[br]<span$1>");
	content=rep2(content,/<\/div>/gi,"</span>[br]");
	content=rep2(content,/&nbsp;/gi," ");
	content=rep2(content,/&quot;/gi,"\"");
	content=rep2(content,/&amp;/gi,"&");
	var sc, sc2;
	do {
		sc = content;
		content=rep2(content,/<font\s[^<>]*?color=\"?([^<>]*?)\"?(\s[^<>]*)?>([^<>]*?)<\/font>/gi,"[color=$1]$3[/color]");
		if(sc==content)
			content=rep2(content,/<font[^<>]*>([^<>]*?)<\/font>/gi,"$1");
		
		//2011.05.16 - content=rep2(content,/<a\s[^<>]*?href=\"?http:\/\/[a-zA-Z\-\_]+\.youtube\.com\/watch\?v=([0-9a-zA-Z\-\_]{1,15})[^<>]*"?(\s[^<>]*)?>([^<>]*?)<\/a>/gi,"[video=youtube]$1[/video]");
		
		content=rep2(content,/<a\s[^<>]*?href=\"?([^<>]*?)\"?(\s[^<>]*)?>([^<>]*?)<\/a>/gi,"[url=$1]$3[/url]");
		//<OBJECT width="470" height="353"><PARAM name="movie" value="http://video.rutube.ru/e02dc0511654ec249caf66fde744b043"></PARAM><PARAM name="wmode" value="window"></PARAM><PARAM name="allowFullScreen" value="true"></PARAM><EMBED src="http://video.rutube.ru/e02dc0511654ec249caf66fde744b043" type="application/x-shockwave-flash" wmode="window" width="470" height="353" allowFullScreen="true" ></EMBED></OBJECT>
		//http://rutube.ru/tracks/4043904.html?v=e02dc0511654ec249caf66fde744b043
		//
		//<iframe src="http://vk.com/video_ext.php?oid=85130883&id=160139870&hash=e4c36526fba08a3a&sd" width="607" height="360" frameborder="0"></iframe>
		//http://vk.com/video85130883_160139870
		//http://vk.com/id85130883
		sc2 = content;
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?font-weight: ?bold;?\"?\s*([^<]*?)<\/\1>/gi,"[b]<$1 style=$2</$1>[/b]");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?font-weight: ?normal;?\"?\s*([^<]*?)<\/\1>/gi,"<$1 style=$2</$1>");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?font-style: ?italic;?\"?\s*([^<]*?)<\/\1>/gi,"[i]<$1 style=$2</$1>[/i]");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?font-style: ?normal;?\"?\s*([^<]*?)<\/\1>/gi,"<$1 style=$2</$1>");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?text-decoration: ?underline;?\"?\s*([^<]*?)<\/\1>/gi,"[u]<$1 style=$2</$1>[/u]");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?text-decoration: ?none;?\"?\s*([^<]*?)<\/\1>/gi,"<$1 style=$2</$1>");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?color: ?([^<>]*?);\"?\s*([^<]*?)<\/\1>/gi, "[color=$2]<$1 style=$3</$1>[/color]");
		content=rep2(content,/<(span|blockquote|pre)\s[^<>]*?style=\"?font-family: ?([^<>]*?);\"?\s*([^<]*?)<\/\1>/gi, "[font=$2]<$1 style=$3</$1>[/font]");
		content=rep2(content,/<(blockquote|pre)\s[^<>]*?style=\"?\"? (class=|id=)([^<>]*)>([^<>]*?)<\/\1>/gi, "<$1 $2$3>$4</$1>");
		content=rep2(content,/<pre>([^<>]*?)<\/pre>/gi, "[code]$1[/code]");
		content=rep2(content,/<span\s[^<>]*?style=\"?\"?>([^<>]*?)<\/span>/gi, "$1");
		if(sc2==content) {
			content=rep2(content,/<span[^<>]*>([^<>]*?)<\/span>/gi, "$1");
			sc2 = content;
		}
	}while(sc!=content)
	content=rep2(content,/<[^<>]*>/gi,"");
	content=rep2(content,/&lt;/gi,"<");
	content=rep2(content,/&gt;/gi,">");

	do {
		sc = content;
		content=rep2(content,/\[(b|i|u)\]\[quote([^\]]*)\]([\s\S]*?)\[\/quote\]\[\/\1\]/gi, "[quote$2][$1]$3[/$1][/quote]");
		content=rep2(content,/\[color=([^\]]*)\]\[quote([^\]]*)\]([\s\S]*?)\[\/quote\]\[\/color\]/gi, "[quote$2][color=$1]$3[/color][/quote]");
		content=rep2(content,/\[(b|i|u)\]\[code\]([\s\S]*?)\[\/code\]\[\/\1\]/gi, "[code][$1]$2[/$1][/code]");
		content=rep2(content,/\[color=([^\]]*)\]\[code\]([\s\S]*?)\[\/code\]\[\/color\]/gi, "[code][color=$1]$2[/color][/code]");
	}while(sc!=content)

	//clean up empty tags
	do {
		sc = content;
		content=rep2(content,/\[b\]\[\/b\]/gi, "");
		content=rep2(content,/\[i\]\[\/i\]/gi, "");
		content=rep2(content,/\[u\]\[\/u\]/gi, "");
		content=rep2(content,/\[quote[^\]]*\]\[\/quote\]/gi, "");
		content=rep2(content,/\[code\]\[\/code\]/gi, "");
		content=rep2(content,/\[url=([^\]]+)\]\[\/url\]/gi, "");
		content=rep2(content,/\[img\]\[\/img\]/gi, "");
		content=rep2(content,/\[color=([^\]]*)\]\[\/color\]/gi, "");
	}while(sc!=content)
        return content;
}

function bbcode2html2(content) {
    var randomValue=Math.random();
    randomValue=randomValue.toString();
    randomValue=randomValue.replace(".", "_");
	// example: [b] to <strong>
	content=rep2(content,/\</gi,"&lt;"); //removing html tags
	content=rep2(content,/\>/gi,"&gt;");

        content=rep2(content,/\\n/gi,"<br />");
        content=rep2(content,/'\n'/gi,"<br />");
        content=rep2(content,/"\n"/gi,"<br />");
	content=rep2(content,/\n/gi, "<br />");
        content=rep2(content,/\[br\]/gi, "<br />");

        content=rep2(content,/\[varwg=([^\]]*?)\]([\s\S]*?)\[\/varwg\]/gi, "<input type=\"hidden\" id=\"$1\"  value=\"$2\">");
        content=rep2(content,/\[widget\]([^<>]*?)\[\/widget\]/gi,"<iframe class='framewidget' src=\""+$_SYS_SITEPROJECT+"store/widget.php?u=$1\" id=\"wg_"+randomValue+"\" onload=\"activateDataWidget('"+randomValue+"')\" width=\"100%\"></iframe>");
	
	//2011.05.16 - content=rep2(content,/\[video=youtube\]([^<>]*?)\[\/video\]/gi,"<iframe width=\"560\" height=\"349\" src=\"https://www.youtube.com/embed/$1?rel=0\" frameborder=\"0\" allowfullscreen></iframe>");

	content=rep2(content,/\[ul\]/gi, "<ul>");
	content=rep2(content,/\[\/ul\]/gi, "</ul>");
	content=rep2(content,/\[ol\]/gi, "<ol>");
	content=rep2(content,/\[\/ol\]/gi, "</ol>");
	content=rep2(content,/\[li\]/gi, "<li>");
	content=rep2(content,/\[\/li\]/gi, "</li>");
	if(browser) {
		content=rep2(content,/\[b\]/gi,"<strong>");
		content=rep2(content,/\[\/b\]/gi,"</strong>");
		content=rep2(content,/\[i\]/gi,"<em>");
		content=rep2(content,/\[\/i\]/gi,"</em>");
		content=rep2(content,/\[u\]/gi,"<u>");
		content=rep2(content,/\[\/u\]/gi,"</u>");
	}else {
		content=rep2(content,/\[b\]/gi,"<span style=\"font-weight: bold;\">");
		content=rep2(content,/\[i\]/gi,"<span style=\"font-style: italic;\">");
		content=rep2(content,/\[u\]/gi,"<span style=\"text-decoration: underline;\">");
		content=rep2(content,/\[\/(b|i|u)\]/gi,"</span>");
	}
	content=rep2(content,/\[img\]([^\"]*?)\[\/img\]/gi,"<img src=\"$1\" />");
        //content=rep2(content,/\[img\]([^\"]*?)\[\/img\]/gi,"<a href=\"$1\" rel=\"single\"  class=\"pirobox\" title=\"\"><img src=\"$1\" width=\"25%\" /></a>");

	var sc;
	do {
		sc = content;

                content=rep2(content,/\[url=(http|https|ftp|stream)\:\/\/(themestream\.ru|theMeStream\.ru|beta\.themestream\.ru|localhost)\/([^\]]+)\]([\s\S]*?)\[\/url\]/gi,"<a href=\"javascript:goLinkStream('$3')\" title=\"StreamLink\" name=\"0\" >$4</a>");

		content=rep2(content,/\[url=([^\]]+)\]([\s\S]*?)\[\/url\]/gi,"<a href=\"$1\" name=\"1\" class=\"oembed\" target=\"_black\">$2</a>");
		content=rep2(content,/\[url\]([\s\S]*?)\[\/url\]/gi,"<a href=\"$1\" name=\"2\" class=\"oembed\" target=\"_black\">$1</a>");
		
		if(browser) {
		    content=rep2(content,/\[color=([^\]]*?)\]([\s\S]*?)\[\/color\]/gi, "<font color=\"$1\">$2</font>");
		    content=rep2(content,/\[font=([^\]]*?)\]([\s\S]*?)\[\/font\]/gi, "<font face=\"$1\">$2</font>");
		} else {
		    content=rep2(content,/\[color=([^\]]*?)\]([\s\S]*?)\[\/color\]/gi, "<span style=\"color: $1;\">$2</span>");
		    content=rep2(content,/\[font=([^\]]*?)\]([\s\S]*?)\[\/font\]/gi, "<span style=\"font-family: $1;\">$2</span>");
		}
		content=rep2(content,/\[code\]([\s\S]*?)\[\/code\]/gi,"<pre>$1</pre>&nbsp;");
	}while(sc!=content);
        return content;
}

// Переход содержания ПОТОКА по внутренней ссылке
function goLinkStream(hashUrl) {
    var hashUrlext=hashUrl;
    hashUrl=hashUrl.replace('#','');
    hashUrl=hashUrl.replace('?act=view&ids=','');
    var massiveHash=hashUrl.split(':');
    var userIDme=$.cookie("profileUserMe");
    if((userIDme != undefined) & (userIDme != '') & (userIDme != 'null')) {
	//location.href=hashUrlext;
	if (massiveHash[0].indexOf('user=') >= 0) {
	    // переход к ПОЛЬЗОВАТЕЛЮ
	    var idUserGo=massiveHash[0].replace('user=','');
	    var idPanelGo=massiveHash[1].replace('panel=','');
	    profileUsersAva(idUserGo); // Переходим к пользователю
	    if(idPanelGo=="stream") { //Потоки
		listwaves(idUserGo);
	    }
	} else {
	    // переход к ПОТОКУ
	    var idStreamGo=massiveHash[0].replace('stream=','');
	    var idBlipGo="";
	    if (massiveHash[1] != undefined) {
		idBlipGo=massiveHash[1].replace('blip=','');
	    }
	    if ((idBlipGo != undefined) & (idBlipGo != "") & (idBlipGo != '') & (idBlipGo != 'null') & (idBlipGo != 0)  & (idBlipGo != '0')  & (idBlipGo != "0")) {
		location.href="#stream="+idStreamGo+":blip="+idBlipGo;
		waveContent(idStreamGo,idBlipGo);
	    } else {
		location.href="#stream="+idStreamGo;
		waveContent(idStreamGo,'');
	    }
	}
    } else {
        var idStreamGo=massiveHash[0].replace('stream=','');
	var idBlipGo="";
	    if (massiveHash[1] != undefined) {
		idBlipGo=massiveHash[1].replace('blip=','');
	    }
        console.info("idStreamGo="+idStreamGo+";");
        if(idStreamGo!="0" & idStreamGo!='0' & idStreamGo!=0) {
	    if(idBlipGo!="0" & idBlipGo!='0' & idBlipGo!=0 & idBlipGo!='' & idBlipGo!="") {
		location.href='./?act=view&ids='+idStreamGo+'&idb='+idBlipGo;
	    } else {
		location.href='./?act=view&ids='+idStreamGo;
	    }
        } else {
            location.href='./';
        }
        //waveContentVeiwPage(massiveHash[0].replace('stream=',''));
    }
}
