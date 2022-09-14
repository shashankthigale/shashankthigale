jQuery(document).ready(function () {
    let form_id = jQuery('form').attr("id");
    form_id = form_id.replaceAll('-','_');
    if (form_id === "mo_user_provisioning"){
        let element = document.getElementById("user_provisioning_headers");
        let url = new URL(window.location);
        let tab_name = decodeURIComponent(url.searchParams.get("tab_name"));

        const TABS_ARR = [];
        TABS_ARR['scim_server'] = 'SCIM SERVER';
        TABS_ARR['scim_client'] = 'SCIM CLIENT';
        TABS_ARR['provider_specific_provisioning'] = 'PROVIDER SPECIFIC PROVISIONING';
        let tags = document.getElementsByTagName('td');
        let found = false;
        for (let itr = 0; itr < tags.length; itr++){
            if (tags[itr]['innerText'] == TABS_ARR[tab_name]){
                found = true;
                document.getElementsByTagName('td')[itr].style = `background:white; border-bottom-color:white;`;
            }
        }
        if (found == false){
            document.getElementsByTagName('td')[0].style = 'background:white; border-bottom-color:white;';
        }
    }
})