<?php
/**
 * @file
 * Contains Licensing information for miniOrange OAuth Login Module.
 */

/**
 * Showing Licensing form info.
 */
namespace Drupal\oauth_login_oauth2\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\oauth_login_oauth2\Utilities;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MiniorangeLicensing extends FormBase {

    public function getFormId() {
        return 'miniorange_oauth_client_licensing';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        global $base_url;

        $form['markup_library'] = array(
            '#attached' => array(
                'library' => array(
                    "oauth_login_oauth2/oauth_login_oauth2.admin",
                    "oauth_login_oauth2/oauth_login_oauth2.style_settings",
                )
            ),
        );

        if (!Utilities::isCustomerRegistered()) {
            $username = \Drupal::config('oauth_login_oauth2.settings')->get('miniorange_oauth_client_customer_admin_email');
            $URL_Redirect_std = "https://login.xecurify.com/moas/login?username=" . $username . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=drupal8_oauth_client_standard_plan";
            $URL_Redirect_pre = "https://login.xecurify.com/moas/login?username=" . $username . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=drupal8_oauth_client_premium_plan";
            $URL_Redirect_ent = "https://login.xecurify.com/moas/login?username=" . $username . "&redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=drupal8_oauth_client_enterprise_plan";
            $targetBlank = 'target="_blank"';
        } else {
            $URL_Redirect_std = $base_url . '/admin/config/people/oauth_login_oauth2/customer_setup';
            $URL_Redirect_pre = $base_url . '/admin/config/people/oauth_login_oauth2/customer_setup';
            $URL_Redirect_ent = $base_url . '/admin/config/people/oauth_login_oauth2/customer_setup';
            $targetBlank = '';
        }

        $form['header_top_style_2'] = array(
            '#markup' => '<div class="mo_oauth_table_layout_1"><div class="mo_oauth_table_layout"><br>'
        );

        $referer = \Drupal::request()->headers->get('referer');
        $referer = isset($referer) && !empty($referer) ? $referer : $base_url . '/oauth_login_oauth2/config_clc';
        $customers = 'https://plugins.miniorange.com/drupal#customer';

        $form['miniorage_module_back_button'] = array(
            '#type' => 'link',
            '#title' => t('&#11164; &nbsp;Back'),
            '#url' => Url::fromUri($referer),
            '#attributes' => ['class' => ['button', 'button--primary']],
        );

        $form['miniorage_oauth_client_cusotmers'] = array(
            '#type' => 'link',
            '#title' => t('Organizations that Trust miniOrange'),
            '#url' => Url::fromUri($customers),
            '#attributes' => ['class' => ['button', 'button--primary'], 'target' => '_blank'],
        );

        $form['markup_1'] = array(
            '#markup' => '<br><br><h3>&nbsp; UPGRADE PLANS </h3><hr><br>'
        );

        $features = [
            [ Markup::create(t('<h3>FEATURES / PLANS</h3>')), Markup::create(t('<br><h2>FREE</h2> <p class="mo_oauth_pricing-rate"><sup>$</sup> 0</p>')), Markup::create(t('<br><h2>STANDARD</h2><p class="mo_oauth_pricing-rate"><sup>$</sup> 249 <sup>*</sup></p>')), Markup::create(t('<br><h2>PREMIUM</h2><p class="mo_oauth_pricing-rate"><sup>$</sup> 399 <sup>*</sup></p>')), Markup::create(t('<br><h2>ENTERPRISE</h2><p class="mo_oauth_pricing-rate"><sup>$</sup> 449 <sup>*</sup></p>')),],
            [ '', Markup::create(t('<a class="button" disabled>You are on this Plan</a>')), Markup::create(t('<a class="button button--primary" target="'.$targetBlank.'" href="'.$URL_Redirect_std.'">Upgrade Now</a>')), Markup::create(t('<a class="button button--primary" target="'.$targetBlank.'" href="'.$URL_Redirect_pre.'">Upgrade Now</a>')), Markup::create(t('<a class="button button--primary" target="'.$targetBlank.'" href="'.$URL_Redirect_ent.'">Upgrade Now</a>'))],
            [ Markup::create(t('OAuth Provider Support')), Markup::create(t('1')), Markup::create(t('1')), Markup::create(t('1')), Markup::create(t('Multiple **')),   ],
            [ Markup::create(t('Autofill OAuth servers configuration')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Basic Attribute Mapping (Email)')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Export Configuration')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Auto Create Users')), Markup::create(t('-')), Markup::create(t('Unlimited')), Markup::create(t('Unlimited')), Markup::create(t('Unlimited')),   ],
            [ Markup::create(t('Import Configuration')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Advanced Attribute Mapping (Username, Email, First Name, Custom Attributes, etc.)')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Custom Redirect URL after login and logout')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Customized Login Button')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Basic Role Mapping (Support for default role for new users)')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Advanced Role Mapping')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Force authentication / Protect complete site')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('OpenID Connect Support (Login using OpenID Connect Server)')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Support for Headless integration')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Domain specific registration')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Dynamic Callback URL')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Support for Group Info Endpoint')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Support for PKCE flow')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
            [ Markup::create(t('Login Reports / Analytics')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('')), Markup::create(t('&#x2714;')),   ],
        ];

        $form['miniorange_oauth_login_feature_list'] = array(
            '#type' => 'table',
            '#responsive' => TRUE,
            '#rows' => $features,
            '#size' => 5,
            '#attributes' => ['class' => 'mo_upgrade_plans_features'],
        );

        $form['miniorage_oauth_client_instance_based'] = array(
            '#markup' => t('<br><div class="mo_instance_note"><b>*</b> This module follows an <b>Instance Based</b> licensing structure. The listed prices are for purchase of a single instance. If you are planning to use the module on multiple instances, you can check out the bulk purchase discount on our website.</div><br>
                        <div class="mo_oauth_client_highlight_background_note_3"><b><u>What is an Instance:</u></b> A Drupal instance refers to a single installation of a Drupal site. It refers to each individual website where the module is active. In the case of multisite/subsite Drupal setup, each site with a separate database will be counted as a single instance. For eg. If you have the dev-staging-prod type of environment then you will require 3 licenses of the module (with additional discounts applicable on pre-production environments).</div><br>
                        <div class="mo_instance_note"> <b>**</b> There is an additional cost for the OAuth Providers if the number of OAuth Provider is more than 1.</div>'),
        );

        $rows = [
            [ Markup::create(t('<b>1.</b> Click on Upgrade Now button for required licensed plan and you will be redirected to miniOrange login console.</li>')), Markup::create(t('<b>5.</b> Uninstall and then delete the free version of the module from your Drupal site.')) ],
            [ Markup::create(t('<b>2.</b> Enter your username and password with which you have created an account with us. After that you will be redirected to payment page.')), Markup::create(t('<b>6.</b> Now install the downloaded licensed version of the module.')) ],
            [ Markup::create(t('<b>3.</b> Enter your card details and proceed for payment. On successful payment completion, the Licensed version module(s) will be available to download.')), Markup::create(t('<b>7.</b> Clear Drupal Cache from <a href="http://localhost/drupal9312/web/admin/config/development/performance" >here</a>.')) ],
            [ Markup::create(t('<b>4.</b> Download the licensed module(s) from Module Releases and Downloads section.')), Markup::create(t('<b>8.</b> After enabling the licensed version of the module, login using the account you have registered with us.')) ],
        ];

        $form['miniorange_oauth_login_how_to_upgrade'] =[
            '#markup' => t('<br><hr><br>'),
        ];


        $form['miniorange_oauth_login_how_to_upgrade_table'] = array(
            '#type' => 'table',
            '#responsive' => TRUE,
            '#header' => [
                'how_to_upgrade' => [
                    'data' => 'HOW TO UPGRADE TO LICENSED VERSION MODULE',
                    'colspan' => 2,
                ],
            ],
            '#rows' => $rows,
            '#attributes' => ['style' => 'border:groove', 'class' => ['mo_how_to_upgrade']],
        );

        $form['markup_7'] = array(
            '#markup' => "<br><div class='mo_instance_note'><b>Return Policy - </b><br><br>
        At miniOrange, we want to ensure you are 100% happy with your purchase. If the module you purchased is not working as advertised and you've attempted to resolve any issues with our support team, which couldn't get resolved, we will refund the whole amount given that you have a raised a refund request within the first 10 days of the purchase. Please email us at <a href='mailto:drupalsupport@xecurify.com'>drupalsupport@xecurify.com</a> for any queries regarding the return policy.</div>"
        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {

    }
}

