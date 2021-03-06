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
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Mageplaza\RequestForQuote\Model\Quote\Item as QuoteItem;

/**
 * @inheritdoc
 */
class QuoteItems implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $cart = $value['model'];

        $itemsData = [];
        foreach ($cart->getAllVisibleItems() as $cartItem) {
            /**
             * @var QuoteItem $cartItem
             */
            $productData          = $cartItem->getProduct()->getData();
            $productData['model'] = $cartItem->getProduct();

            $itemsData[] = $this->mergeData(
                $cartItem->getData(),
                [
                    'id'       => $cartItem->getItemId(),
                    'quantity' => $cartItem->getQty(),
                    'product'  => $productData,
                    'model'    => $cartItem,
                ]
            );
        }

        return $itemsData;
    }

    /**
     * @param array $array1
     * @param $array2
     *
     * @return array
     */
    protected function mergeData($array1, $array2)
    {
        return array_merge($array1, $array2);
    }
}
