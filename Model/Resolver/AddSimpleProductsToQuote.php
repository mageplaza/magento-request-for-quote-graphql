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

namespace Mageplaza\RequestForQuoteGraphQl\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\RequestForQuote\Model\Api\QuoteRepository;
use Mageplaza\RequestForQuote\Model\CartQuote;
use Mageplaza\RequestForQuoteGraphQl\Model\Quote\AddProductsToQuote;
use Magento\Framework\Exception\AuthorizationException;

/**
 * Class AddSimpleProductsToQuote
 * @package Mageplaza\RequestForQuoteGraphQl\Model\Resolver
 */
class AddSimpleProductsToQuote implements ResolverInterface
{

    /**
     * @var AddProductsToQuote
     */
    private $addProductsToQuote;

    /**
     * @var GetCustomer
     */
    private $getCustomer;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var CartQuote
     */
    private $cartQuote;

    /**
     * AddSimpleProductsToQuote constructor.
     *
     * @param AddProductsToQuote $addProductsToQuote
     * @param GetCustomer $getCustomer
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        AddProductsToQuote $addProductsToQuote,
        GetCustomer $getCustomer,
        QuoteRepository $quoteRepository,
        CartQuote $cartQuote
    ) {
        $this->addProductsToQuote = $addProductsToQuote;
        $this->getCustomer        = $getCustomer;
        $this->quoteRepository    = $quoteRepository;
        $this->cartQuote = $cartQuote;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new AuthorizationException(__('The current customer isn\'t authorized.'));
        }

        if (empty($args['input']['cart_items'])
            || !is_array($args['input']['cart_items'])
        ) {
            throw new GraphQlInputException(__('Required parameter "cart_items" is missing'));
        }
        $cartItems  = $args['input']['cart_items'];
        $customer   = $this->getCustomer->execute($context);
        $customerId = $customer->getId();
        $this->addProductsToQuote->execute($customerId, $cartItems);
        $quote = $this->quoteRepository->getInactiveQuoteCart($customerId);
        $this->cartQuote->collectQuoteById($quote->getId());

        return [
            'quote' => array_merge($quote->getData(), [
                'model' => $quote
            ])
        ];
    }
}
