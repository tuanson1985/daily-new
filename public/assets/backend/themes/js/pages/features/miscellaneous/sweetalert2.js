!function(e){var t={};function o(n){if(t[n])return t[n].exports;var i=t[n]={i:n,l:!1,exports:{}};return e[n].call(i.exports,i,i.exports,o),i.l=!0,i.exports}o.m=e,o.c=t,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var i in e)o.d(n,i,function(t){return e[t]}.bind(null,i));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="/",o(o.s=699)}({699:function(e,t,o){e.exports=o(700)},700:function(e,t,o){"use strict";var n={init:function(){$("#kt_sweetalert_demo_1").click((function(e){Swal.fire("Good job!")})),$("#kt_sweetalert_demo_2").click((function(e){Swal.fire("Here's the title!","...and here's the text!")})),$("#kt_sweetalert_demo_3_1").click((function(e){Swal.fire("Good job!","You clicked the button!","warning")})),$("#kt_sweetalert_demo_3_2").click((function(e){Swal.fire("Good job!","You clicked the button!","error")})),$("#kt_sweetalert_demo_3_3").click((function(e){Swal.fire("Good job!","You clicked the button!","success")})),$("#kt_sweetalert_demo_3_4").click((function(e){Swal.fire("Good job!","You clicked the button!","info")})),$("#kt_sweetalert_demo_3_5").click((function(e){Swal.fire("Good job!","You clicked the button!","question")})),$("#kt_sweetalert_demo_4").click((function(e){Swal.fire({title:"Good job!",text:"You clicked the button!",icon:"success",buttonsStyling:!1,confirmButtonText:"Confirm me!",customClass:{confirmButton:"btn btn-primary"}})})),$("#kt_sweetalert_demo_5").click((function(e){Swal.fire({title:"Good job!",text:"You clicked the button!",icon:"success",buttonsStyling:!1,confirmButtonText:"<i class='la la-headphones'></i> I am game!",showCancelButton:!0,cancelButtonText:"<i class='la la-thumbs-down'></i> No, thanks",customClass:{confirmButton:"btn btn-danger",cancelButton:"btn btn-default"}})})),$("#kt_sweetalert_demo_6").click((function(e){Swal.fire({position:"top-right",icon:"success",title:"Your work has been saved",showConfirmButton:!1,timer:1500})})),$("#kt_sweetalert_demo_7").click((function(e){Swal.fire({title:"jQuery HTML example",showClass:{popup:"animate__animated animate__wobble"},hideClass:{popup:"animate__animated animate__swing"}})})),$("#kt_sweetalert_demo_8").click((function(e){Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!"}).then((function(e){e.value&&Swal.fire("Deleted!","Your file has been deleted.","success")}))})),$("#kt_sweetalert_demo_9").click((function(e){Swal.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!",cancelButtonText:"No, cancel!",reverseButtons:!0}).then((function(e){e.value?Swal.fire("Deleted!","Your file has been deleted.","success"):"cancel"===e.dismiss&&Swal.fire("Cancelled","Your imaginary file is safe :)","error")}))})),$("#kt_sweetalert_demo_10").click((function(e){Swal.fire({title:"Sweet!",text:"Modal with a custom image.",imageUrl:"https://unsplash.it/400/200",imageWidth:400,imageHeight:200,imageAlt:"Custom image",animation:!1})})),$("#kt_sweetalert_demo_11").click((function(e){Swal.fire({title:"Auto close alert!",text:"I will close in 5 seconds.",timer:5e3,onOpen:function(){Swal.showLoading()}}).then((function(e){"timer"===e.dismiss&&console.log("I was closed by the timer")}))}))}};jQuery(document).ready((function(){n.init()}))}});