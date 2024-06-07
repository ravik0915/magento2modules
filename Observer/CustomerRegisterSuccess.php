<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace STechnology\CustomerAttribute\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\CustomerRepositoryInterface;

class CustomerRegisterSuccess implements ObserverInterface
{
    /** 
     * @var CustomerRepositoryInterface 
     */
    protected $customerRepository;

    /** 
     * @var \Psr\Log\LoggerInterface  
     */
    protected $logger;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->logger = $logger;
    }

    /**
     * Manages redirect
     */
    public function execute(Observer $observer)
    {
        $accountController = $observer->getAccountController();
        $customer = $observer->getCustomer();

        $request = $accountController->getRequest();
        $companyName = $request->getParam('company_name');
        $phoneNumber = $request->getParam('phone_number');
        $customerAddress = $request->getParam('customer_address');
        $userType = $request->getParam('user_type');

        try {
            $customer->setCustomAttribute('company_name', $companyName);
            $customer->setCustomAttribute('phone_number', $phoneNumber);
            $customer->setCustomAttribute('customer_address', $customerAddress);
            $customer->setCustomAttribute('user_type', $userType);
            
            /* save custom customer attributes  */
            $this->customerRepository->save($customer);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
