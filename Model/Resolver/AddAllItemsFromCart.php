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

use Exception;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\QuoteGraphQl\Model\Cart\CreateEmptyCartForCustomer;
use Mageplaza\RequestForQuote\Model\Api\QuoteRepository;

/**
 * Class AddAllItemsFromCart
 * @package Mageplaza\RequestForQuoteGraphQl\Model\Resolver
 */
class AddAllItemsFromCart implements ResolverInterface
{
    /**
     * @var CreateEmptyCartForCustomer
     */
    private $createEmptyCartForCustomer;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * AddAllItemsFromCart constructor.
     *
     * @param CreateEmptyCartForCustomer $createEmptyCartForCustomer
     * @param CartManagementInterface $cartManagement
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        CreateEmptyCartForCustomer $createEmptyCartForCustomer,
        CartManagementInterface $cartManagement,
        QuoteRepository $quoteRepository
    ) {
        $this->createEmptyCartForCustomer = $createEmptyCartForCustomer;
        $this->cartManagement             = $cartManagement;
        $this->quoteRepository            = $quoteRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $currentUserId = $context->getUserId();

        /** @var ContextInterface $context */
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new AuthorizationException(__('The request is allowed for logged in customer'));
        }
        try {
            $cart = $this->cartManagement->getCartForCustomer($currentUserId);
        } catch (NoSuchEntityException $e) {
            $this->createEmptyCartForCustomer->execute($currentUserId, null);
            $cart = $this->cartManagement->getCartForCustomer($currentUserId);
        }

        try {
            $this->quoteRepository->addAllItemsFromCart($currentUserId, $cart->getId());
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
