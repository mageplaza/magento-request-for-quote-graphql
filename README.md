# Magento Request a Quote GraphQl

**Magento 2 Request For Quote GraphQL is now a part of the Mageplaza Request For Quote extension that adds GraphQL features. This supports PWA Studio.** 

[Mageplaza Request For Quote for Magento 2](https://www.mageplaza.com/magento-2-request-for-quote/) enables you to create an easy and fast way for customers to negotiate the prices of products on your website. You will see there are more benefits than drawbacks that this functionality will bring to your business. It first reduces the cart abandonment rate due to the dissatisfaction of customers with the products’ prices and that they have no choice or right to discuss to lower the price. With Mageplaza Request For Quote will give you peace of mind about this issue. This is extremely useful to wholesale buyers who usually buy in bulk from your store. 

With this extension, you can add a “Add to quote” icon next to each product so that your customer can add any products to the quote cart. In this quote cart, you can view the cart details and add the quote price that you are willing to pay for the products added to your cart. It’s easy to modify the quote cart before submitting it to update the quoted price that’s more reasonable to help you have more chances to win the negotiation. With one simple click, customers can add all the items to the quote cart to request new prices for multiple items at the same time. They can add items to the cart as usual and add the quoted price later. 

The extension enables you to display the “Add to quote” cart on multiple pages, such as category page, product page, or shopping cart page. Customers can request a quote from any page, making it convenient for them instead of going back and forth to different pages to update their quote price or add a new one. 

Another excellent feature of this extension is that it allows requesting quotes for a massive number of items at once by using SKUs. Your wholesale customers will love this feature as they can quote all the items at a lightning-fast speed that saves much time. This definitely increases customer satisfaction and user experience in your store. 

Customers can delete or cancel their quotes at any time, while you have flexible options to approve or reject their quotes as well. You can also suggest a new price and modify customers’ quotes to create a better discussion about the price with them. The extension ensures that you and your customers can communicate with each other smoothly to come to a final reasonable price for the products. Accordingly, it offers your customers a comment box right under the quote cart. You can then view the quote cart from the backend and reply to the customers’ requests without difficulty. 

Mageplaza Request For Quote will create an exceptional customer experience by adding a super-duper functionality in your Magento 2 store.

## 1. How to install

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-request-for-quote-graphql
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

**Note:**
Age Verification GraphQL requires installing [Mageplaza Request a Quote](https://www.mageplaza.com/magento-2-request-for-quote/) in your Magento installation.

## 2. How to use

To perform GraphQL queries in Magento, please do the following requirements:

- Use Magento 2.3.x or higher. Set your site to [developer mode](https://www.mageplaza.com/devdocs/enable-disable-developer-mode-magento-2.html).
- Set GraphQL endpoint as `http://<magento2-server>/graphql` in url box, click **Set endpoint**.
  (e.g. `http://dev.site.com/graphql`)
- To view the queries that the **Mageplaza Age Verification GraphQL** extension supports, you can look in `Docs > Query` in the right corner

## 3. Devdocs

- [Magento 2 Request For Quote Rest API & examples](https://documenter.getpostman.com/view/10589000/T17Na4ek?version=latest)
- [Magento 2 Request For Quote GraphQL & examples](https://documenter.getpostman.com/view/10589000/TVspmpoT)

## 4. Contribute to this module

Feel free to **Fork** and contribute to this module. 
You can create a pull request so we will merge your changes main branch.

## 5. Get Support

- Don't hesitate to [contact us](https://www.mageplaza.com/contact.html) if you have any further questions. Our support team is   always willing to help. 
- If you find this project helpful, please give us a **Star** ![star](https://i.imgur.com/S8e0ctO.png)
