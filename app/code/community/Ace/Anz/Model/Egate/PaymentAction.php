<?php
/**
 * Ace ANZ eGate Extension
 *
 *
 * @category   Ace
 * @package    Ace_Anz
 */

class Ace_Anz_Model_Egate_PaymentAction
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => 'authorize_capture',
				'label' => 'Authorise and Capture'
			),
			array(
				'value' => 'authorize',
				'label' => 'Authorise'
			)
		);
	}
}

?>
