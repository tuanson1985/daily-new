!function(e){var t={};function o(a){if(t[a])return t[a].exports;var r=t[a]={i:a,l:!1,exports:{}};return e[a].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.m=e,o.c=t,o.d=function(e,t,a){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(o.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(a,r,function(t){return e[t]}.bind(null,r));return a},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/",o(o.s=667)}({667:function(e,t,o){e.exports=o(668)},668:function(e,t,o){"use strict";function a(e,t,o){return t in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}var r,i=(r=function(){function e(){return Math.floor(21*Math.random())+20}var t=[[1,e()],[2,e()],[3,2+e()],[4,3+e()],[5,5+e()],[6,10+e()],[7,15+e()],[8,20+e()],[9,25+e()],[10,30+e()],[11,35+e()],[12,25+e()],[13,15+e()],[14,20+e()],[15,45+e()],[16,50+e()],[17,65+e()],[18,70+e()],[19,85+e()],[20,80+e()],[21,75+e()],[22,80+e()],[23,75+e()],[24,70+e()],[25,65+e()],[26,75+e()],[27,80+e()],[28,85+e()],[29,90+e()],[30,95+e()]],o=[[1,e()-5],[2,e()-5],[3,e()-5],[4,6+e()],[5,5+e()],[6,20+e()],[7,25+e()],[8,36+e()],[9,26+e()],[10,38+e()],[11,39+e()],[12,50+e()],[13,51+e()],[14,12+e()],[15,13+e()],[16,14+e()],[17,15+e()],[18,15+e()],[19,16+e()],[20,17+e()],[21,18+e()],[22,19+e()],[23,20+e()],[24,21+e()],[25,14+e()],[26,24+e()],[27,25+e()],[28,26+e()],[29,27+e()],[30,31+e()]];$.plot($("#kt_flotcharts_2"),[{data:t,label:"Unique Visits",lines:{lineWidth:1},shadowSize:0},{data:o,label:"Page Views",lines:{lineWidth:1},shadowSize:0}],{series:{lines:{show:!0,lineWidth:2,fill:!0,fillColor:{colors:[{opacity:.05},{opacity:.01}]}},points:{show:!0,radius:3,lineWidth:1},shadowSize:2},grid:{hoverable:!0,clickable:!0,tickColor:"#eee",borderColor:"#eee",borderWidth:1},colors:[KTApp.getSettings().colors.theme.base.primary,KTApp.getSettings().colors.theme.base.danger],xaxis:{ticks:11,tickDecimals:0,tickColor:"#eee"},yaxis:{ticks:11,tickDecimals:0,tickColor:"#eee"}});var a=null;$("#chart_2").bind("plothover",(function(e,t,o){if($("#x").text(t.x.toFixed(2)),$("#y").text(t.y.toFixed(2)),o){if(a!=o.dataIndex){a=o.dataIndex,$("#tooltip").remove();var r=o.datapoint[0].toFixed(2),i=o.datapoint[1].toFixed(2);!function(e,t,o){$('<div id="tooltip">'+o+"</div>").css({position:"absolute",display:"none",top:t+5,left:e+15,border:"1px solid #333",padding:"4px",color:"#fff","border-radius":"3px","background-color":"#333",opacity:.8}).appendTo("body").fadeIn(200)}(o.pageX,o.pageY,o.series.label+" of "+r+" = "+i)}}else $("#tooltip").remove(),a=null}))},{init:function(){var e,t;!function(){for(var e=[],t=0;t<2*Math.PI;t+=.25)e.push([t,Math.sin(t)]);var o=[];for(t=0;t<2*Math.PI;t+=.25)o.push([t,Math.cos(t)]);var a=[];for(t=0;t<2*Math.PI;t+=.1)a.push([t,Math.tan(t)]);$.plot($("#kt_flotcharts_1"),[{label:"sin(x)",data:e,lines:{lineWidth:1},shadowSize:0},{label:"cos(x)",data:o,lines:{lineWidth:1},shadowSize:0},{label:"tan(x)",data:a,lines:{lineWidth:1},shadowSize:0}],{colors:[KTApp.getSettings().colors.theme.base.success,KTApp.getSettings().colors.theme.base.primary,KTApp.getSettings().colors.theme.base.danger],series:{lines:{show:!0},points:{show:!0,fill:!0,radius:3,lineWidth:1}},xaxis:{tickColor:"#eee",ticks:[0,[Math.PI/2,"π/2"],[Math.PI,"π"],[3*Math.PI/2,"3π/2"],[2*Math.PI,"2π"]]},yaxis:{tickColor:"#eee",ticks:10,min:-2,max:2},grid:{borderColor:"#eee",borderWidth:1}})}(),r(),function(){for(var e=[],t=[],o=0;o<14;o+=.1)e.push([o,Math.sin(o)]),t.push([o,Math.cos(o)]);var a=$.plot($("#kt_flotcharts_3"),[{data:e,label:"sin(x) = -0.00",lines:{lineWidth:1},shadowSize:0},{data:t,label:"cos(x) = -0.00",lines:{lineWidth:1},shadowSize:0}],{colors:[KTApp.getSettings().colors.theme.base.primary,KTApp.getSettings().colors.theme.base.warning],series:{lines:{show:!0}},crosshair:{mode:"x"},grid:{hoverable:!0,autoHighlight:!1,tickColor:"#eee",borderColor:"#eee",borderWidth:1},yaxis:{min:-1.2,max:1.2}}),r=$("#kt_flotcharts_3 .legendLabel");r.each((function(){$(this).css("width",$(this).width())}));var i=null,s=null;function l(){i=null;var e=s,t=a.getAxes();if(!(e.x<t.xaxis.min||e.x>t.xaxis.max||e.y<t.yaxis.min||e.y>t.yaxis.max)){var o,l,n=a.getData();for(o=0;o<n.length;++o){var c=n[o];for(l=0;l<c.data.length&&!(c.data[l][0]>e.x);++l);var d,h=c.data[l-1],p=c.data[l];d=null==h?p[1]:null==p?h[1]:h[1]+(p[1]-h[1])*(e.x-h[0])/(p[0]-h[0]),r.eq(o).text(c.label.replace(/=.*/,"= "+d.toFixed(2)))}}}$("#kt_flotcharts_3").bind("plothover",(function(e,t,o){s=t,i||(i=setTimeout(l,50))}))}(),function(){var e,t=[];function o(){for(t.length>0&&(t=t.slice(1));t.length<250;){var e=(t.length>0?t[t.length-1]:50)+10*Math.random()-5;e<0&&(e=0),e>100&&(e=100),t.push(e)}for(var o=[],a=0;a<t.length;++a)o.push([a,t[a]]);return o}var r=(a(e={colors:[KTApp.getSettings().colors.theme.base.danger,KTApp.getSettings().colors.theme.base.primary],series:{shadowSize:1},lines:{show:!0,lineWidth:.5,fill:!0,fillColor:{colors:[{opacity:.1},{opacity:1}]}},yaxis:{min:0,max:100,tickColor:"#eee",tickFormatter:function(e){return e+"%"}},xaxis:{show:!1}},"colors",[KTApp.getSettings().colors.theme.base.primary]),a(e,"grid",{tickColor:"#eee",borderWidth:0}),e),i=$.plot($("#kt_flotcharts_4"),[o()],r);!function e(){i.setData([o()]),i.draw(),setTimeout(e,30)}()}(),function(){for(var e=[],t=0;t<=10;t+=1)e.push([t,parseInt(30*Math.random())]);var o=[];for(t=0;t<=10;t+=1)o.push([t,parseInt(30*Math.random())]);var a=[];for(t=0;t<=10;t+=1)a.push([t,parseInt(30*Math.random())]);var r=0,i=!0,s=!1,l=!1;function n(){$.plot($("#kt_flotcharts_5"),[{label:"sales",data:e,lines:{lineWidth:1},shadowSize:0},{label:"tax",data:o,lines:{lineWidth:1},shadowSize:0},{label:"profit",data:a,lines:{lineWidth:1},shadowSize:0}],{colors:[KTApp.getSettings().colors.theme.base.danger,KTApp.getSettings().colors.theme.base.primary],series:{stack:r,lines:{show:s,fill:!0,steps:l,lineWidth:0},bars:{show:i,barWidth:.5,lineWidth:0,shadowSize:0,align:"center"}},grid:{tickColor:"#eee",borderColor:"#eee",borderWidth:1}})}$(".stackControls input").click((function(e){e.preventDefault(),r="With stacking"==$(this).val()||null,n()})),$(".graphControls input").click((function(e){e.preventDefault(),i=-1!=$(this).val().indexOf("Bars"),s=-1!=$(this).val().indexOf("Lines"),l=-1!=$(this).val().indexOf("steps"),n()})),n()}(),e=function(e){for(var t=[],o=100+e,a=200+e,r=1;r<=20;r++){var i=Math.floor(Math.random()*(a-o+1)+o);t.push([r,i]),o++,a++}return t}(0),t={colors:[KTApp.getSettings().colors.theme.base.success,KTApp.getSettings().colors.theme.base.primary],series:{bars:{show:!0}},bars:{barWidth:.8,lineWidth:0,shadowSize:0,align:"left"},grid:{tickColor:"#eee",borderColor:"#eee",borderWidth:1}},$.plot($("#kt_flotcharts_6"),[{data:e,lines:{lineWidth:1},shadowSize:0}],t),function(){var e={colors:[KTApp.getSettings().colors.theme.base.primary],series:{bars:{show:!0}},bars:{horizontal:!0,barWidth:6,lineWidth:0,shadowSize:0,align:"left"},grid:{tickColor:"#eee",borderColor:"#eee",borderWidth:1}};$.plot($("#kt_flotcharts_7"),[[[10,10],[20,20],[30,30],[40,40],[50,50]]],e)}(),function(){var e=[{label:"CSS",data:10,color:KTApp.getSettings().colors.theme.base.primary},{label:"HTML5",data:40,color:KTApp.getSettings().colors.theme.base.success},{label:"PHP",data:30,color:KTApp.getSettings().colors.theme.base.danger},{label:"Angular",data:20,color:KTApp.getSettings().colors.theme.base.warning}];$.plot($("#kt_flotcharts_8"),e,{series:{pie:{show:!0}}})}(),function(){var e=[{label:"USA",data:10,color:KTApp.getSettings().colors.theme.base.primary},{label:"Germany",data:25,color:KTApp.getSettings().colors.theme.base.success},{label:"Norway",data:30,color:KTApp.getSettings().colors.theme.base.danger},{label:"Malaysia",data:15,color:KTApp.getSettings().colors.theme.base.warning},{label:"France",data:10,color:KTApp.getSettings().colors.theme.base.info}];$.plot($("#kt_flotcharts_9"),e,{series:{pie:{show:!0}},legend:{show:!1}})}(),function(){var e=[{label:"Google",data:20,color:KTApp.getSettings().colors.theme.base.primary},{label:"Twitter",data:35,color:KTApp.getSettings().colors.theme.base.success},{label:"Linkedin",data:20,color:KTApp.getSettings().colors.theme.base.danger},{label:"Instagram",data:25,color:KTApp.getSettings().colors.theme.base.warning},{label:"Facebook",data:10,color:KTApp.getSettings().colors.theme.base.info}];$.plot($("#kt_flotcharts_10"),e,{series:{pie:{show:!0,radius:1,label:{show:!0,radius:1,formatter:function(e,t){return'<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+e+"<br/>"+Math.round(t.percent)+"%</div>"},background:{opacity:.8}}}},legend:{show:!1}})}(),function(){var e=[{label:"Vue",data:13,color:KTApp.getSettings().colors.theme.base.danger},{label:"Angular",data:25,color:KTApp.getSettings().colors.theme.base.success},{label:"React",data:15,color:KTApp.getSettings().colors.theme.base.primary},{label:"Ember",data:10,color:KTApp.getSettings().colors.theme.base.warning},{label:"Backbone",data:8,color:KTApp.getSettings().colors.theme.base.info}];$.plot($("#kt_flotcharts_11"),e,{series:{pie:{show:!0,radius:1,label:{show:!0,radius:1,formatter:function(e,t){return'<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+e+"<br/>"+Math.round(t.percent)+"%</div>"},background:{opacity:.8}}}},legend:{show:!1}})}()}});jQuery(document).ready((function(){i.init()}))}});