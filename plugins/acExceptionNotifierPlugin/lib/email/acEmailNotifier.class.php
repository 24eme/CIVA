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
    	$emailInformations = sfConfig::get('app_ac_exception_notifier_email');
        $from = array($emailInformations['from'] => $emailInformations['from_name']);
        $to = $emailInformations['to'];
        $subject = $emailInformations['subject'];
        $email = self::getMailer()->compose($from, $to, $subject, $message)->setContentType('text/html');
        return self::getMailer()->send($email);
    }
    
    public static function exceptionAttachedEmailNotifier($message) {
        $emailInformations = sfConfig::get('app_ac_exception_notifier_email');
        $from = array($emailInformations['from'] => $emailInformations['from_name']);
        $to = $emailInformations['to'];
        $subject = $emailInformations['subject'];
        $email = self::getMailer()->compose($from, $to, $subject, '<p>Exception traces attached</p>')->attach(new Swift_Attachment($message, 'exception-traces.html', 'text/html'))->setContentType('text/html');

        return self::getMailer()->send($email);
    }

    protected static function getMailer() {
        return sfContext::getInstance()->getMailer();
    }

}