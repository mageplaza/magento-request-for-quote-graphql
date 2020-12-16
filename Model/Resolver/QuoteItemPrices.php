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
use Magento\Quote\Model\Cart\Totals;
use Mageplaza\RequestForQuote\Model\Quote;
use Mageplaza\RequestForQuote\Model\Quote\Item;
use Magento\Quote\Model\Quote\TotalsCollector;

/**
 * @inheritdoc
 */
class QuoteItemPrices implements ResolverInterface
{
    /**
     * @var TotalsCollector
     */
    private $totalsCollector;

    /**
     * @var Totals
     */
    private $totals;

    /**
     * @var \Mageplaza\RequestForQuote\Model\CartQuote
     */
    private $cartQuote;

    /**
     * @param TotalsCollector $totalsCollector
     */
    public function __construct(
        TotalsCollector $totalsCollector,
        \Mageplaza\RequestForQuote\Model\CartQuote $cartQuote
    ) {
        $this->totalsCollector = $totalsCollector;
        $this->cartQuote = $cartQuote;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Item $quoteItem */
        $quoteItem = $value['model'];
        $quoteItem->load($quoteItem->getId());

        if (!$this->totals) {
            // The totals calculation is based on quote address.
            // But the totals should be calculated even if no address is set
            /** @var Quote $quote */
            $quote = $quoteItem->getQuote();
            $this->cartQuote->collectQuoteById($quote->getId());
            $this->totals = $quote->getTotals();
        }
        $currencyCode = $quoteItem->getQuote()->getQuoteCurrencyCode();

        return [
            'price' => [
                'currency' => $currencyCode,
                'value' => $quoteItem->getPrice(),
            ],
            'row_total' => [
                'currency' => $currencyCode,
                'value' => $quoteItem->getRowTotal(),
            ],
            'row_total_including_tax' => [
                'currency' => $currencyCode,
                'value' => $quoteItem->getRowTotalInclTax(),
            ],
            'total_item_discount' => [
                'currency' => $currencyCode,
                'value' => $quoteItem->getDiscountAmount(),
            ],
            'discounts' => $this->getDiscountValues($quoteItem, $currencyCode)
        ];
    }

    /**
     * Get Discount Values
     *
     * @param Item $cartItem
     * @param string $currencyCode
     * @return array
     */
    private function getDiscountValues($cartItem, $currencyCode)
    {
        $itemDiscounts = $cartItem->getExtensionAttributes()->getDiscounts();
        if ($itemDiscounts) {
            $discountValues=[];
            foreach ($itemDiscounts as $value) {
                $discount = [];
                $amount = [];
                /* @var \Magento\SalesRule\Api\Data\DiscountDataInterface $discountData */
                $discountData = $value->getDiscountData();
                $discountAmount = $discountData->getAmount();
                $discount['label'] = $value->getRuleLabel() ?: __('Discount');
                $amount['value'] = $discountAmount;
                $amount['currency'] = $currencyCode;
                $discount['amount'] = $amount;
                $discountValues[] = $discount;
            }
            return $discountValues;
        }
        return null;
    }
}
