(function(a){var b=new Array;a.Watermark={ShowAll:function(){for(var a=0;a<b.length;a++){if(b[a].obj.val()==""){b[a].obj.val(b[a].text);b[a].obj.css("color",b[a].WatermarkColor)}else{b[a].obj.css("color",b[a].DefaultColor)}}},HideAll:function(){for(var a=0;a<b.length;a++){if(b[a].obj.val()==b[a].text)b[a].obj.val("")}}};a.fn.Watermark=function(c,d){if(!d)d="#aaa";return this.each(function(){function h(){if(e.val().length==0||e.val()==c){e.val(c);e.css("color",d)}else e.css("color",f)}function g(){if(e.val()==c)e.val("");e.css("color",f)}var e=a(this);var f=e.css("color");b[b.length]={text:c,obj:e,DefaultColor:f,WatermarkColor:d};e.focus(g);e.blur(h);e.change(h);h()})}})(jQuery);