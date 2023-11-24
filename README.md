# BeSelf Code Assessment


# Beself_CustomerB2b
This is a Magento 2.4.5 module that creates custom attributes for Customer and Customer Group for B2B purposes

## Author
* **César Hernández** - [cesarhndev@gmail.com](mailto:cesarhndev@gmail.com)

### Prerequisites
Before testing this module, make sure that you meet the requirements.

```
A Magento 2.4.* working
```

## INSTALLATION
### Manual Installation
* Extract files from ChernandezBeselfCustomerB2b.zip archive
* Go to your Magento root folder
* Move files into Magento2 folder `app/code/`.


## ENABLE EXTENSION
* Make sure you have correct read/write permissions on your Magento root directory.
  Read about them [here](https://experienceleague.adobe.com/docs/commerce-operations/configuration-guide/deployment/file-system-permissions.html?lang=en).
* Go to Magento root folder

###  Enable Extension Using Magento CLI
Execute the following commands to manually install Chernandez_MiPago
   ```sh
  bin/magento module:status
   ``` 
You must see Chernandez_MiPago in the list of disabled modules.

- Enable module
   ```sh
   bin/magento module:enable Beself_CustomerB2b
   ```
- Launch the upgrade to install the attributes and regenerate the code dependencies:
   ```sh
   bin/magento setup:upgrade &&
   bin/magento setup:di:compile
   ```

- If you have your Magento running on Production mode you must regenerate your static content:
  ```sh
    bin/magento setup:static-content:deploy
  ```
- Clean the cache
   ```sh
   bin/magento cache:clean
   ```

### Configurations

Inside Magento backend go to:
```
Customers > Customer Group
```
In this page you can see the Customer Group grid. Here you can play changing the Is Distributor value for any Customer group. Then log in into the frontend side with a customer assigned to this group and check if everything is working as expected


### CODE DECISIONS

I have decided to use extension_attributes for Customer Group. Because Customer Group is not an EAV Entity we have developed the insert and the get from the database manually in their proper plugins (before get and after save).
There is a new feature in Magento 2.4.5 regarding Customer Groups and Excluded websites. I've removed it as I've seen the screenshot is not showing that feature.
I've create a CustomerB2bRepository to handle customer information regarding this functionality. This could be extended easily in the future for new B2B features
Because of the way the Magento Core is built regarding Customer Group editing I've been force to override Edit Form and Save controller into this module to make the required changes.