<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Calculator\UndefinedShippingMethodException;

/**
 * Delegating calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingCalculator extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface $registry
     */
    function let($registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\DelegatingCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface');
    }

    function it_should_extend_base_Sylius_shipping_calculator()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\Calculator');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_complain_if_shipment_has_no_method_defined($shipment)
    {
        $shipment->getMethod()->willReturn(null);

        $this
            ->shouldThrow('Sylius\Bundle\ShippingBundle\Calculator\UndefinedShippingMethodException')
            ->duringCalculate($shipment)
        ;
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface        $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface  $method
     * @param Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface $calculator
     */
    function it_should_delegate_calculation_to_a_calculator_defined_on_shipping_method($registry, $shipment, $method, $calculator)
    {
        $shipment->getMethod()->willReturn($method);
        $method->getCalculator()->willReturn('default');

        $registry->getCalculator('default')->willReturn($calculator);
        $calculator->calculate($shipment)->shouldBeCalled()->willReturn(10.00);

        $this->calculate($shipment)->shouldReturn(10.00);
    }
}
