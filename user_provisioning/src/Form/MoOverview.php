<?php

namespace Drupal\user_provisioning\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user_provisioning\moUserProvisioningConstants;
use Drupal\user_provisioning\moUserProvisioningUtilities;

class MoOverview extends FormBase
{

    private $base_url;

    public function __construct()
    {
        global $base_url;
        $this->base_url = $base_url;
    }

    /**
     * @inheritDoc
     */
    public function getFormId()
    {
        return "mo_overview";
    }

    /**
     * @inheritDoc
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['mo_user_provisioning_overview_add_css'] = array(
            '#attached' => array(
                'library' => array(
                    'user_provisioning/user_provisioning.admin',
                )
            ),
        );
        $form['overview_header_style'] = array(
            '#markup' => t('<div class="mo_user_provisioning_table_layout"><div class="mo_user_provisioning_container">
                           <div class="mo_user_provisioning_tab_heading"><br>USER PROVISIONING & SYNC<hr></div><br/>'),
        );

        $form['overview_content_markup'] = array(
            '#markup' => t('
            <div style="text-align: justify">
                <b>The User Provisioning &amp; Sync module</b> will help you keep all your users and their data consistent &amp; in sync across multiple applications.
              This module will allow you to automatically create, read, modify and even delete user accounts and their profiles across your IT infrastructure and applications.
              <br><br>
              <strong>Salient features:</strong><br><br>
              <ol>
              <li id="user_provisioning">
                 <strong>User Provisioning:</strong><br>
                 <ul>
                  <li><strong><u>SCIM Protocol</u>:</strong> The System for Cross-domain Identity Management (SCIM) is a standard which is widely used to manage user identities across applications. SCIM provides a defined set of rules and representation for users, roles, groups and APIs and on how to perform CRUD operations on the same.</p>
                    <p>The miniOrange User Provisioning &amp; Sync module allows your Drupal site to act both as a SCIM Client and Server.</p>
                    <p>If any of your Applications support SCIM protocol, you can configure them from under the <a href="' . $this->base_url . moUserProvisioningConstants::USER_PROVISIONING . '">User Provisioning</a> tab of the module.</p>
                  </li>
                  <li>
                      <p><strong><u>Provider Specific Provisioning</u>:</strong> The module supports multiple inbuilt providers, providing quick and easy setup required for the provisioning operations with the provider of your choice. You can select your provider from under the User provisioning tab of the module. In case you do not find your IDP/provider listed, please reach out to us at <a href="mailto:drupalsupport@xecurify.com">drupalsupport@xecurify.com</a> and we will help you with the required setup.</p>
                  </li>
                </ul>
               </li>
               <li>
                    <p><strong>Audit &amp; Logs:</strong> As your users increase, monitoring and logging the provisioning and deprovisioning operations becomes more and more crucial. The module provides Reports and Logs informing you of all the provisioning operations, status, user performing those operations along with the timestamp and much more. There is a separate tab named <a href="' . $this->base_url . moUserProvisioningConstants::AUDITS_AND_LOGS . '">Audits &amp; Logs</a> for monitoring and reporting of provisioning and deprovisioning activities.</p>
               </li>
               <li>
                    <p><strong>Advanced Settings:</strong> The Advanced Settings tab of the module contains various configurable options including Import/Export Users, in which you can import as well as export users\' accounts and roles in bulk through APIs as well as .csv files.</p>
               </li>
               <li>
                  <p><strong>Contact Us:</strong> If you face any issues in the module or if you need any sort of assistance in configuring our module with all your applications, you can simply reach out to us by clicking on the <strong>Contact Us</strong> button, and we will help you with the required setup in no time.
                  <br><br>If you want, our Drupal technical team will also set up a call with you and help you with all your queries.<br>
                  </p>
               </li>
              </ol>
            </div>
                           '),
        );
        $form['overview_style_end'] = array(
            '#markup' => t('</div>'),
        );

        moUserProvisioningUtilities::userProvisioningConfigGuide($form, $form_state);

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
