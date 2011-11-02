<?php
/**
 * acEmail allows you to send email
 *
 * @package    acExceptionNotifier
 * @subpackage email
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acEmailNotifier {
    
    public static function exceptionEmailNotifier($message) {
        $from = array(sfConfig::get('app_ac_exception_notifier_email_from') => sfConfig::get('app_ac_exception_notifier_email_from_name'));
        $to = array(sfConfig::get('app_ac_exception_notifier_email_to'));
        $subject = sfConfig::get('app_ac_exception_notifier_email_subject');
        $body = self::getBodyFromPartial('exception_email_notifier', array('message' => $message));
        $email = self::getMailer()->compose($from, $to, $subject, $body)->setContentType('text/html');

        return self::getMailer()->send($email);
    }
    
    public static function exceptionAttachedEmailNotifier($message) {
        $from = array(sfConfig::get('app_ac_exception_notifier_email_from') => sfConfig::get('app_ac_exception_notifier_email_from_name'));
        $to = array(sfConfig::get('app_ac_exception_notifier_email_to'));
        $subject = sfConfig::get('app_ac_exception_notifier_email_subject');
        $body = self::getBodyFromPartial('exception_attached_email_notifier', array());
        $email = self::getMailer()->compose($from, $to, $subject, $body)->attach(new Swift_Attachment($message, 'exception-traces.html', 'text/html'))->setContentType('text/html');

        return self::getMailer()->send($email);
    }

    protected static function getMailer() {
        return sfContext::getInstance()->getMailer();
    }

    protected static function getBodyFromPartial($partial, $vars = null) {
        return sfContext::getInstance()->getController()->getAction('acEmailNotifier', 'main')->getPartial('acEmailNotifier/' . $partial, $vars);
    }

}