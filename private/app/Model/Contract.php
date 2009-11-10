<?php
	class Model_Contract extends Mvc_Model_Abstract 
	{
		private $storage;
		
		public function __construct () {
			parent::__construct ();
			
			$this->storage = new Storage ('../private/contracts');
		}
		
		public static function create() {
            return new self();
        }

		public function addContract ($originalFileInfo,$appId)
		{
			$filename = $this->storage->storeFile ($originalFileInfo['tmp_name']);
			
            $sql = '
                UPDATE applications a
                SET contract_filename = :new_contract_filename
                WHERE app_id = :app_id
            ';
			
            $values = array(
                ':app_id'     			 => $appId,
                ':new_contract_filename' => $filename
            );
            
            $stmt = $this->prepare($sql);
            $stmt->execute($values);
            $row_count = $stmt->rowCount();
            return $row_count > 0;
		}

        public function sendContractByMail ($params)
		{
			require_once 'Zend/Mail.php';
			$mail = new Zend_Mail('utf-8');

            if (!empty($params['bodyText'])) 
			{
				$mail->setBodyText($params['bodyText']);				
			}
            if (!empty($params['bodyHtml'])) $mail->setBodyHtml($params['bodyHtml']);
            $mail->setFrom($params['fromEmail'], $params['fromName']);
            $mail->addTo($params['toEmail'], $params['toName']);
            $mail->setSubject($params['subject']);
            if (!empty($params['attachFile']) && file_exists($params['attachFile']))
			{
				$attachment = $mail->createAttachment(file_get_contents($params['attachFile']));
                $attachment->type        = $params['attachType'];
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->filename    = $params['attachName'];
            }
            $mail->send();			
		}
	}
?>