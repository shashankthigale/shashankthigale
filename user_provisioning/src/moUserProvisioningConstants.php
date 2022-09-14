<?php

namespace Drupal\user_provisioning;

class moUserProvisioningConstants
{
    //Tabs relative URLs
    const OVERVIEW = '/admin/config/people/user_provisioning/Overview';
    const USER_PROVISIONING = '/admin/config/people/user_provisioning/user_provisioning';
    const AUDITS_AND_LOGS = '/admin/config/people/user_provisioning/audits_and_logs';
    const ADVANCED_SETTINGS = '/admin/config/people/user_provisioning/advanced_settings';
    const UPGRADE_PLANS = '/admin/config/people/user_provisioning/upgrade_plans';
    const SUPPORT_EMAIL = 'drupalsupport@xecurify.com';
    const DRUPAL_LOGS_PATH = '/admin/reports/dblog?type[]=user_provisioning';

    // API calls to login.xecurify.com and Register/Login tab
    const BASE_URL = 'https://login.xecurify.com';
    const GET_TIMESTAMP = self::BASE_URL . '/moas/rest/mobile/get-timestamp';
    const MO_NOTIFY_SEND = self::BASE_URL . '/moas/api/notify/send';
    const CONTACT_US = self::BASE_URL . '/moas/rest/customer/contact-us';
    const CHECK_CUSTOMER_EXISTENCE = self::BASE_URL . '/moas/rest/customer/check-if-exists';
    const CHECK_CUSTOMER_KEY = self::BASE_URL . '/moas/rest/customer/key';
    const SEND_OTP = self::BASE_URL.'/moas/api/auth/challenge';
    const VALIDATE_OTP = self::BASE_URL.'/moas/api/auth/validate';
    const CREATE_CUSTOMER = self::BASE_URL.'/moas/rest/customer/add';
    const TRANSACTION_LIMIT_EXCEEDED = "TRANSACTION_LIMIT_EXCEEDED";
    const API_CALL_FAILED = "FAILED";
    const SUCCESS = "SUCCESS";
    const CUSTOMER_NOT_FOUND = "CUSTOMER_NOT_FOUND";
    const TEMP_EMAIL = 'INVALID_EMAIL_QUICK_EMAIL';

    //Sub-tab names
    const SCIM_SERVER_TAB_NAME = 'scim_server';
    const PROVIDER_SPECIFIC_PROVISIONING_TAB_NAME = 'provider_specific_provisioning';

    //SCIM CLIENT constants
    const USER_SCHEMAS = 'urn:ietf:params:scim:schemas:core:2.0:User';
    const SCIM_PROTOCOL_VERSION = '2.0';
    const AWS_SSO = 'aws_sso';
    const WORDPRESS = 'wordpress';
    const Drupal = 'drupal';
    const JOOMLA = 'joomla';
    const CUSTOM_APP = 'custom_app';

    // Database table names
    const USER_PROVISIONING_TABLE = 'mo_user_provisioning_users';
    const AUDIT_LOG_TABLE = 'mo_user_provisioning_audits_and_logs';

    //Audits and Logs tab constants
    const OPERATION_TYPES = array('any' => '-Any-', 'read' => 'Read', 'create' => 'Create', 'delete' => 'Delete', 'update' => 'Update');
    const STATUS_TYPES = array('any' => '-Any-', 'success' => 'Success', 'failed' => 'Failed');

    //status names and code
    const STATUS_CONFLICT = 409;
    const STATUS_SUCCESS = 200;

    //Handling conflict
    const SCIM_NO_CONFLICT = 0;
    const SCIM_CONFLICT = 1;
    const SCIM_CONFLICT_UNDETERMINED = 2;

    //application names
    const DEFAULT_APP = 'default_scim';

    //setup guide links
    const WORDPRESS_GUIDE = 'https://www.drupal.org/docs/contributed-modules/user-sync-provisioning-in-drupal/sync-provision-drupal-to-other-applications/wordpress-as-scim-server';
    const AWS_SSO_GUIDE = 'https://www.drupal.org/docs/contributed-modules/user-sync-provisioning-in-drupal/sync-provision-drupal-to-other-applications/aws-as-scim-server';
    const JOOMLA_GUIDE = 'https://www.drupal.org/docs/contributed-modules/user-sync-provisioning-in-drupal/sync-provision-drupal-to-other-applications/joomla-as-scim-server';
    const DRUPAL_GUIDE = 'https://www.drupal.org/docs/contributed-modules/user-sync-provisioning-in-drupal/sync-provision-drupal-to-other-applications/user-provisioning-between-two-drupal-websites';
    const CUSTOM_APP_GUIDE = 'https://www.drupal.org/docs/contributed-modules/user-sync-provisioning-in-drupal/sync-provision-drupal-to-other-applications';
}
