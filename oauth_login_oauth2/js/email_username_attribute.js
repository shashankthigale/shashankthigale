//Textfield added in case if other is selected from drop-down box in mapping tab for email or username
jQuery('#mo_oauth_email_attribute').change( function () {
  if (jQuery('#mo_oauth_email_attribute').val() == 'other'){
    jQuery('#miniorange_oauth_client_other_field_for_email').css('display','');
  }
  else{
    jQuery('#miniorange_oauth_client_other_field_for_email').css('display','none');
  }
} )

if (jQuery('#mo_oauth_email_attribute').val() == 'other'){
  jQuery('#miniorange_oauth_client_other_field_for_email').css('display','');
}

jQuery('#mo_oauth_name_attribute').change( function () {
  if (jQuery('#mo_oauth_name_attribute').val() == 'other'){
    jQuery('#miniorange_oauth_client_other_field_for_name').css('display','');
  }
  else{
    jQuery('#miniorange_oauth_client_other_field_for_name').css('display','none');
  }
} )

if (jQuery('#mo_oauth_name_attribute').val() == 'other'){
  jQuery('#miniorange_oauth_client_other_field_for_name').css('display','');
}
