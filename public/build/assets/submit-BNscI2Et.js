window.addEventListener("DOMContentLoaded",function(){for(var t=document.getElementsByClassName("is-submit"),e=0;e<t.length;e++)t[e].addEventListener("change",function(){if(this.value!==""){window.location.href=this.value;var n=this.closest("form");n.submit()}});return!1});