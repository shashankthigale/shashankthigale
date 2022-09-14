<?php

namespace Drupal\user_provisioning\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\user_provisioning\moUserProvisioningConstants;

class MoUpgradePlans extends FormBase
{
    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return "mo_upgrade_plans";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        global $base_url;

        $form['markup_library'] = array(
            '#attached' => array(
                'library' => array(
                    "user_provisioning/user_provisioning.admin",
                )
            ),
        );

        $form['header_top_style_2'] = array(
            '#markup' => '<div class=""><div class=""><br>'
        );

        $customers = 'https://plugins.miniorange.com/drupal#customer';

        $form['mo_user_provisioning_header'] = array(
            '#markup' => '<div class="container-inline mo_user_provisioning_upgrade_background_note"><h1>&nbsp; UPGRADE PLANS </h1></div><br><br>'
        );

        /*        $form['mo_user_provisioning_customers'] = array(
                    '#type' => 'link',
                    '#title' => $this->t('Organizations that Trust miniOrange'),
                    '#url' => Url::fromUri($customers),
                    '#attributes' => ['class' => ['button', 'button--primary mo_user_provisioning_customer_button'], 'target' => '_blank'],
                    '#suffix' => '</h3></div><hr><br>'
                );*/


        $features = [
            [Markup::create(t('<br><h1>FREE</h1><p class="user_provisioning_pricing_rate"><sup>$</sup> 0</p><a class="button" disabled>You are on this Plan</a><br><br>')), Markup::create(t('<br><h1>PREMIUM</h1><p class="user_provisioning_pricing_rate"><sup>$</sup> 599 <sup>*</sup></p> <a class="button"  href="mailto:' . moUserProvisioningConstants::SUPPORT_EMAIL . '">Contact Us</a> <br><br>')),],
            [Markup::create(t('<h4>FEATURE LIST</h4>')), Markup::create(t('<h4>FEATURE LIST</h4>')),],
            [
                //Features of Free version

                Markup::create(t(
                    '<div class="mo_user_provisioning_feature_list">
                            <ul class="checkmark">
                                <li>Setup Drupal as SCIM CLIENT</li>
                                <li>Bearer Token based Authentication for SCIM</li>
                                <li>On-demand provisioning for Creating Users from Drupal to other applications</li>
                                <li>Audits and Logs</li>
                                <li>Export Users data in .json and .csv format file (Username and Email)</li>
                            </ul>
                           </div>'
                )),

                //Features of Premium version
                Markup::create(t(
                    '<br><h3>ALL THE FEATURES OF FREE </h3><h2> + </h2> <br>
                           <div class="mo_user_provisioning_feature_list">
                            <ul class="checkmark">
                                <li>Setup Drupal as SCIM CLIENT</li>
                                <li>Mapping of Drupal User attributes</li>
                                <li>On-demand provisioning for All Create, Read, Delete and Update operations</li>
                                <li>Real Time/On the spot Provisioning for Drupal based CRUD operations</li>
                                <li>Scheduler/Cron based Provisioning for automatic User management across all applications</li>
                                <li>Manual/Onclick User and Role Provisioning</li>
                                <li>Role specific Provisioning</li>
                                <li>Import Users from external applications/3rd party providers using .json and .csv format</li>
                                <li>Support for Provider Specific Provisioning (E.g. Cognito, Azure AD B2C, Salesforce, etc.)</li>
                            </ul>
                           </div>'
                )),
            ]
        ];


        $form['miniorange_oauth_login_feature_list'] = array(
            '#type' => 'table',
            '#responsive' => TRUE,
            '#rows' => $features,
            '#size' => 3,
            '#attributes' => ['class' => 'mo_upgrade_plans_features mo_user_prov_feature_table'],
        );

        $form['mo_user_provisioning_instance_note'] = array(
            '#type' => 'fieldset',
            '#prefix' => '<br>',
        );


        $form['mo_user_provisioning_instance_note']['miniorage_oauth_client_instance_based'] = array(
            '#markup' => t('<div class="mo_instance_note"><b>*</b> This module follows an <b>Instance Based</b> licensing structure. The listed prices are for purchase of a single instance. If you are planning to use the module on multiple instances, you can check out the bulk purchase discount on our website.</div><br>
                        <div class="mo_user_provisioning_highlight_background"><b><u>What is an Instance:</u></b> A Drupal instance refers to a single installation of a Drupal site. It refers to each individual website where the module is active. In the case of multisite/subsite Drupal setup, each site with a separate database will be counted as a single instance. For eg. If you have the dev-staging-prod type of environment then you will require 3 licenses of the module (with additional discounts applicable on pre-production environments).</div>'),
        );

        $form['markup_7'] = array(
            '#markup' => "<br><div class='mo_instance_note'><b>Return Policy - </b><br><br>
        At miniOrange, we want to ensure you are 100% happy with your purchase. If the module you purchased is not working as advertised and you've attempted to resolve any issues with our support team, which couldn't get resolved, we will refund the whole amount given that you have a raised a refund request within the first 10 days of the purchase. Please email us at <a href='mailto:drupalsupport@xecurify.com'>drupalsupport@xecurify.com</a> for any queries regarding the return policy.</div>"
        );

        return $form;

    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // TODO: Implement submitForm() method.
    }
}
