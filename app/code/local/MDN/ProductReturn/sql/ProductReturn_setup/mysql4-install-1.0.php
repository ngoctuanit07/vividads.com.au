<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2009 Maison du Logiciel (http://www.maisondulogiciel.com)
 * @author : Olivier ZIMMERMANN
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$installer=$this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

																						
//Create tables
$installer->run("
	
CREATE TABLE  {$this->getTable('rma')} (
 `rma_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `rma_ref` VARCHAR( 25 ) NOT NULL ,
 `rma_created_at` DATETIME NULL ,
 `rma_updated_at` DATETIME NULL ,
 `rma_order_id` INT NOT NULL ,
 `rma_customer_phone` VARCHAR( 25 ) NOT NULL ,
 `rma_address_id` INT NOT NULL ,
 `rma_customer_id` INT NOT NULL ,
 `rma_customer_name` VARCHAR( 50 ) NOT NULL ,
 `rma_customer_email` VARCHAR( 255 ) NOT NULL ,
 `rma_status` VARCHAR( 35 ) NOT NULL ,
 `rma_reason` VARCHAR( 20 ) NOT NULL ,
 `rma_expire_date` DATE NULL ,
 `rma_public_description` TEXT NULL ,
 `rma_private_description` TEXT NULL ,
 `rma_aftersale_description` TEXT NULL ,
 `rma_action_order_id` INT NULL ,
 `rma_carrier` VARCHAR( 25 ) NULL ,
 `rma_shipping_cost` DECIMAL( 8, 2 ) NULL ,
 `rma_technical_cost` DECIMAL( 8, 2 ) NULL ,
 `rma_reception_date` DATE NULL ,
 `rma_return_date` DATE NULL ,
INDEX (  `rma_order_id` ,  `rma_customer_id` )
);

CREATE TABLE  {$this->getTable('rma_products')} (
 `rp_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `rp_rma_id` INT NOT NULL ,
 `rp_product_id` INT NOT NULL ,
 `rp_orderitem_id` INT NOT NULL ,
 `rp_qty` INT NOT NULL ,
 `rp_product_name` VARCHAR( 255 ) NOT NULL ,
 `rp_description` TEXT NULL ,
INDEX (  `rp_rma_id` )
);


insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Nouvelle demande de retour produit',
	'Une nouvelle demande de RMA &agrave; &eacute;t&eacute; demand&eacute;.',
	2,
	'Nouvelle demande de retour produit'
);

insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Retour produit refuse',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Bonjour  {{var customer_name}}</strong>,<br/>
  <br>
  Votre demande de retour mat&eacute;riel vient d\'&ecirc;tre refus&eacute; concernant votre commande {{var order_id}} .
  <p>Nous vous invitons &agrave; vous connecter sur votre <a href=\"{{store url=\"customer/account/\"}}\" >espace client</a>, afin de prendre connaissance de la raison de ce refus.

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Retour produit refuse'
);


insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Retour produit accepte',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Bonjour  {{var customer_name}}</strong>,<br/>
  <br>
  Un Retour Mat&eacute;riel Autoris&eacute; (RMA) num&eacute;ro {{var rma_id}} concernant votre commande {{var order_id}}  a &eacute;t&eacute; cr&eacute;&eacute; sur notre site internet <a href=\"{{store url=\"\"}}\">{{store url=\"\"}}</a> afin de vous permettre de nous retourner la marchandise.
  <p>Nous vous invitons &agrave; vous connecter sur votre <a href=\"{{store url=\"customer/account/\"}}\" >espace client</a>, afin de prendre connaissance des conditions de retour de votre RMA, d\'acc&eacute;der aux documents n&eacute;cessaires et ainsi  suivre son avancement.
  <p><b>ATTENTION !!! Le num&eacute;ro qui vous est founi n\'est valable que jusqu\'au {{var rma_expire_date}}</b>  
  <p>Informations Compl&eacute;mentaires:
<br>Statut de la demande : {{var rma_status}}
</p>

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Retour produit accepte'
);


insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Produit receptionne',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Bonjour  {{var customer_name}}</strong>,<br/>
  <br>
  Nous venons de recevoir votre retour produit.Votre produit va maintenant &ecirc; expertis&eacute; pas nos sp&eacute;cialiste.
 </p>
  <p>Nous vous tiendrons inform&eacute; par mail de l\'avanc&eacute;e de l expertise.</p>

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Produit receptionne'
);






insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Produit receptionne mais refuse',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Bonjour  {{var customer_name}}</strong>,<br/>
  </p>
  Nous venons de recevoir votre produit , mais nous venons de le refuser.
  <p>Nous vous invitons &agrave; vous connecter sur votre <a href=\"{{store url=\"customer/account/\"}}\" >espace client</a>, afin de prendre connaissance de la raison de ce refus.</p>

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Produit receptionne mais refuse'
);




insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Produit en expertise',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Bonjour  {{var customer_name}}</strong>,<br/>
  </p>
    <p>Votre produit est en cours d\'expertise, nous vous tiendrons inform&eacute; par mail de l\'&eacute;volution.</p>

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Produit en expertise'
);



insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'New request product return',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
 							New request send for product return.
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'New request product return'
);


insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Product return refused',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Hello {{var customer_name}}</strong>,<br/>
  </p>
  Your reqyest for product return is refused for your order n# {{var order_id}} .
  <p>We invite you to connect on your  <a href=\"{{store url=\"customer/account/\"}}\" > customer account </a>,  to learn about the reason for the refusal.

 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Product return refused'
);



insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Product return accepted',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Hello {{var customer_name}}</strong>,<br/>
  </p>
    An Authorized Return Material number {{var rma_id}} on your command {{var order_id}}  has been created on our website <a href=\"{{store url=\"\"}}\">{{store url=\"\"}}</a> so you can return product..
  <p> We invite you to connect your <a href=\"{{store url=\"customer/account/\"}}\" >customer account</a>,  to note the conditions of your return product access to documents and necessary and follow his progress.</p>
  <p><b>ATTENTION! The number is only valid until  {{var rma_expire_date}}</b></p>
  <p>Additional Information:
<br>Reason for return request : {{var rma_reason}}
<br>Description : {{var rma_description}}
</p>
 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Product return accepted'
);




insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Product received',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Hello {{var customer_name}}</strong>,<br/>
   <br>
 We have just received your return produit.Your product will now be expertise by our specialist.
 </p>
  <p>We will notify you by mail of the progress of expertise<./p>
 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Product received'
);




insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Product received but refused',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Hello {{var customer_name}}</strong>,<br/>
   <br>
 We have just received your return produit, but we just refused it.</p>

  <p>We invite you to connect your <a href=\"{{store url=\"customer/account/\"}}\" >customer account</a>, to be aware of the reason to the refusal.
 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Product received but refused'
);



insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'product expertise',
	'<style type=\"text/css\">body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }</style>

<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
        <tr>
            <td align=\"center\" valign=\"top\">
                <!-- [ header starts here] -->
                <div align=\"left\">
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td valign=\"top\" align=\"center\">
                              <p align=\"right\"><a href=\"{{store url=\"\"}}\" style=\"color:#1E7EC8;\"><img src=\"{{skin url=\"images/logo_email.gif\" _area=\'frontend\'}}\" alt=\"{{var store_name}}\" border=\"0\"/></a></p></td>
                      </tr>
                  </table>
                  <!-- [ middle starts here] -->
                  <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
                      <tr>
                          <td align=\"left\">
  <br>&nbsp;
  <p><strong>Hello {{var customer_name}}</strong>,<br/>
   <br>
   Your product is being expertise, we will notify you by mail of evolution.</p>
 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'product expertise'
);


INSERT INTO  {$this->getTable('cms_block')} 
(`block_id`, `title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES 
(NULL, 'cgv_rma', 'cgv_rma','Please fill Product return commitment', NOW(), NOW(), '1');

INSERT INTO {$this->getTable('cms_block_store')} (`block_id` ,`store_id`)
SELECT `block_id`,'0' FROM {$this->getTable('cms_block')} WHERE identifier = 'cgv_rma';
");

																																											
$installer->endSetup();

