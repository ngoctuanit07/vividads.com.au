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
 
//Ajoute les emails template
$installer->run("
 
insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Retour produit termine',
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
  Votre demande de retour mat&eacute;riel concernant votre commande {{var order_id}} est d&eacute;sormais termin&eacute;e et a donn&eacute; lieux &agrave; un <b>{{var rma_action}}</b>.

  <p>Nous vous invitons &agrave; vous connecter sur votre <a href=\"{{store url=\"customer/account/\"}}\" >espace client</a>, afin d\'obtenir plus d\'informations.

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Retour produit termine'
);



insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Product return complete',
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
  Your product return  for order #{{var order_id}} is now complete and generated a <b>{{var rma_action}}</b>.
  <p>We invite you to connect on your  <a href=\"{{store url=\"customer/account/\"}}\" > customer account </a>, for more information.

 <p>Best regard,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Product return complete'
);

");
 
$installer->endSetup();
