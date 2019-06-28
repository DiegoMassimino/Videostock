(function(d,c,a,g){var e={};function b(i,h){this.$qlwapp=d(i);this.init(this)}b.prototype={init:function(i){var h=this.$qlwapp;h.on("qlwapp.init",function(j){i.mobiledevice=(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))});h.on("qlwapp.resize",function(j){if(d(this).hasClass("qlwapp-show")){d(this).trigger("qlwapp.toggle")}});h.on("qlwapp.init",function(j){if(!i.mobiledevice){h.addClass("desktop").removeClass("mobile")}else{h.addClass("mobile").removeClass("desktop")}h.addClass("qlwapp-js-ready")});h.addClass("qlwapp-js-ready").trigger("qlwapp.init");h.on("qlwapp.height",function(o){var p=d(o.delegateTarget),n=p.find(".qlwapp-body"),m=n.find(".qlwapp-carousel");var l=p.find(".qlwapp-header"),k=p.find(".qlwapp-footer"),j=(d(c).innerHeight()-l.outerHeight()-k.outerHeight());if(!i.mobiledevice){j=(d(c).innerHeight()*0.666-l.outerHeight()-k.outerHeight())}m.css({"max-height":j+"px"})});h.on("qlwapp.toggle",function(k){var l=d(k.delegateTarget),j=l.find(".qlwapp-box");l.addClass("qlwapp-transition");j.removeClass("response texting");setTimeout(function(){l.toggleClass("qlwapp-show").trigger("qlwapp.height")},10);setTimeout(function(){l.toggleClass("qlwapp-transition")},300)});h.on("click","[data-action=box], [data-action=close]",function(j){j.preventDefault();d(j.delegateTarget).trigger("qlwapp.toggle")});h.on("click","[data-action=open]",function(k){var j="https://api.whatsapp.com/send";if(!i.mobiledevice){j="https://web.whatsapp.com/send"}d(this).attr("href",j+"?phone="+d(this).data("phone")+"&text="+d(this).data("message"))});h.on("click","[data-action=previous]",function(k){k.preventDefault();var l=d(k.delegateTarget),j=l.find(".qlwapp-box");j.addClass("closing");setTimeout(function(){j.removeClass("response").removeClass("closing");j.removeClass("texting")},300)});h.on("click","[data-action=chat]",function(r){r.preventDefault();var k=d(this),x=d(r.delegateTarget),l=x.find(".qlwapp-box"),t=k.find(".qlwapp-avatar img").attr("src"),m=k.find(".qlwapp-name").text(),u=k.find(".qlwapp-label").text(),y=k.data("message"),q=k.data("phone");l.addClass("response").addClass("opening");x.trigger("qlwapp.height");setTimeout(function(){l.removeClass("opening")},300);var n=l.find(".qlwapp-reply"),j=l.find(".qlwapp-header"),o=j.find(".qlwapp-avatar img"),v=j.find(".qlwapp-number"),w=j.find(".qlwapp-name"),s=j.find(".qlwapp-label"),p=l.find(".qlwapp-message");n.data("phone",q);o.attr("src",t);v.html(q);w.html(m);s.html(u);p.html(y)});h.on("click","textarea",function(j){h.off("qlwapp.resize")});h.on("keypress","textarea",function(j){if(j.keyCode==13){h.find(".qlwapp-reply").trigger("click");setTimeout(function(){c.location=h.find(".qlwapp-reply").attr("href")},100)}});h.on("keyup","[data-action=response]",function(m){m.preventDefault();var p=d(this).find("textarea"),o=d(this).find("pre"),j=d(this).find(".qlwapp-reply"),n=d(m.delegateTarget),l=n.find(".qlwapp-box"),k=l.find(".qlwapp-buttons");o.html(p.val());setTimeout(function(){l.addClass("texting").css({"padding-bottom":o.outerHeight()});k.addClass("active");var q=p.val();j.data("message",q);if(q==""){l.removeClass("texting");k.removeClass("active")}},300)});h.trigger("qlwapp.init")},};d.fn.qlwapp=function(i){var h=arguments;if(i===g||typeof i==="object"){return this.each(function(){if(!d.data(this,"plugin_qlwapp")){d.data(this,"plugin_qlwapp",new b(this,i))}})}else{if(typeof i==="string"&&i[0]!=="_"&&i!=="init"){var j;this.each(function(){var k=d.data(this,"plugin_qlwapp");if(k instanceof b&&typeof k[i]==="function"){j=k[i].apply(k,Array.prototype.slice.call(h,1))}if(i==="destroy"){d.data(this,"plugin_qlwapp",null)}});return j!==g?j:this}}};function f(){d("div#qlwapp").qlwapp()}f();d(c).on("load",function(){f()});d(c).on("click",function(h){if(!d(h.target).closest("#qlwapp.qlwapp-show").length){d("div#qlwapp.qlwapp-show").trigger("qlwapp.toggle")}});d(c).on("resize",function(h){d("div#qlwapp").trigger("qlwapp.resize");d("div#qlwapp").trigger("qlwapp.init")})})(jQuery,window,document);