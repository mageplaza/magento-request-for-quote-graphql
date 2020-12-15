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
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\RequestForQuote\Model\Api\QuoteRepository;
use Mageplaza\RequestForQuote\Model\Api\ReplyRepository;

/**
 * Class GetConversation
 * @package Mageplaza\RequestForQuoteGraphQl\Model\Resolver
 */
class GetConversation implements ResolverInterface
{

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ReplyRepository
     */
    private $replyRepository;

    /**
     * GetList constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        ReplyRepository $replyRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->quoteRepository       = $quoteRepository;
        $this->replyRepository = $replyRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }

        $searchCriteria = $this->searchCriteriaBuilder->build('conversation', $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        try {
            $searchResult = $this->replyRepository->getConversation($currentUserId, $args['quote_id'], $searchCriteria);
            $pageInfo     = $this->getPageInfo($searchResult, $searchCriteria, $args);
            $items        = [];
            foreach ($searchResult->getItems() as $item) {
                $items[$item->getId()]          = $item->getData();
                $items[$item->getId()]['model'] = $item;
            }

            return [
                'total_count' => $searchResult->getTotalCount(),
                'items'       => $items,
                'page_info'   => $pageInfo
            ];
        } catch (Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }
    }

    /**
     * @param $searchResult
     * @param $searchCriteria
     * @param $args
     *
     * @return array
     * @throws GraphQlInputException
     */
    public function getPageInfo($searchResult, $searchCriteria, $args): array
    {
        //possible division by 0
        if ($searchCriteria->getPageSize()) {
            $maxPages = ceil($searchResult->getTotalCount() / $searchCriteria->getPageSize());
        } else {
            $maxPages = 0;
        }

        $currentPage = $searchCriteria->getCurrentPage();
        if ($searchCriteria->getCurrentPage() > $maxPages && $searchResult->getTotalCount() > 0) {
            throw new GraphQlInputException(
                __(
                    'currentPage value %1 specified is greater than the %2 page(s) available.',
                    [$currentPage, $maxPages]
                )
            );
        }

        return [
            'page_size'       => $args['pageSize'],
            'current_page'    => $args['currentPage'],
            'hasNextPage'     => $currentPage < $maxPages,
            'hasPreviousPage' => $currentPage > 1,
            'startPage'       => 1,
            'total_pages'     => $maxPages,
        ];
    }
}
