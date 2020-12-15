<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_RequestForQuoteGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

declare(strict_types=1);

namespace Mageplaza\RequestForQuoteGraphQl\Model\Quote;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Mageplaza\RequestForQuoteGraphQl\Model\Resolver\AddSimpleProductsToQuote;

/**
 * Class AddProductsToQuote
 * @package Mageplaza\RequestForQuoteGraphQl\Model\Quote
 */
class AddProductsToQuote
{
    /**
     * @var AddSimpleProductsToQuote
     */
    private $addProductToQuote;

    /**
     * AddProductsToQuote constructor.
     *
     * @param AddSimpleProductToQuote $addProductToQuote
     */
    public function __construct(
        AddSimpleProductToQuote $addProductToQuote
    ) {
        $this->addProductToQuote = $addProductToQuote;
    }

    /**
     * @param $customerId
     * @param array $cartItems
     *
     * @throws GraphQlInputException
     */
    public function execute($customerId, array $cartItems): void
    {
        foreach ($cartItems as $cartItemData) {
            $this->addProductToQuote->execute($customerId, $cartItemData);
        }
    }
}
