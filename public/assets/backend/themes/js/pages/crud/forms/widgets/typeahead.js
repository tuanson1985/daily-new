!function(e){var a={};function n(t){if(a[t])return a[t].exports;var o=a[t]={i:t,l:!1,exports:{}};return e[t].call(o.exports,o,o.exports,n),o.l=!0,o.exports}n.m=e,n.c=a,n.d=function(e,a,t){n.o(e,a)||Object.defineProperty(e,a,{enumerable:!0,get:t})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,a){if(1&a&&(e=n(e)),8&a)return e;if(4&a&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(n.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&a&&"string"!=typeof e)for(var o in e)n.d(t,o,function(a){return e[a]}.bind(null,o));return t},n.n=function(e){var a=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(a,"a",a),a},n.o=function(e,a){return Object.prototype.hasOwnProperty.call(e,a)},n.p="/",n(n.s=547)}({547:function(e,a,n){e.exports=n(548)},548:function(e,a){var n,t=(n=["Alabama","Alaska","Arizona","Arkansas","California","Colorado","Connecticut","Delaware","Florida","Georgia","Hawaii","Idaho","Illinois","Indiana","Iowa","Kansas","Kentucky","Louisiana","Maine","Maryland","Massachusetts","Michigan","Minnesota","Mississippi","Missouri","Montana","Nebraska","Nevada","New Hampshire","New Jersey","New Mexico","New York","North Carolina","North Dakota","Ohio","Oklahoma","Oregon","Pennsylvania","Rhode Island","South Carolina","South Dakota","Tennessee","Texas","Utah","Vermont","Virginia","Washington","West Virginia","Wisconsin","Wyoming"],{init:function(){var e,a,t,o,i,r;$("#kt_typeahead_1, #kt_typeahead_1_modal").typeahead({hint:!0,highlight:!0,minLength:1},{name:"states",source:(e=n,function(a,n){var t;t=[],substrRegex=new RegExp(a,"i"),$.each(e,(function(e,a){substrRegex.test(a)&&t.push(a)})),n(t)})}),a=new Bloodhound({datumTokenizer:Bloodhound.tokenizers.whitespace,queryTokenizer:Bloodhound.tokenizers.whitespace,local:n}),$("#kt_typeahead_2, #kt_typeahead_2_modal").typeahead({hint:!0,highlight:!0,minLength:1},{name:"states",source:a}),t=new Bloodhound({datumTokenizer:Bloodhound.tokenizers.whitespace,queryTokenizer:Bloodhound.tokenizers.whitespace,prefetch:HOST_URL+"/api/?file=typeahead/countries.json"}),$("#kt_typeahead_3, #kt_typeahead_3_modal").typeahead(null,{name:"countries",source:t}),o=new Bloodhound({datumTokenizer:Bloodhound.tokenizers.obj.whitespace("value"),queryTokenizer:Bloodhound.tokenizers.whitespace,prefetch:HOST_URL+"/api/?file=typeahead/movies.json"}),$("#kt_typeahead_4").typeahead(null,{name:"best-pictures",display:"value",source:o,templates:{empty:['<div class="empty-message" style="padding: 10px 15px; text-align: center;">',"unable to find any Best Picture winners that match the current query","</div>"].join("\n"),suggestion:Handlebars.compile("<div><strong>{{value}}</strong> – {{year}}</div>")}}),i=new Bloodhound({datumTokenizer:Bloodhound.tokenizers.obj.whitespace("team"),queryTokenizer:Bloodhound.tokenizers.whitespace,prefetch:HOST_URL+"/api/?file=typeahead/nba.json"}),r=new Bloodhound({datumTokenizer:Bloodhound.tokenizers.obj.whitespace("team"),queryTokenizer:Bloodhound.tokenizers.whitespace,prefetch:HOST_URL+"/api/?file=typeahead/nhl.json"}),$("#kt_typeahead_5").typeahead({highlight:!0},{name:"nba-teams",display:"team",source:i,templates:{header:'<h3 class="league-name" style="padding: 5px 15px; font-size: 1.2rem; margin:0;">NBA Teams</h3>'}},{name:"nhl-teams",display:"team",source:r,templates:{header:'<h3 class="league-name" style="padding: 5px 15px; font-size: 1.2rem; margin:0;">NHL Teams</h3>'}})}});jQuery(document).ready((function(){t.init()}))}});