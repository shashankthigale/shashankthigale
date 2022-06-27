jQuery(document).ready(function(){
  jQuery("li").click(function() {
    if (jQuery(this).hasClass("mo-flex-item")) {
      window.location.href = 'configure_app/' + this.id;
    }
  });
});

function searchApp() {
  var input, filter, ul, li, a, i, txtValue, oidc_div, ul1, li1, a1, j, oauth_div, count_oauth, count_oidc;
  input = document.getElementById("mo_text_search");
  filter = input.value.toUpperCase();
  ul = document.getElementById("mo_search_ul");
  li = ul.getElementsByTagName("li");
  oauth_div = document.getElementById("oauth_apps");
  count_oauth = 0;
  count_oidc = 0;

  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
      count_oauth ++;
    } else {
      li[i].style.display = "none";
    }
  }

  oidc_div = document.getElementById("oidc_apps");
  ul1 = document.getElementById("mo_search_ul_oidc");
  li1 = ul1.getElementsByTagName("li");

  for (j = 0; j < li1.length; j++) {
    a1 = li1[j].getElementsByTagName("a")[0];
    txtValue = a1.textContent || a1.innerText;
    console.log(txtValue.toUpperCase());
    console.log(txtValue.toUpperCase().indexOf(filter));
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li1[j].style.display = "";
      count_oidc ++;
    } else {
      li1[j].style.display = "none";
    }
  }

  oauth_div.style.visibility = count_oauth <= 0 ? "hidden" : "visible";
  oidc_div.style.visibility = count_oidc <= 0 ? "hidden" : "visible";

}

function CopyToClipboard(element){
  jQuery(".selected-text").removeClass("selected-text");
  let textToCopy = document.getElementById('Callback_textfield').innerText;
  navigator.clipboard.writeText(textToCopy);
  jQuery(element).addClass("selected-text");
}

jQuery(window).click(function(e) {
  if( e.target.className === undefined || e.target.className.indexOf("copy_button") === -1)
    jQuery(".selected-text").removeClass("selected-text");
});


async function test_configuration_window(){
  var base_url = window.location.href.split('/admin');
  var finalUrl = base_url[0]+'/testConfig';
  var myWindow = window.open(finalUrl, "TEST OAUTH LOGIN", "scrollbars=1 width=800, height=600");
}
