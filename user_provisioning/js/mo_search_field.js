function searchApp() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("mo_text_search");
    filter = input.value.toUpperCase();
    ul = document.getElementById("mo_search_ul");
    li = ul.getElementsByTagName("li");

    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

function CopyToClipboard(element) {
    jQuery(".selected-text").removeClass("selected-text");
    let textToCopy = document.getElementById('Callback_textfield').innerText;
    navigator.clipboard.writeText(textToCopy);
    jQuery(element).addClass("selected-text");
}

jQuery(window).click(function (e) {
    if (e.target.className === undefined || e.target.className.indexOf("copy_button") === -1)
        jQuery(".selected-text").removeClass("selected-text");
});


async function test_configuration_window() {
    var base_url = window.location.href.split('/admin');
    var finalUrl = base_url[0] + '/testConfig';
    var myWindow = window.open(finalUrl, "TEST OAUTH LOGIN", "scrollbars=1 width=800, height=600");
}
