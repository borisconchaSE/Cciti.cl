<?php

namespace Intouch\Framework\MailHelper;

use Exception;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Mail {

    public static ?MailConfig $MailConfig = null;

    public function __construct(
        public Message $Message,
        public array   $Attachments = []
    ) {

    }

    public function Send() {

        if (!isset(self::$MailConfig)) {
            return false;
        }

        $mail = new PHPMailer(true);

        try {

            //Server settings
            //
            $mail->isSMTP();
            $mail->Host       = self::$MailConfig->SmtpServer;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::$MailConfig->SmtpUser;
            $mail->Password   = self::$MailConfig->SmtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::$MailConfig->SmtpPort;

            //Recipients
            //
            foreach ($this->Message->Recipients as $recipient) {
                if ($recipient instanceof Recipient) {

                    switch($recipient->Type) {
                        case RecipientTypeEnum::FROM_RECIPIENT:
                            $mail->setFrom($recipient->Address, $recipient->Name);
                            break;
                        case RecipientTypeEnum::REPLY_TO_RECIPIENT:
                            $mail->addReplyTo($recipient->Address, $recipient->Name);
                            break;
                        case RecipientTypeEnum::TO_RECIPIENT:
                            $mail->addAddress($recipient->Address, $recipient->Name);
                            break;
                        case RecipientTypeEnum::CC_RECIPIENT:
                            $mail->addCC($recipient->Address, $recipient->Name);
                            break;
                        case RecipientTypeEnum::BCC_RECIPIENT:
                            $mail->addBCC($recipient->Address, $recipient->Name);
                    }
                    
                }
            }

            //Content
            //
            $mail->isHTML($this->Message->IsHtml);
            $mail->Subject = $this->Message->Subject;
            $mail->Body    = $this->Message->Body;
            $mail->AltBody = $this->Message->AltBody;

            // Attachments
            //
            if (isset($this->Attachments)) {
                foreach($this->Attachments as $attachment) {
                    if ($attachment instanceof Attachment) {                        
                        $mail->addAttachment(
                            path: $attachment->FilePath, 
                            name: $attachment->FileName, 
                            disposition: isset($attachment->AttachmentType) ? $attachment->AttachmentType : 'attachment'
                        );
                    }
                }
            }

            $mail->send();

            return true;
        } 
        catch (Exception $e) {
            return false;
        }
    }

    public static function SendMultiple(
        Message $Message,
        array $Replacements
    ) {

        $enviosCorrectos   = [];
        $enviosIncorrectos = [];
        $cantidadEnviosCorrectos    = 0;
        $cantidadEnviosIncorrectos  = 0;

        // Obtener el subject y body originales con los tokens sin reemplazar
        $originalBody       = $Message->Body;
        $originalAltBody    = $Message->AltBody;
        $originalSubject    = $Message->Subject;

        // Componer correo para cada recipiente
        //
        $cantRep = count($Replacements);
        $idxRep = 1;
        foreach($Replacements as $rep) {

            if ($rep instanceof ComposeReplacement && $rep->Recipient->Type == RecipientTypeEnum::TO_RECIPIENT) {
                
                // Reemplazar los tokens
                $newBody    = $originalBody;
                $newAltBody = $originalAltBody;
                $newSubject = $originalSubject;

                // Generar un nuevo mensaje
                foreach ($rep->Tokens as $token) {
                    if (is_array($token) && count($token) == 2) { //los tokens deben ser arreglos [buscar, reemplazar]
                        $newBody    = str_replace($token[0], $token[1], $newBody);
                        $newAltBody = str_replace($token[0], $token[1], $newAltBody);
                        $newSubject = str_replace($token[0], $token[1], $newSubject);
                    }
                }

                $newRecipients = [];
                array_push($newRecipients, $rep->Recipient);
                foreach($Message->Recipients as $recip) {
                    array_push($newRecipients, $recip);
                }

                $nuevoMail = new Mail(
                    Message: new Message(
                        Recipients: $newRecipients,
                        IsHtml: $Message->IsHtml,
                        Subject: $newSubject,
                        Body: $newBody,
                        AltBody: $newAltBody
                    ),
                    Attachments: (isset($rep->Attachments) && is_array($rep->Attachments)) ? $rep->Attachments : []
                );

                try {

                    $sendResult = $nuevoMail->Send();

                    if ($sendResult) {
                        $cantidadEnviosCorrectos++;
                        array_push($enviosCorrectos, $rep->Recipient);
                    }
                    else {
                        $cantidadEnviosIncorrectos++;
                        array_push($enviosIncorrectos, $rep->Recipient);
                    }
                }
                catch (\Exception $ex) {
                    $cantidadEnviosIncorrectos++;
                    array_push($enviosIncorrectos, $rep->Recipient);
                }

                // Esperar 1 segundo para el siguiente envio si hay más correos que enviar
                // 
                if ($idxRep < $cantRep)
                    sleep(1);

                $idxRep++;
            }
        }

        $result = new ResultMultipleSend(
            EnviosCorrectos    : $enviosCorrectos,
            EnviosIncorrectos  : $enviosIncorrectos,
            CantidadEnviosCorrectos    : $cantidadEnviosCorrectos,
            CantidadEnviosIncorrectos  : $cantidadEnviosIncorrectos
        );

        return $result;
    }

}

