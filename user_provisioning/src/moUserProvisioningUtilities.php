<?php

namespace Drupal\user_provisioning;

use Drupal;
use Drupal\Core\Render\Markup;
use Drupal\user\Entity\User;

class moUserProvisioningUtilities
{
    public static function customUserFields(): array
    {
        $custom_fields = array('' => 'Select Attribute Value');
        $usr = User::load(Drupal::currentUser()->id());
        $usrVal = $usr->toArray();
        foreach ($usrVal as $key => $value) {
            $custom_fields[$key] = $key;
        }
        return $custom_fields;
    }

    public static function mo_get_drupal_core_version()
    {
        return DRUPAL::VERSION[0];
    }

    public static function userProvisioningConfigGuide(array &$form, \Drupal\Core\Form\FormStateInterface $form_state)
    {

        $form['miniorange_user_provisioning_guide'] = array(
            '#markup' => '<div class="mo_user_provisioning_table_layout_setup_guide mo_user_provisioning_container_2" id="mo_guide_vt">
                    <div style="font-size: 15px;">To see detailed documentation of how to configure Drupal User Provisioning Module</div></br>',
        );

        $mo_Wordpress = Markup::create('<strong><a href="' . moUserProvisioningConstants::WORDPRESS_GUIDE . '" class="mo_guide_text-color" target="_blank">Wordpress</a></strong>');
        $mo_AWS_SSO = Markup::create('<strong><a href="' . moUserProvisioningConstants::AWS_SSO_GUIDE . '" class="mo_guide_text-color" target="_blank">AWS SSO</a></strong>');
        $mo_Drupal = Markup::create('<strong><a href="' . moUserProvisioningConstants::DRUPAL_GUIDE . '" class="mo_guide_text-color" target="_blank">Drupal</a></strong>');
        $mo_Joomla = Markup::create('<strong><a href="' . moUserProvisioningConstants::JOOMLA_GUIDE . '" class="mo_guide_text-color" target="_blank">Joomla</a></strong>');

        $mo_table_content = array(
            array($mo_Wordpress, $mo_AWS_SSO),
            array($mo_Drupal, $mo_Joomla),
        );
        $header = array(array(
            'data' => t('User Provisioning Setup Guides'),
            'colspan' => 2,

        ),
        );

        $form['modules'] = array(
            '#type' => 'table',
            '#header' => $header,
            '#rows' => $mo_table_content,
            '#attributes' => ['class' => ['setup_guides']],
            '#responsive' => TRUE,
        );
        $form['miniorange_user_provisioning_guide_end'] = array(
            '#markup' => '</div>',
        );
    }

}
