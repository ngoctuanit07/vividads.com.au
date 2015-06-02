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

insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'En attente fournisseur',
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
  Le traitement de votre RMA poursuit son cours. Nous sommes actuellemetn en attente d\'informations de la part du fournisseur.
  <p>Nous vous tenons au courant de l\'avancement.</p>

<p>Cordialement,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'En attente fournisseur'
);


insert into {$this->getTable('core_email_template')} 
(template_code, template_text, template_type, template_subject)
values
(
	'Waiting for supplier',
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
  <p><strong>Dear  {{var customer_name}}</strong>,<br/>
  </p>
  We are currently waiting for supplier information for your RMA.
  <p>We will let you know when we ll have more information.</p>

<p>Best regards,<br/><strong>{{var store_name}}</strong></p>
                          </td>
                      </tr>
                  </table>
          </div></td>
        </tr>
    </table>
</div>',
	2,
	'Waiting for supplier'
);

");

																																											
$installer->endSetup();

