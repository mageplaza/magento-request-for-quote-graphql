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
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\RequestForQuote\Model\Api\ReplyRepository;

/**
 * Class SaveReply
 * @package Mageplaza\RequestForQuoteGraphQl\Model\Resolver
 */
class SaveReply implements ResolverInterface
{
    /**
     * @var ReplyRepository
     */
    private $replyRepository;

    /**
     * SaveReply constructor.
     *
     * @param ReplyRepository $replyRepository
     */
    public function __construct(
        ReplyRepository $replyRepository
    ) {
        $this->replyRepository = $replyRepository;
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

        $quoteId = $args['quote_id'] ?? null;
        $content = $args['content'] ?? null;
        $files = $args['files'] ?? null;

        try {
            return $this->replyRepository->save($currentUserId, $quoteId, $content, $files);
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }
}
