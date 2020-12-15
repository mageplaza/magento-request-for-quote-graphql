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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

/**
 * @inheritdoc
 */
class QuoteItemTypeResolver implements TypeResolverInterface
{
    /**
     * @var array
     */
    private $supportedTypes = [];

    /**
     * @param array $supportedTypes
     */
    public function __construct(array $supportedTypes = [])
    {
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * @inheritdoc
     */
    public function resolveType(array $data) : string
    {
        if (!isset($data['product'])) {
            throw new LocalizedException(__('Missing key "product" in cart data'));
        }
        $productData = $data['product'];

        if (!isset($productData['type_id'])) {
            throw new LocalizedException(__('Missing key "type_id" in product data'));
        }
        $productTypeId = $productData['type_id'];

        if (!isset($this->supportedTypes[$productTypeId])) {
            throw new LocalizedException(
                __('Product "%product_type" type is not supported', ['product_type' => $productTypeId])
            );
        }
        return $this->supportedTypes[$productTypeId];
    }
}
